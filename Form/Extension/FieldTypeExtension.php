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

class FieldTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'field';
    }

    public function buildView(FormView $view, FormInterface $form)
    {
        // Add validation groups to the view
        if ($form->hasAttribute('validation_groups')) {
            $view->set('validation_groups' , $form->getAttribute('validation_groups'));
        }
    }
}
