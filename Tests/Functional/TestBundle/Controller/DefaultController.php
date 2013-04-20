<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\Functional\TestBundle\Controller;

use Symfony\Component\Validator\Constraints\Length;
use APY\JsFormValidationBundle\Tests\Functional\TestBundle\Entity\Product;
use APY\JsFormValidationBundle\Tests\Functional\TestBundle\Entity\User;
use APY\JsFormValidationBundle\Tests\Functional\TestBundle\Form\Type\ProductType;
use APY\JsFormValidationBundle\Tests\Functional\TestBundle\Form\Type\UserType;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author    Vitaliy Demidov   <zend@i.ua>
 * @since     15 Aug 2012
 */
class DefaultController extends Controller
{
    /**
     * @Route("/simple-form", name = "simple_form")
     * @Template
     */
    public function simpleFormAction(Request $request)
    {
        $collectionConstraint = new Collection(array(
            'username' => array(
                new NotBlank(array('message' => 'This value should not be blank.')),
                new Regex(array('pattern' => "/^[a-z_0-9]{3,}$/", 'message' => 'Username should contain valid characters')),
            ),
            'password' => array(
                new Length(array('min' => 8, 'minMessage' => 'This value should contain at least 8 characters.')),
            ),
            'email'    => array(
                new NotBlank(array('message' => 'This value should not be blank.')),
                new Email(array('checkMX' => true, 'message' => 'Please enter valid email address.')),
            ),
        ));
        $formBuilder = $this->createFormBuilder(
                array(),
                array(
                    'constraints' => $collectionConstraint,
                )
            )
            ->add('username', 'text')
            ->add('password', 'password')
            ->add('email', 'email')
        ;
        $form = $formBuilder->getForm();

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/entity-annotation-form", name = "entity_annotation_form")
     * @Template
     */
    public function entityAnnotationFormAction(Request $request)
    {
        $product = new Product();
        $formBuilder = $this->createFormBuilder($product, array(
                'error_mapping' => array(
                    'isPasswordLegal' => 'password',
                )
            ))
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
        $form = $formBuilder->getForm();

        //Creates Form without second argument!
        $form2 = $this->createForm(new ProductType());

        //UniqueEntity
        $form3 = $this->createForm(new UserType(), new User());

        return array(
            'form' => $form->createView(),
            'form2' => $form2->createView(),
            'form3' => $form3->createView(),
        );
    }
}