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

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\MinLength;
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
                new MinLength(array('limit' => 8, 'message' => 'This value should contain at least 8 characters.')),
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
}