<?php

declare(strict_types=1);

/*
 * This file is part of SolidInvoice project.
 *
 * (c) Pierre du Plessis <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidInvoice\ClientBundle\Action\Ajax\Contact;

use SolidInvoice\ClientBundle\Entity\Contact;
use SolidInvoice\CoreBundle\Response\AjaxResponse;
use SolidInvoice\CoreBundle\Traits\DoctrineAwareTrait;
use SolidInvoice\CoreBundle\Traits\JsonTrait;

final class Delete implements AjaxResponse
{
    use DoctrineAwareTrait;
    use JsonTrait;

    public function __invoke(Contact $contact)
    {
        $em = $this->doctrine->getManager();
        $em->remove($contact);
        $em->flush();

        return $this->json([]);
    }
}
