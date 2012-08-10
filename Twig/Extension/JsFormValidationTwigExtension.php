<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Twig\Extension;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use APY\JsFormValidationBundle\Generator\FormValidationScriptGenerator;

class JsFormValidationTwigExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'JSFV' => new \Twig_Function_Method($this, 'JsFormValidationFunction', array('is_safe' => array('all'))),
            'getJsFormElementValue' => new \Twig_Function_Method($this, 'getJsFormElementValue', array('is_safe' => array('all'))),
            'getCountries' => new \Twig_Function_Method($this, 'getCountries'),
            'getLanguages' => new \Twig_Function_Method($this, 'getLanguages'),
            'getLocales' => new \Twig_Function_Method($this, 'getLocales'),
        );
    }

    /**
     * Retrieves validation javascript.
     *
     * @param    FormView  $formView       A Form View.
     * @param    string    $getScriptPath  Whether it should return script url instead of script tag (default: false).
     * @return   string    Returns script tag or script url depending on getScriptPath option.
     * @throws   \RuntimeException
     */
    public function JsFormValidationFunction(FormView $formView, $getScriptPath = false)
    {
        $enabled = $this->container->getParameter('apy_js_form_validation.enabled');

        if ($enabled == true) {
            // Generate the script
            $jsfvGenerator = $this->container->get('jsfv');
            $scriptFile = $jsfvGenerator->generate($formView);

            if ($getScriptPath) {
                return $scriptFile;
            } else {
                return sprintf('<script type="text/javascript" src="%s"></script>', $scriptFile);
            }
        } else {
            // If the bundle is disabled and $getScriptPath is set to true an empty script is generated
            if ($getScriptPath) {
                $asseticPath =  $this->container->getParameter('assetic.write_to');
                $scriptPath = $this->container->getParameter('apy_js_form_validation.script_directory');
                $scriptFile = 'no_jsfv_script.js';
                $scriptRealPath = $asseticPath.'/'.$scriptPath;

                $this->container->get('filesystem')->mkdir($scriptRealPath);

                $filePath = $scriptRealPath.$scriptFile;
                if (false === file_exists($filePath)) {
                    if (false === @file_put_contents($filePath, '// JsFormValidation bundle is disabled')) {
                        throw new \RuntimeException('Unable to write file '.$filePath);
                    }
                }

                return $this->container->get('templating.helper.assets')->getUrl($scriptPath.$scriptFile);
            }
        }
    }

    /**
     * Returns JS language construction which acquire form field value.
     * This function generates code taking into account javascript_framework option
     * of the bundle.
     *
     * @param      string    $formElementObjectName  Variable name that represents form element object.
     * @param      string    $framework              Optional. Used to override default framework.
     * @return     string    Returns javascript code that returns value of the
     */
    public function getJsFormElementValue($formElementObjectName, $framework = null)
    {
        $ret = '';
        $f = $formElementObjectName;
        $framework = $framework ?: $this->container->getParameter('apy_js_form_validation.javascript_framework');
        switch ($framework) {
            case 'dojo':
                $ret = 'dojo.query(' . $f . ').val()';
                break;

            case 'extjs':
                $ret = 'Ext.get(' . $f . ').getValue()';
                break;

            case 'jquery':
            case 'zepto':
                $ret = '$(' . $f . ').val()';
                break;

            case 'mootools':
                $ret = '((' . $f . '.nodeName.toLowerCase() == "select") ? '
                     . '$(' . $f . ').getSelected()[0].value : '
                     . '$(' . $f . ').get("value"))';
                break;

            case 'prototype':
                $ret = '$(' . $f . ').getValue()';
                break;

            case 'yui3':
                $ret = 'Y.one(' . $f . ').get("value")';
                break;

            default:
                throw new \RuntimeException("Unknown framework!");
        }
        return $ret;
    }

    public function getCountries()
    {
        return json_encode(\Symfony\Component\Locale\Locale::getCountries());
    }

    public function getLanguages()
    {
        return json_encode(\Symfony\Component\Locale\Locale::getLanguages());
    }

    public function getLocales()
    {
        return json_encode(\Symfony\Component\Locale\Locale::getLocales());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'JsFormValidation';
    }
}
