<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\Functional\TestBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Product Entity FormType which is used for testing purposes
 *
 * @author Vitaliy Demidov   <zend@i.ua>
 * @since  16 Aug 2012
 */
class ProductType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('price', 'money')
            ->add('purchased', 'date')
            ->add('email', 'email')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => "Passwords must match.",
            ))
            ->add('excluded', 'datetime', array(
                'mapped' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     *
     * @see Symfony\Component\Form.AbstractType::setDefaultOptions()
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'APY\JsFormValidationBundle\Tests\Functional\TestBundle\Entity\Product',
            'error_mapping' => array(
                'isPasswordLegal' => 'password',
            ),
        ));
    }

    /**
     * {@inheritdoc}
     *
     * @see Symfony\Component\Form.FormTypeInterface::getName()
     */
    public function getName()
    {
        return 'product';
    }
}