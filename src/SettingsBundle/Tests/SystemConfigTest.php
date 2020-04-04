<?php

declare(strict_types=1);

/*
 * This file is part of SolidInvoice project.
 *
 * (c) 2013-2017 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidInvoice\SettingsBundle\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use SolidInvoice\CoreBundle\Test\Traits\DoctrineTestTrait;
use SolidInvoice\SettingsBundle\Entity\Setting;
use SolidInvoice\SettingsBundle\Exception\InvalidSettingException;
use SolidInvoice\SettingsBundle\SystemConfig;

class SystemConfigTest extends TestCase
{
    use DoctrineTestTrait;
    use
        MockeryPHPUnitIntegration;

    public function testGet()
    {
        $config = new SystemConfig($this->em->getRepository(Setting::class));

        $this->assertSame('skin-solidinvoice-default', $config->get('design/system/theme'));
    }

    public function testGetAll()
    {
        $config = new SystemConfig($this->em->getRepository(Setting::class));

        $this->assertSame(
            [
                'design/system/theme' => 'skin-solidinvoice-default',
                'email/format' => 'both',
                'email/from_address' => 'info@solidinvoice.co',
                'email/from_name' => 'SolidInvoice',
                'email/sending_options/encryption' => null,
                'email/sending_options/host' => null,
                'email/sending_options/password' => null,
                'email/sending_options/port' => null,
                'email/sending_options/transport' => null,
                'email/sending_options/user' => null,
                'invoice/bcc_address' => null,
                'invoice/email_subject' => 'New Invoice - #{id}',
                'notification/client_create' => '{"email":true,"sms":false}',
                'notification/invoice_status_update' => '{"email":true,"sms":false}',
                'notification/payment_made' => '{"email":true,"sms":false}',
                'notification/quote_status_update' => '{"email":true,"sms":false}',
                'quote/bcc_address' => null,
                'quote/email_subject' => 'New Quotation - #{id}',
                'sms/twilio/number' => null,
                'sms/twilio/sid' => null,
                'sms/twilio/token' => null,
                'system/company/company_name' => 'SolidInvoice',
                'system/company/contact_details/address' => null,
                'system/company/contact_details/email' => null,
                'system/company/contact_details/phone_number' => null,
                'system/company/currency' => null,
                'system/company/logo' => null,
                'system/company/vat_number' => null,
            ],
            $config->getAll()
        );
    }

    public function testInvalidGet()
    {
        $config = new SystemConfig($this->em->getRepository(Setting::class));

        $this->expectException(InvalidSettingException::class);
        $this->expectExceptionMessage('Invalid settings key: some/invalid/key');

        $config->get('some/invalid/key');
    }
}
