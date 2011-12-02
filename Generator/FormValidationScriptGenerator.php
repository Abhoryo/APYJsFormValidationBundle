<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Generator;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Constraints;

use Assetic\AssetWriter;
use Assetic\AssetManager;
use Assetic\Factory\AssetFactory;
use Assetic\Factory\LazyAssetManager;
use Assetic\Filter\Yui\JsCompressorFilter;
use Assetic\FilterManager;
use Assetic\Asset\AssetCollection;

class FormValidationScriptGenerator {
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function generate(FormView $formView, $overwrite = false)
    {  
        // retrieve parameters from the form
        $entityName = get_class($formView->get('value'));
        $formName = $formView->get('name');
        $formValidationGroups = $formView->get('validation_groups', array('Default'));
        $formFields = array_keys($formView->getChildren());

        // Prepare output file
        $scriptPath = $this->container->getParameter('apy_js_form_validation.script_directory');
        $scriptRealPath = $this->container->getParameter('assetic.write_to').$scriptPath;

        if ( ! is_dir($scriptRealPath) ) {
            mkdir($scriptRealPath, 0777, true);
        }

        $route = $this->container->get('request')->get('_route');
        $scriptFile = strtolower($route).".js";

        if ( $overwrite || false === file_exists($scriptRealPath.$scriptFile) ) {

            $metadata = new ClassMetadata($entityName);

            // annotations constraints
            $annotationloader = new AnnotationLoader(new AnnotationReader());
            $annotationloader->loadClassMetadata($metadata);

            // php constraints
            // $entity = new $entityName();
            // $entity->loadValidatorMetadata($metadata);

            // yml constraints

            // xml constraints

            $librairyCalls = array();
            $javascriptCalls = array();
            $constraints = array();

            // we look through each field of the form
            foreach ($formFields as $fieldName) {
                // we look for constraints for the field
                if (in_array($fieldName, array_keys($metadata->properties))) {
                    // we look through each field constraint
                    foreach ($metadata->properties[$fieldName]->getConstraints() as $contraint) {

                        $contraintName = end((explode(chr(92), get_class($contraint))));
                        $contraintParameters = get_object_vars($contraint);

                        // Check validation groups
                        foreach ($contraintParameters['groups'] as $validationGroup) {
                            if (in_array($validationGroup, $formValidationGroups)) {
                                // Groups are no longer needed
                                unset($contraintParameters['groups']);

                                $librairies = "APYJsFormValidationBundle:Constraints:{$contraintName}Validator.js.twig";

                                if (!isset($librairyCalls[$contraintName])) {
                                    $librairyCalls[$contraintName] = $librairies;
                                }

                                $javascriptConstraintParameters = array();
                                foreach ($contraintParameters as $variable => $value) {
                                    if (is_array($value)) {
                                        $value = json_encode($value);
                                    }
                                    else {
                                        // regex
                                        if (stristr('pattern', $variable) === false) {
                                            $value = "'".$value."'";
                                        }
                                    }

                                    $javascriptConstraintParameters[] = "$variable:$value";
                                }

                                $javascriptConstraintParameters = '{'.join(', ',$javascriptConstraintParameters).'}';

                                $constraints[$formName."_".$fieldName][] = array(
                                    'name' => $contraintName,
                                    'parameters' => $javascriptConstraintParameters
                                );

                                break;
                            }
                        }
                    }
                }
            }

            // Retrieve validation mode from configuration
            $check_modes = array('submit' => false, 'blur' => false);
            switch($this->container->getParameter('apy_js_form_validation.check_mode')) {
                default:
                case 'submit':
                    $check_modes['submit'] = true;
                    break;
                case 'blur':
                    $check_modes['blur'] = true;
                    break;
                case 'both':
                    $check_modes = array('submit' => true, 'blur' => true);
                    break;
            }

            // Render the validation script
            $validation_bundle = $this->container->getParameter('apy_js_form_validation.validation_bundle');
            $template = $this->container->get('templating')->render($validation_bundle.'::JsFormValidation.js.twig',
                array(
                    'formName'=>$formName,
                    'fieldConstraints'=>$constraints,
                    'librairyCalls'=>$librairyCalls,
                    'check_modes'=>$check_modes
                    )
            );

            // Create asset and compress it
            $asset = new AssetCollection();
            $asset->setContent($template);
            $asset->setTargetPath($scriptRealPath.$scriptFile);

            // Js compression
            if ($this->container->getParameter('apy_js_form_validation.yui_js')) {
                $yui = new JsCompressorFilter($this->container->getParameter('assetic.filter.yui_js.jar'), $this->container->getParameter('assetic.java.bin'));
                $yui->filterDump($asset);
            }

            if (false === @file_put_contents($asset->getTargetPath(), $asset->getContent())) {
                throw new \RuntimeException('Unable to write file '.$asset->getTargetPath());
            }
        }

        return $scriptPath.$scriptFile;
    }
}