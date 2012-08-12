<?php

/**
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Form\Extension;

use APY\JsFormValidationBundle\Generator\FormValidationScriptGenerator;
use APY\JsFormValidationBundle\EventListener\AddIdentifierSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * @var FormValidationScriptGenerator
     */
    protected $jsfv;

    /**
     * Sets javascript form validation script generator service.
     *
     * @param   FormValidationScriptGenerator  $jsfv
     */
    public function setJsfv(FormValidationScriptGenerator $jsfv)
    {
        $this->jsfv = $jsfv;
    }

    /**
     * Gets javascript form validation script generator service.
     *
     * @return FormValidationScriptGenerator Returns javascript form validation script generator service
     */
    public function getJsfv()
    {
        return $this->jsfv;
    }

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
     * @see Symfony\Component\Form.AbstractTypeExtension::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new AddIdentifierSubscriber($builder->getFormFactory(), $this->getJsfv());
        $builder->addEventSubscriber($subscriber);
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
        // Adds constraints to the view. It comes from simple forms
        if ($form->getConfig()->hasOption('constraints')) {
            $view->set('constraints', $form->getConfig()->getOption('constraints'));
        }
        $view->set('error_mapping', $form->getConfig()->getOption('error_mapping'));
    }
}
