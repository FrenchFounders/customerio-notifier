Customer.io Notifier
==================

Provides [Customer.io](https://customer.io/docs/api/app/#operation/sendPush) integration for Symfony Notifier.

Installation
-------

`composer require frenchfounders/customerio-notifier`

Declare the notifier factory as a service :

```
services:
    notifier.transport_factory.customerio:
        class: FrenchFounders\CustomerioNotifier\CustomerioTransportFactory
        parent: 'notifier.transport_factory.abstract'
        tags:
            - { name: texter.transport_factory }
```

DSN example
-----------

```
CUSTOMERIO_DSN=customerio://APP_API_KEY@default?defaultRecipientId=DEFAULT_RECIPIENT_ID
```

where:
 - `APP_API_KEY` is your CustomerIo application API key
 - `DEFAULT_RECIPIENT_ID` is an optional default recipient


Resources
---------

 * Based on [OneSignal Notifier](https://github.com/symfony/symfony/tree/6.4/src/Symfony/Component/Notifier/Bridge/OneSignal)