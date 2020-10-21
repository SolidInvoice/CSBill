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

namespace SolidInvoice\QuoteBundle\Tests\Listener;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as M;
use PHPUnit\Framework\TestCase;
use SolidInvoice\CoreBundle\Test\Traits\DoctrineTestTrait;
use SolidInvoice\InvoiceBundle\Entity\Invoice;
use SolidInvoice\InvoiceBundle\Manager\InvoiceManager;
use SolidInvoice\NotificationBundle\Notification\NotificationManager;
use SolidInvoice\QuoteBundle\Entity\Quote;
use SolidInvoice\QuoteBundle\Listener\WorkFlowSubscriber;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\StateMachine;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\WorkflowInterface;

class WorkFlowSubscriberTest extends TestCase
{
    use DoctrineTestTrait;
    use MockeryPHPUnitIntegration;

    public function testOnQuoteAccepted()
    {
        $quote = new Quote();
        $invoice = new Invoice();

        $invoiceManager = M::mock(InvoiceManager::class);

        $invoiceManager->shouldReceive('createFromQuote')
            ->with($quote)
            ->andReturn($invoice);

        $stateMachine = M::mock(StateMachine::class);

        $stateMachine->shouldReceive('apply')
            ->with($invoice, 'new');

        $stateMachine->shouldReceive('apply')
            ->with($invoice, 'accept');

        $notification = M::mock(NotificationManager::class);
        $notification->shouldReceive('sendNotification')
            ->zeroOrMoreTimes();

        $subscriber = new WorkFlowSubscriber($this->registry, $invoiceManager, $stateMachine, $notification);

        $subscriber->onQuoteAccepted(new Event($quote, new Marking(['pending' => 1]), new Transition('archive', 'pending', 'archived'), M::mock(WorkflowInterface::class)));
    }

    public function testOnWorkflowTransitionApplied()
    {
        $quote = (new Quote())->setStatus('pending');

        $invoiceManager = M::mock(InvoiceManager::class);
        $stateMachine = M::mock(StateMachine::class);

        $notification = M::mock(NotificationManager::class);
        $notification->shouldReceive('sendNotification')
            ->zeroOrMoreTimes();

        $subscriber = new WorkFlowSubscriber($this->registry, $invoiceManager, $stateMachine, $notification);

        $subscriber->onWorkflowTransitionApplied(new Event($quote, new Marking(['pending' => 1]), new Transition('archive', 'pending', 'archived'), M::mock(WorkflowInterface::class)));

        static::assertTrue($quote->isArchived());
        static::assertSame($quote, $this->em->getRepository(Quote::class)->find($quote->getId()));
    }
}
