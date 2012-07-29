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

class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     *
     * @see Symfony\Component\Form.FormTypeExtensionInterface::getExtendedType()
     */
    public function getExtendedType()
    {
        return 'form';
    }

    /**
     * {@inheritdoc}
     *
     * @see Symfony\Component\Form.AbstractTypeExtension::buildView()
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Add validation groups to the view
        if ($form->getConfig()->hasAttribute('validation_groups')) {
            $view->set('validation_groups', $form->getConfig()->getAttribute('validation_groups'));
        }
        $view->set('error_mapping', $form->getConfig()->getOption('error_mapping'));
    }
}
