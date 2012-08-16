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
        $dataClass = $builder->getDataClass();
        $subscriber = new AddIdentifierSubscriber($builder->getFormFactory(), $this->getJsfv());
        $builder->addEventSubscriber($subscriber);
        if (!empty($options['validation_groups'])) {
            $builder->setAttribute('validation_groups', $options['validation_groups']);
        }
        if ($dataClass !== null) {
            $builder->setAttribute('data_class', $dataClass);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see Symfony\Component\Form.AbstractTypeExtension::buildView()
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $config = $form->getConfig();
        // Add validation groups to the view
        if ($config->hasAttribute('validation_groups')) {
            $view->set('validation_groups', $config->getAttribute('validation_groups'));
        }
        if ($config->hasAttribute('data_class')) {
            $view->set('data_class', $config->getAttribute('data_class'));
        }
        // Adds constraints to the view. It comes from simple forms
        if ($config->hasOption('constraints')) {
            $view->set('constraints', $config->getOption('constraints'));
        }
        if ($config->hasOption('property_path')) {
            // Fields with property_path = false must be excluded from validation
            $property_path = $config->getOption('property_path');
            if ($property_path === false) {
                $view->set('property_path', false);
            }
        }
        $view->set('error_mapping', $config->getOption('error_mapping'));
    }
}
