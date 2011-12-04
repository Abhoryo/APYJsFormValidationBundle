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

class FieldsConstraints {
    public $constraints;
    public $libraries;

    public function getFieldsConstraints() {
        return $this->constraints;
    }

    public function hasFieldConstraints($fieldName) {
        return isset($this->constraints[$fieldName]) && !empty($this->constraints[$fieldName]);
    }

    public function getFieldConstraints($fieldName) {
        return $this->constraints[$fieldName];
    }

    public function setFieldConstraints($fieldName, $constraints) {
        $this->constraints[$fieldName] = $constraints;

        return $this;
    }

    public function addFieldConstraint($fieldName, $constraint) {
        $this->constraints[$fieldName][] = $constraint;

        return $this;
    }

    public function getLibraries() {
        return $this->libraries;
    }

    public function addLibrary($contraintName, $libraryScript) {
        $this->libraries[$contraintName] = $libraryScript;

        return $this;
    }

    public function hasLibrary($contraintName) {
        return isset($this->libraries[$contraintName]) && !empty($this->libraries[$contraintName]);
    }
}
