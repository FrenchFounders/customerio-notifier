<?php

namespace FrenchFounders\CustomerioNotifier;

use Symfony\Component\Notifier\Exception\LogicException;
use Symfony\Component\Notifier\Exception\TransportException;
use Symfony\Component\Notifier\Exception\UnsupportedMessageTypeException;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Olivier Mouren <olivier@frenchfounders.com>
 */
final class CustomerioTransport extends AbstractTransport
{
    protected const HOST = 'api.customer.io';

    private string $appApiKey;
    private ?string $defaultRecipientId;

    public function __construct(#[\SensitiveParameter] string $appApiKey, string $defaultRecipientId = null, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        $this->appApiKey = $appApiKey;
        $this->defaultRecipientId = $defaultRecipientId;

        parent::__construct($client, $dispatcher);
    }

    public function __toString(): string
    {
        if (null === $this->defaultRecipientId) {
            return sprintf('customerio://%s@%s', urlencode($this->appApiKey), $this->getEndpoint());
        }

        return sprintf('customerio://%s@%s?recipientId=%s', urlencode($this->appApiKey), $this->getEndpoint(), $this->defaultRecipientId);
    }

    public function supports(MessageInterface $message): bool
    {
        return $message instanceof PushMessage && (null === $message->getOptions() || $message->getOptions() instanceof CustomerioOptions);
    }

    /**
     * @see https://customer.io/docs/api/app/#operation/sendPush
     */
    protected function doSend(MessageInterface $message): SentMessage
    {
        if (!$message instanceof PushMessage) {
            throw new UnsupportedMessageTypeException(__CLASS__, PushMessage::class, $message);
        }

        if (!($options = $message->getOptions()) && $notification = $message->getNotification()) {
            $options = CustomerioOptions::fromNotification($notification);
        }

        $recipientId = $message->getRecipientId() ?? $this->defaultRecipientId;

        if (null === $recipientId) {
            throw new LogicException(sprintf('The "%s" transport should have configured `defaultRecipientId` via DSN or provided with message options.', __CLASS__));
        }

        $options = $options?->toArray() ?? [];
        $options['identifiers']['id'] = $recipientId;
        $options['title'] ??= $message->getSubject();
        $options['message'] ??= $message->getContent();

        $response = $this->client->request('POST', 'https://'.$this->getEndpoint().'/v1/send/push', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$this->appApiKey,
            ],
            'json' => $options,
        ]);

        try {
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $e) {
            throw new TransportException('Could not reach the remote Customer.io server.', $response, 0, $e);
        }

        if (200 !== $statusCode) {
            throw new TransportException(sprintf('Unable to send the Customer.io push notification: "%s".', $response->getContent(false)), $response);
        }

        $result = $response->toArray(false);

        if (empty($result['delivery_id'])) {
            throw new TransportException(sprintf('Unable to send the Customer.io push notification: "%s".', $response->getContent(false)), $response);
        }

        $sentMessage = new SentMessage($message, (string) $this);
        $sentMessage->setMessageId($result['delivery_id']);

        return $sentMessage;
    }
}
