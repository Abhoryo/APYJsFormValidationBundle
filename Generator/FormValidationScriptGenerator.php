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

use APY\JsFormValidationBundle\JsfvEvents;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

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
        // Prepare output file
        $scriptPath = $this->container->getParameter('apy_js_form_validation.script_directory');
        $scriptRealPath = $this->container->getParameter('assetic.write_to').'/'.$scriptPath;
        $scriptFile = strtolower($this->container->get('request')->get('_route')).".js";

        if ( $overwrite || false === file_exists($scriptRealPath.$scriptFile) ) {
            // Retrieve entityName from the form
            $entityName = get_class($formView->get('value'));
            
            // Load metadata
            $metadata = new ClassMetadata($entityName);

            // from annotations
            $annotationloader = new AnnotationLoader(new AnnotationReader());
            $annotationloader->loadClassMetadata($metadata);

            // from php
            // $entity = new $entityName();
            // $entity->loadValidatorMetadata($metadata);

            // from yml

            // from xml

            // Dispatch JsfvEvents::preProcess event
            $dispatcher = $this->container->get('event_dispatcher');
            $preProcessEvent = new PreProcessEvent($formView, $metadata);
            $dispatcher->dispatch(JsfvEvents::preProcess, $preProcessEvent);

            $fieldsConstraints = new FieldsConstraints();

            // we look through each field of the form
            foreach ($formView->getChildren() as $formField) {
                // we look for constraints for the field
                if (isset($metadata->properties[$formField->get('name')])) {
                    // we look through each field constraint
                    foreach ($metadata->properties[$formField->get('name')]->getConstraints() as $contraint) {

                        $contraintName = end((explode(chr(92), get_class($contraint))));
                        $contraintProperties = get_object_vars($contraint);

                        // Groups are no longer needed
                        unset($contraintProperties['groups']);

                        if (!$fieldsConstraints->hasLibrary($contraintName)) {
                            $librairy = "APYJsFormValidationBundle:Constraints:{$contraintName}Validator.js.twig";
                            $fieldsConstraints->addLibrary($contraintName, $librairy);
                        }

                        $constraintParameters = array();
                        foreach ($contraintProperties as $variable => $value) {
                            if (is_array($value)) {
                                $value = json_encode($value);
                            }
                            else {
                                // regex
                                if (stristr('pattern', $variable) === false) {
                                    $value = "'".$value."'";
                                }
                            }

                            $constraintParameters[] = "$variable:$value";
                        }

                        $fieldsConstraints->addFieldConstraint($formField->get('id'), array(
                            'name' => $contraintName,
                            'parameters' => '{'.join(', ',$constraintParameters).'}'
                        ));
                    }
                }
            }

            // Dispatch JsfvEvents::postProcess event
            $postProcessEvent = new PostProcessEvent($formView, $fieldsConstraints);
            $dispatcher->dispatch(JsfvEvents::postProcess, $postProcessEvent);

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
            $javascript_framework = strtolower($this->container->getParameter('apy_js_form_validation.javascript_framework'));
            $template = $this->container->get('templating')->render("{$validation_bundle}:Frameworks:JsFormValidation.js.{$javascript_framework}.twig",
                array(
                    'formName'=>$formView->get('name'),
                    'fieldConstraints'=>$fieldsConstraints->getFieldsConstraints(),
                    'librairyCalls'=>$fieldsConstraints->getLibraries(),
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

            $this->container->get('filesystem')->mkdir($scriptRealPath);

            if (false === @file_put_contents($asset->getTargetPath(), $asset->getContent())) {
                throw new \RuntimeException('Unable to write file '.$asset->getTargetPath());
            }
        }

        return $this->container->get('templating.helper.assets')->getUrl($scriptPath.$scriptFile);
    }
}
