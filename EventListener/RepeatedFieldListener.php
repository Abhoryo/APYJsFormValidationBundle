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

use APY\JsFormValidationBundle\Generator\PostProcessEvent;

class RepeatedFieldListener
{
    public function onJsfvPostProcess(PostProcessEvent $event)
    {
        $formFields = $event->getFormView()->children;
        $fieldsConstraints = $event->getFieldsConstraints();

        foreach ($formFields as $formField) {
            if (isset($formField->vars['type']) && $formField->vars['type'] == 'repeated') {
                $formFieldId = $formField->vars['id'];

                // Get the real fields name of the repeated type form
                $repeatedNames = array_keys($formField->vars['value']);
                $formFieldId_first = $formFieldId.'_'.$repeatedNames[0];
                $formFieldId_second = $formFieldId.'_'.$repeatedNames[1];

                // Rename the original field constraints
                if ($fieldsConstraints->hasFieldConstraints($formFieldId)) {
                    $fieldsConstraints->setFieldConstraints($formFieldId_first, $fieldsConstraints->getFieldConstraints($formFieldId));
                    unset($fieldsConstraints->constraints[$formFieldId]);
                }

                // Add a special Repeat constraint for repeated field
                if (!$fieldsConstraints->hasLibrary('Repeated')) {
                    $fieldsConstraints->addLibrary('Repeated', "APYJsFormValidationBundle:Constraints:RepeatedValidator.js.twig");
                }

                // Get invalid message from the form
                $invalid_message = isset($formField->vars['invalid_message']) ? $formField->vars['invalid_message'] : null;
                $invalid_message_parameters = isset($formField->vars['invalid_message_parameters']) ?
                    $formField->vars['invalid_message_parameters'] : null;
                if (!empty($invalid_message_parameters)) {
                    foreach ($invalid_message_parameters as $invalid_message_parameter => $value){
                        $invalid_message = str_replace($invalid_message_parameter,'{{ '.$invalid_message_parameter.' }}',$invalid_message);
                    }
                }
                $invalid_message_parameters = json_encode($invalid_message_parameters);

                $fieldsConstraints->addFieldConstraint($formFieldId_second, array(
                    'name'       => 'Repeated',
                    'parameters' => "{first_name: '$formFieldId_first', second_name: '$formFieldId_second', invalid_message: '$invalid_message', invalid_message_parameters: $invalid_message_parameters}"
                ));
            }
        }
    }
}
