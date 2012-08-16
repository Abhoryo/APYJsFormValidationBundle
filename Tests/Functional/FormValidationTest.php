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

        $this->assertEquals('/bundles/jsformvalidation/js/simple_form_form.js', $scriptSrc, "Script url does not match.");

        $asseticWriteTo = $this->getKernel()->getCacheDir() . "/../web";

        foreach (array($scriptSrc) as $src) {
            if (file_exists($asseticWriteTo . $src)) {
                $script = file_get_contents($asseticWriteTo . $src);
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

                unset($script);
            } else {
                $this->assertFalse(true, sprintf("Generated javascript for %s does not exist.", $src));
            }
        }
    }

    public function testEntityAnnotationFormAction()
    {
        $client = $this->createClient(array('config' => 'config.yml'));
        $client->insulate();

        $crawler = $client->request('GET', '/entity-annotation-form');

        $this->assertEquals(12, $crawler->filter('form input')->count(), "Number of input fields does not match.");
        $this->assertEquals(16, $crawler->filter('form select')->count(), "Number of select fields does not match.");
        $this->assertEquals(2, $crawler->filter('script')->count(), "Validation scripts have not been generated.");

        $scriptSrc = $crawler->filter('script')->eq(0)->attr('src');
        $scriptSrc2 = $crawler->filter('script')->eq(1)->attr('src');

        $this->assertEquals('/bundles/jsformvalidation/js/entity_annotation_form_form.js', $scriptSrc);
        $this->assertEquals('/bundles/jsformvalidation/js/entity_annotation_form_product.js', $scriptSrc2);

        $asseticWriteTo = $this->getKernel()->getCacheDir() . "/../web";

        foreach (array($scriptSrc => 'form', $scriptSrc2 => 'product') as $src => $form) {
            if (file_exists($asseticWriteTo . $src)) {
                $script = file_get_contents($asseticWriteTo . $src);

                $this->assertNotEmpty(preg_match('/var[\s]+jsfv[\s]*=[\s]*new[\s]+function/', $script),
                    "Cannot find jsfv initialization in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+True\(/', $script),
                    "Cannot find True validator in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+NotBlank\(/', $script),
                    "Cannot find NotBlank validator in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+Regex\(/', $script),
                    "Cannot find Regex validator in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+Min\(/', $script),
                    "Cannot find Min validator in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+Email\(/', $script),
                    "Cannot find Email validator in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+Date\(/', $script),
                    "Cannot find Email validator in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+Repeated\(/', $script),
                    "Cannot find Repeated validator in $src.");
                $this->assertNotEmpty(preg_match('/function[\s]+Product_isPasswordLegal[\s]*\(/', $script),
                    "Cannot find Product_isPasswordLegal getter in $src.");

                $this->assertNotEmpty(preg_match('/check_' . $form . '_name\:[\s]*function\(/', $script),
                    "Cannot find name validation in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_name\',[\s]*NotBlank,/', $script),
                    "Cannot find checkError name NotBlank in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_name\',[\s]*Regex,/', $script),
                    "Cannot find checkError name Regex in $src.");

                $this->assertNotEmpty(preg_match('/check_' . $form . '_price\:[\s]*function\(/', $script),
                    "Cannot find price validation.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_price\',[\s]*NotBlank,/', $script),
                    "Cannot find checkError price NotBlank in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_price\',[\s]*Min,/', $script),
                    "Cannot find checkError price Min in $src.");

                $this->assertNotEmpty(preg_match('/check_' . $form . '_purchased\:[\s]*function\(/', $script),
                    "Cannot find purchased validation in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_purchased\',[\s]*NotBlank,/', $script),
                    "Cannot find checkError purchased NotBlank in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_purchased\',[\s]*Date,/', $script),
                    "Cannot find checkError purchased Date in $src.");

                $this->assertNotEmpty(preg_match('/check_' . $form . '_email\:[\s]*function\(/', $script),
                    "Cannot find email validation in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_email\',[\s]*NotBlank,/', $script),
                    "Cannot find checkError email NotBlank in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_email\',[\s]*Email,/', $script),
                    "Cannot find checkError email Email in $src.");

                $this->assertNotEmpty(preg_match('/check_' . $form . '_password_first\:[\s]*function\(/', $script),
                    "Cannot find password first validation in $src.");
                $this->assertNotEmpty(preg_match('/check_' . $form . '_password_second\:[\s]*function\(/', $script),
                    "Cannot find password second validation in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_password_first\',[\s]*NotBlank,/', $script),
                    "Cannot find checkError password NotBlank in $src.");
                $this->assertNotEmpty(preg_match('/gv[\s]*=[\s]*Product_isPasswordLegal[\s]*\(\)/', $script),
                    "Cannot find Product_isPasswordLegal caller in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_password_first\',[\s]*True,/', $script),
                    "Cannot find checkError Product_isPasswordLegal getter value True in $src.");
                $this->assertNotEmpty(preg_match('/checkError\(\'' . $form . '_password_second\',[\s]*Repeated,/', $script),
                    "Cannot find checkError password second Repeated in $src.");

                $this->assertEmpty(preg_match('/check_' . $form . '_excluded\:[\s]*function\(/', $script),
                    "Field with property_path = FALSE must be excluded from validation in $src.");

                unset($script);
            } else {
                $this->assertFalse(true, "Generated javascript does not exist.");
            }
        }

        file_put_contents('D:\\Downloads\\error.html', $client->getResponse()->getContent());

    }
}