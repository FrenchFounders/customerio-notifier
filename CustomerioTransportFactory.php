<?php

namespace FrenchFounders\CustomerioNotifier;

use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Transport\Dsn;

/**
 * @author Olivier Mouren <olivier@frenchfounders.com>
 */
final class CustomerioTransportFactory extends AbstractTransportFactory
{
    public function create(Dsn $dsn): CustomerioTransport
    {
        if ('customerio' !== $dsn->getScheme()) {
            throw new UnsupportedSchemeException($dsn, 'customerio', $this->getSupportedSchemes());
        }

        $appApiKey = $this->getUser($dsn);
        $defaultRecipientId = $dsn->getOption('defaultRecipientId');
        $host = 'default' === $dsn->getHost() ? null : $dsn->getHost();
        $port = $dsn->getPort();

        return (new CustomerioTransport($appApiKey, $defaultRecipientId, $this->client, $this->dispatcher))->setHost($host)->setPort($port);
    }

    protected function getSupportedSchemes(): array
    {
        return ['customerio'];
    }
}
