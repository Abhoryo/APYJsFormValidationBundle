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
            'getCountries' => new \Twig_Function_Method($this, 'getCountries'),
            'getLanguages' => new \Twig_Function_Method($this, 'getLanguages'),
            'getLocales' => new \Twig_Function_Method($this, 'getLocales'),
        );
    }

    public function JsFormValidationFunction(FormView $formView, $getScriptPath = false)
    {
        $enabled = $this->container->getParameter('apy_js_form_validation.enabled');

        if ($enabled == true) {
            // Generate the script
            $jsfvGenerator = $this->container->get('jsfv');
            $scriptFile = $jsfvGenerator->generate($formView);

            if ($getScriptPath) {
                return $scriptFile;
            }
            else {
                return sprintf('<script type="text/javascript" src="%s"></script>', $scriptFile);
            }
        }
        else {
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
