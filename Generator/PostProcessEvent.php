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

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormView;

class PostProcessEvent extends Event
{
    private $formView;
    private $fieldsConstraints;

    public function __construct(FormView $formView, FieldsConstraints $fieldsConstraints)
    {
        $this->formView = $formView;
        $this->fieldsConstraints = $fieldsConstraints;
    }

    /**
     * @return FormView 
     */
    public function getFormView()
    {
        return $this->formView;
    }

    /**
     * @return FieldsConstraints 
     */
    public function getFieldsConstraints()
    {
        return $this->fieldsConstraints;
    }
}
