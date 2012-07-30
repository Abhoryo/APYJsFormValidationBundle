<?php

/**
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Generator;

use APY\JsFormValidationBundle\Generator\GettersLibraries;
use APY\JsFormValidationBundle\Generator\PreProcessEvent;
use APY\JsFormValidationBundle\Generator\FieldsConstraints;
use APY\JsFormValidationBundle\Generator\PostProcessEvent;
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

class FormValidationScriptGenerator
{

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

            $formValidationGroups = $formView->get('validation_groups', array('Default'));

            // Dispatch JsfvEvents::preProcess event
            $dispatcher = $this->container->get('event_dispatcher');
            $preProcessEvent = new PreProcessEvent($formView, $metadata);
            $dispatcher->dispatch(JsfvEvents::preProcess, $preProcessEvent);

            $fieldsConstraints = new FieldsConstraints();

            $aConstraints = array();
            if (!empty($metadata->constraints)) {
                foreach ($metadata->constraints as $constraint) {
                    $constraintName = end((explode(chr(92), get_class($constraint))));
                    if ($constraintName == 'UniqueEntity') {
                        if (is_array($constraint->fields)) {
                            //It has not been implemented yet
                        } else if (is_string($constraint->fields)) {
                            if (!isset($aConstraints[$constraint->fields])) {
                                $aConstraints[$constraint->fields] = array();
                            }
                            $aConstraints[$constraint->fields][] = $constraint;
                        }
                    }
                }
            }

            $gettersLibraries = new GettersLibraries($this->container, $formView);
            $errorMapping = $formView->get('error_mapping');
            $aGetters = array();
            if (!empty($metadata->getters)) {
                foreach ($metadata->getters as $getterMetadata) {
                    /* @var \Symfony\Component\Validator\Mapping\GetterMetadata $getterMetadata */
                    if (!empty($getterMetadata->constraints)) {
                        if ($gettersLibraries->findLibrary($getterMetadata) === null) {
                            // You have to provide getter templates in the following location
                            // {EntityBundle}/Resources/views/Getters/{EntityName}.{GetterMethod}.js.twig
                            // or all templates in one place:
                            // app/Resources/APYJsFormValidationBundle/views/Getters/{EntityName}.{GetterMethod}.js.twig
                            continue;
                        }
                        foreach ($getterMetadata->constraints as $constraint) {
                            /* @var \Symfony\Component\Validator $constraint */
                            $getterName = $getterMetadata->getName();
                            $jsHandlerCallback = $gettersLibraries->getKey($getterMetadata, '_');
                            $constraintName = end((explode(chr(92), get_class($constraint))));
                            $constraintProperties = get_object_vars($constraint);
                            $exist = array_intersect($formValidationGroups, $constraintProperties['groups']);
                            if (!empty($exist)) {
                                if (!$gettersLibraries->has($getterMetadata)) {
                                    $gettersLibraries->add($getterMetadata);
                                }
                                if (!$fieldsConstraints->hasLibrary($constraintName)) {
                                    $librairy = "APYJsFormValidationBundle:Constraints:{$constraintName}Validator.js.twig";
                                    $fieldsConstraints->addLibrary($constraintName, $librairy);
                                }
                                if (!empty($errorMapping[$getterName]) && is_string($errorMapping[$getterName])) {
                                    $fieldName = $errorMapping[$getterName];
                                    //'type' property is set in RepeatedTypeExtension class
                                    if (!empty($formView->children[$fieldName]) &&
                                        $formView->children[$fieldName]->get('type') == 'repeated') {
                                        $repeatedNames = array_keys($formView->children[$fieldName]->get('value'));
                                        //Listen first repeated element
                                        $fieldId = $formView->children[$fieldName]->get('id') . "_" . $repeatedNames[0];
                                    } else {
                                        $fieldId = $formView->children[$fieldName]->get('id');
                                    }
                                } else {
                                    $fieldId = '.';
                                }
                                if (!isset($aGetters[$fieldId][$jsHandlerCallback])) {
                                    $aGetters[$fieldId][$jsHandlerCallback] = array();
                                }

                                unset($constraintProperties['groups']);

                                $aGetters[$fieldId][$jsHandlerCallback][] = array(
                                    'name'       => $constraintName,
                                    'parameters' => json_encode($constraintProperties),
                                );
                            }
                        }
                    }
                }
            }

            // we look through each field of the form
            foreach ($formView->children as $formField) {
                // we look for constraints for the field
                if (isset($metadata->properties[$formField->get('name')])) {
                    // we look through each field constraint
                    $constraintList = $metadata->properties[$formField->get('name')]->getConstraints();

                    //Adds entity level constraints that have been provided for this field
                    if (!empty($aConstraints[$formField->get('name')])) {
                        $constraintList = array_merge($constraintList, $aConstraints[$formField->get('name')]);
                    }

                    foreach ($constraintList as $constraint) {
                        $constraintName = end((explode(chr(92), get_class($constraint))));
                        $constraintProperties = get_object_vars($constraint);

                        // Groups are no longer needed
                        unset($constraintProperties['groups']);
                        if (isset($constraintProperties['em'])) unset($constraintProperties['em']);

                        if (!$fieldsConstraints->hasLibrary($constraintName)) {
                            $librairy = "APYJsFormValidationBundle:Constraints:{$constraintName}Validator.js.twig";
                            $fieldsConstraints->addLibrary($constraintName, $librairy);
                        }

                        $constraintParameters = array();
                        foreach ($constraintProperties as $variable => $value) {
                            if (is_array($value)) {
                                $value = json_encode($value);
                            } else {
                                // regex
                                if (stristr('pattern', $variable) === false) {
                                    $value = json_encode($value);
                                }
                            }

                            $constraintParameters[] = "$variable:$value";
                        }

                        $fieldsConstraints->addFieldConstraint($formField->get('id'), array(
                            'name'       => $constraintName,
                            'parameters' => '{' . join(', ', $constraintParameters) . '}'
                        ));
                    }
                }
            }

            // Dispatch JsfvEvents::postProcess event
            $postProcessEvent = new PostProcessEvent($formView, $fieldsConstraints);
            $dispatcher->dispatch(JsfvEvents::postProcess, $postProcessEvent);

            // Retrieve validation mode from configuration
            $check_modes = array('submit' => false, 'blur' => false);
            switch ($this->container->getParameter('apy_js_form_validation.check_mode')) {
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
            $template = $this->container->get('templating')->render(
                "{$validation_bundle}:Frameworks:JsFormValidation.js.{$javascript_framework}.twig",
                array(
                    'formName'           => $formView->get('name'),
                    'fieldConstraints'   => $fieldsConstraints->getFieldsConstraints(),
                    'librairyCalls'      => $fieldsConstraints->getLibraries(),
                    'check_modes'        => $check_modes,
                    'getterHandlers'     => $gettersLibraries->all(),
                    'gettersConstraints' => $aGetters,
            ));

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
