<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\EventListener;

use APY\JsFormValidationBundle\Generator\PreProcessEvent;

class ValidationGroupsListener
{
    public function onJsfvPreProcess(PreProcessEvent $event)
    {
        $formView = $event->getFormView();
        $formValidationGroups = !empty($formView->vars['validation_groups']) ?
            $formView->vars['validation_groups'] : array('Default');
        if ($formValidationGroups instanceof \Closure) {
            // !TODO It couldn't be processed in current implementation.
            // No one constraint will be excluded in this case.
            return;
        }
        $formFields = array_keys($formView->children);
        $metadata = $event->getMetaData();
        foreach ($formFields as $fieldName) {
            if (isset($metadata->properties[$fieldName])) {
                foreach ($metadata->properties[$fieldName]->constraints as $key => $contraint) {
                    $contraintParameters = get_object_vars($contraint);
                    // Check validation groups for each contraint of each property
                    foreach ($contraintParameters['groups'] as $validationGroup) {
                        if (in_array($validationGroup, $formValidationGroups)) {
                            continue 2;
                        }
                    }
                    // Unset constraint which is not in the validation groups
                    unset($metadata->properties[$fieldName]->constraints[$key]);
                }
            }
        }
    }
}
