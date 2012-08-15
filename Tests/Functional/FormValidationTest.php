<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\Functional;

/**
 * @author    Vitaliy Demidov  <zend@i.ua>
 * @since     15 Aug 2012
 */
class FormValidationTest extends BaseTestCase
{
    public function testSimpleFormAction()
    {
        $client = $this->createClient(array('config' => 'config.yml'));
        $client->insulate();

        $crawler = $client->request('GET', '/simple-form');

        $this->assertEquals(4, $crawler->filter('form input')->count(), "Number of input fields does not match.");
        $this->assertNotEmpty($crawler->filter('script')->count(), "Validation script has not generated.");

        $scriptSrc = $crawler->filter('script')->first()->attr('src');

        $this->assertEquals('/bundles/jsformvalidation/js/simple_form.js', $scriptSrc, "Script url does not match.");

        $asseticWriteTo = $this->getKernel()->getCacheDir() . "/../web";

        if (file_exists($asseticWriteTo . $scriptSrc)) {
            $script = file_get_contents($asseticWriteTo . $scriptSrc);
            $this->assertRegExp('/var[\s]+jsfv[\s]*=[\s]*new[\s]+function/', $script, "Cannot find jsfv initialization.");
            $this->assertRegExp('/function[\s]+NotBlank\(/', $script, "Cannot find NotBlank validator.");
            $this->assertRegExp('/function[\s]+Regex\(/', $script, "Cannot find Regex validator.");
            $this->assertRegExp('/function[\s]+MinLength\(/', $script, "Cannot find MinLength validator.");
            $this->assertRegExp('/function[\s]+Email\(/', $script, "Cannot find Email validator.");

            $this->assertRegExp('/check_form_username\:[\s]*function\(/', $script, "Cannot find username validation.");
            $this->assertRegExp('/checkError\(\'form_username\',[\s]*NotBlank,/', $script, "Cannot find checkError username NotBlank.");
            $this->assertRegExp('/checkError\(\'form_username\',[\s]*Regex,/', $script, "Cannot find checkError username Regex.");

            $this->assertRegExp('/check_form_email\:[\s]*function\(/', $script, "Cannot find email validation.");
            $this->assertRegExp('/checkError\(\'form_email\',[\s]*NotBlank,/', $script, "Cannot find checkError email NotBlank.");
            $this->assertRegExp('/checkError\(\'form_email\',[\s]*Email,/', $script, "Cannot find checkError email Email.");

            $this->assertRegExp('/check_form_password\:[\s]*function\(/', $script, "Cannot find password validation.");
            $this->assertRegExp('/checkError\(\'form_password\',[\s]*MinLength,/', $script, "Cannot find checkError password MinLength.");

        } else {
            $this->assertFalse(true, "Generated javascript does not exist.");
        }
    }

    public function testEntityAnnotationFormAction()
    {
        $client = $this->createClient(array('config' => 'config.yml'));
        $client->insulate();

        $crawler = $client->request('GET', '/entity-annotation-form');

        $this->assertEquals(6, $crawler->filter('form input')->count(), "Number of input fields does not match.");
        $this->assertNotEmpty($crawler->filter('script')->count(), "Validation script has not generated.");

        $scriptSrc = $crawler->filter('script')->first()->attr('src');

        $this->assertEquals('/bundles/jsformvalidation/js/entity_annotation_form.js', $scriptSrc, "Script url does not match.");

        $asseticWriteTo = $this->getKernel()->getCacheDir() . "/../web";

        if (file_exists($asseticWriteTo . $scriptSrc)) {
            $script = file_get_contents($asseticWriteTo . $scriptSrc);
            $this->assertRegExp('/var[\s]+jsfv[\s]*=[\s]*new[\s]+function/', $script, "Cannot find jsfv initialization.");
            $this->assertRegExp('/function[\s]+True\(/', $script, "Cannot find True validator.");
            $this->assertRegExp('/function[\s]+NotBlank\(/', $script, "Cannot find NotBlank validator.");
            $this->assertRegExp('/function[\s]+Regex\(/', $script, "Cannot find Regex validator.");
            $this->assertRegExp('/function[\s]+Min\(/', $script, "Cannot find Min validator.");
            $this->assertRegExp('/function[\s]+Email\(/', $script, "Cannot find Email validator.");
            $this->assertRegExp('/function[\s]+Date\(/', $script, "Cannot find Email validator.");
            $this->assertRegExp('/function[\s]+Repeated\(/', $script, "Cannot find Repeated validator.");
            $this->assertRegExp('/function[\s]+Product_isPasswordLegal[\s]*\(/', $script, "Cannot find Product_isPasswordLegal getter.");

            $this->assertRegExp('/check_form_name\:[\s]*function\(/', $script, "Cannot find name validation.");
            $this->assertRegExp('/checkError\(\'form_name\',[\s]*NotBlank,/', $script, "Cannot find checkError name NotBlank.");
            $this->assertRegExp('/checkError\(\'form_name\',[\s]*Regex,/', $script, "Cannot find checkError name Regex.");

            $this->assertRegExp('/check_form_price\:[\s]*function\(/', $script, "Cannot find price validation.");
            $this->assertRegExp('/checkError\(\'form_price\',[\s]*NotBlank,/', $script, "Cannot find checkError price NotBlank.");
            $this->assertRegExp('/checkError\(\'form_price\',[\s]*Min,/', $script, "Cannot find checkError price Min.");

            $this->assertRegExp('/check_form_purchased\:[\s]*function\(/', $script, "Cannot find purchased validation.");
            $this->assertRegExp('/checkError\(\'form_purchased\',[\s]*NotBlank,/', $script, "Cannot find checkError purchased NotBlank.");
            $this->assertRegExp('/checkError\(\'form_purchased\',[\s]*Date,/', $script, "Cannot find checkError purchased Date.");

            $this->assertRegExp('/check_form_email\:[\s]*function\(/', $script, "Cannot find email validation.");
            $this->assertRegExp('/checkError\(\'form_email\',[\s]*NotBlank,/', $script, "Cannot find checkError email NotBlank.");
            $this->assertRegExp('/checkError\(\'form_email\',[\s]*Email,/', $script, "Cannot find checkError email Email.");

            $this->assertRegExp('/check_form_password_first\:[\s]*function\(/', $script, "Cannot find password first validation.");
            $this->assertRegExp('/check_form_password_second\:[\s]*function\(/', $script, "Cannot find password second validation.");
            $this->assertRegExp('/checkError\(\'form_password_first\',[\s]*NotBlank,/', $script, "Cannot find checkError password NotBlank.");
            $this->assertRegExp('/gv[\s]*=[\s]*Product_isPasswordLegal[\s]*\(\)/', $script, "Cannot find Product_isPasswordLegal caller.");
            $this->assertRegExp('/checkError\(\'form_password_first\',[\s]*True,/', $script, "Cannot find checkError Product_isPasswordLegal getter value True.");
            $this->assertRegExp('/checkError\(\'form_password_second\',[\s]*Repeated,/', $script, "Cannot find checkError password second Repeated.");

        } else {
            $this->assertFalse(true, "Generated javascript does not exist.");
        }
    }
}