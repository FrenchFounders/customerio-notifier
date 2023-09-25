<?php

namespace FrenchFounders\CustomerioNotifier\Tests;

use FrenchFounders\CustomerioNotifier\CustomerioOptions;
use PHPUnit\Framework\TestCase;

/**
 * @author Olivier Mouren <olivier@frenchfounders.com>
 */
final class CustomerioOptionsTest extends TestCase
{
    public function testCustomerioOptions()
    {
        $customerIoOptions = (new CustomerioOptions())
            ->title('Title')
            ->message('Message content');

        $this->assertSame([
            'title' => 'Title',
            'message' => 'Message content',
        ], $customerIoOptions->toArray());
    }
}
