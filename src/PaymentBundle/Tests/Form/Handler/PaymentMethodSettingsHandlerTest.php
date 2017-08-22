<?php

declare(strict_types = 1);

/*
 * This file is part of SolidInvoice project.
 *
 * (c) 2013-2017 Pierre du Plessis <info@customscripts.co.za>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidInvoice\PaymentBundle\Tests\Form\Handler;

use SolidInvoice\CoreBundle\Templating\Template;
use SolidInvoice\FormBundle\Test\FormHandlerTestCase;
use SolidInvoice\PaymentBundle\Entity\PaymentMethod;
use SolidInvoice\PaymentBundle\Factory\PaymentFactories;
use SolidInvoice\PaymentBundle\Form\Handler\PaymentMethodSettingsHandler;
use Mockery as M;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class PaymentMethodSettingsHandlerTest extends FormHandlerTestCase
{
    /**
     * @return string|FormHandlerInterface
     */
    public function getHandler()
    {
        $router = M::mock(RouterInterface::class);
        $router->shouldReceive('generate')
            ->once()
            ->with('_xhr_payments_settings', ['method' => 'test_method'])
            ->andReturn('/payment/test_method');

        $handler = new PaymentMethodSettingsHandler(new PaymentFactories(), $router);
        $handler->setDoctrine($this->registry);

        return $handler;
    }

    protected function getHandlerOptions(): array
    {
        $method = new PaymentMethod();
        $method->setName('Test')
            ->setGatewayName('test_method')
            ->setFactoryName('offline');

        return [
            'payment_method' => $method,
        ];
    }

    public function getFormData(): array
    {
        return [
            'payment_methods' => [
                'name' => 'My Test Payment',
                'enabled' => true,
            ],
        ];
    }

    protected function assertOnSuccess(? Response $response, $data, FormRequest $form)
    {
        /* @var PaymentMethod $data */
        $this->assertSame('My Test Payment', $data->getName());
        $this->assertTrue($data->isEnabled());
        $this->assertCount(1, $this->em->getRepository('SolidInvoicePaymentBundle:PaymentMethod')->findAll());
    }

    protected function assertResponse(FormRequest $formRequest)
    {
        $this->assertInstanceOf(Template::class, $formRequest->getResponse());
    }

    protected function getEntityNamespaces(): array
    {
        return [
            'SolidInvoicePaymentBundle' => 'SolidInvoice\PaymentBundle\Entity',
        ];
    }

    protected function getEntities(): array
    {
        return [
            'SolidInvoicePaymentBundle:PaymentMethod',
        ];
    }
}
