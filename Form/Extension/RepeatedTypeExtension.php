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
use Symfony\Component\Form\FormBuilderInterface;

class RepeatedTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'repeated';
    }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        if (!empty($options['invalid_message'])) {
            $builder->setAttribute('invalid_message', $options['invalid_message']);
        }
        if (!empty($options['invalid_message_parameters'])) {
            $builder->setAttribute('invalid_message_parameters', $options['invalid_message_parameters']);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see Symfony\Component\Form.AbstractTypeExtension::buildView()
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['type'] = 'repeated';
        $view->vars['invalid_message'] = $form->getConfig()->getAttribute('invalid_message');
        $view->vars['invalid_message_parameters'] = $form->getConfig()->getAttribute('invalid_message_parameters');
    }
}
