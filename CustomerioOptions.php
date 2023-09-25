<?php

namespace FrenchFounders\CustomerioNotifier;

use Symfony\Component\Notifier\Message\MessageOptionsInterface;
use Symfony\Component\Notifier\Notification\Notification;

/**
 * @author Olivier Mouren <olivier@frenchfounders.com>
 */
final class CustomerioOptions implements MessageOptionsInterface
{
    private array $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public static function fromNotification(Notification $notification): static
    {
        $options = new self();
        $options->title($notification->getSubject());
        $options->message($notification->getContent());

        return $options;
    }

    public function title($title): static
    {
        $this->options['title'] = $title;

        return $this;
    }

    public function message($message): static
    {
        $this->options['message'] = $message;

        return $this;
    }

    public function recipient(string $id): static
    {
        $this->options['recipient_id'] = $id;

        return $this;
    }

    public function transactionalMessageId($transactionalMessageId): static
    {
        $this->options['transactional_message_id'] = $transactionalMessageId;

        return $this;
    }

    public function imageUrl($imageUrl): static
    {
        $this->options['image_url'] = $imageUrl;

        return $this;
    }

    public function link($link): static
    {
        $this->options['link'] = $link;

        return $this;
    }

    public function sound($sound): static
    {
        $this->options['sound'] = $sound;

        return $this;
    }

    public function customData($customData): static
    {
        $this->options['custom_data'] = $customData;

        return $this;
    }

    public function customPayload($customPayload): static
    {
        $this->options['custom_payload'] = $customPayload;

        return $this;
    }

    public function language($language): static
    {
        $this->options['language'] = $language;

        return $this;
    }

    public function messageData($messageData): static
    {
        $this->options['message_data'] = $messageData;

        return $this;
    }

    public function sendAt($sendAt): static
    {
        $this->options['sendAt'] = $sendAt;

        return $this;
    }

    public function disableMessageRetention($disableMessageRetention): static
    {
        $this->options['disable_message_retention'] = $disableMessageRetention;

        return $this;
    }

    public function sendToUnsubscribed($sendToUnsubscribed): static
    {
        $this->options['send_to_unsubscribed'] = $sendToUnsubscribed;

        return $this;
    }

    public function queueDraft($queueDraft): static
    {
        $this->options['queue_draft'] = $queueDraft;

        return $this;
    }

    public function setOption($key, $value): static
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function getRecipientId(): ?string
    {
        return $this->options['recipient_id'] ?? null;
    }

    public function toArray(): array
    {
        $options = $this->options;
        unset($options['recipient_id']);

        return $options;
    }
}
