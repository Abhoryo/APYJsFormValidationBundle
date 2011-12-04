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
use Symfony\Component\Validator\Mapping\ClassMetadata;

class PreProcessEvent extends Event
{
    private $formView;
    private $metadata;

    public function __construct(FormView $formView, ClassMetadata $metadata)
    {
        $this->formView = $formView;
        $this->metadata = $metadata;
    }

    /**
     * @return FormView 
     */
    public function getFormView()
    {
        return $this->formView;
    }

    /**
     * @return ClassMetadata 
     */
    public function getMetaData()
    {
        return $this->metadata;
    }
}
