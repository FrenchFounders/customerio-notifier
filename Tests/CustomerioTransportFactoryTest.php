<?php

namespace FrenchFounders\CustomerioNotifier\Tests;

use FrenchFounders\CustomerioNotifier\CustomerioTransportFactory;
use Symfony\Component\Notifier\Test\TransportFactoryTestCase;

/**
 * @author Olivier Mouren <olivier@frenchfounders.com>
 */
final class CustomerioTransportFactoryTest extends TransportFactoryTestCase
{
    public function createFactory(): CustomerioTransportFactory
    {
        return new CustomerioTransportFactory();
    }

    public static function createProvider(): iterable
    {
        yield [
            'customerio://app_api_key@host.test',
        ];
    }

    public static function supportsProvider(): iterable
    {
        yield [true, 'customerio://token@host'];
        yield [false, 'somethingElse://token@host'];
    }

    public static function incompleteDsnProvider(): iterable
    {
        yield 'missing app_api_key' => ['customerio://app_api_key:@host.test'];
    }

    public static function unsupportedSchemeProvider(): iterable
    {
        yield ['somethingElse://token@host'];
    }
}
