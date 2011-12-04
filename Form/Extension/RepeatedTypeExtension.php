<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class RepeatedTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'repeated';
    }

    public function buildView(FormView $view, FormInterface $form)
    {   
        $view->set('invalid_message', $form->getAttribute('invalid_message'));
        $view->set('invalid_message_parameters', $form->getAttribute('invalid_message_parameters'));
    }
}
