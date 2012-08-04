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

class FieldsConstraints
{
    /**
     * Constraints
     * @var array
     */
    public $constraints;

    /**
     * Constraint libararies
     * @var array
     */
    public $libraries;

    /**
     * Gets all constraints
     *
     * @return array Returns all constraints
     */
    public function getFieldsConstraints()
    {
        return $this->constraints;
    }

    /**
     * Checks, whether current field has constraints.
     *
     * @param    string     $fieldName
     * @return   boolean    Returns true if field has constraints or false otherwise
     */
    public function hasFieldConstraints($fieldName)
    {
        return !empty($this->constraints[$fieldName]);
    }

    /**
     * Gets cosntraints of specific field
     *
     * @param    array    $fieldName  Returns cosntraints of specific field
     */
    public function getFieldConstraints($fieldName)
    {
        return $this->constraints[$fieldName];
    }

    /**
     * Sets field constraints
     *
     * @param     string     $fieldName     Field name
     * @param     array      $constraints   Array of constraints
     * @return    FieldsConstraints
     */
    public function setFieldConstraints($fieldName, $constraints)
    {
        $this->constraints[$fieldName] = $constraints;

        return $this;
    }

    /**
     * Adds field cosntraint
     *
     * @param     string    $fieldName    Field name
     * @param     string    $constraint   Constraint name
     * @return    FieldsConstraints
     */
    public function addFieldConstraint($fieldName, $constraint)
    {
        $this->constraints[$fieldName][] = $constraint;

        return $this;
    }

    /**
     * Gets the libraries
     *
     * @return array Returns array of the libraries
     */
    public function getLibraries()
    {
        return $this->libraries;
    }

    /**
     * Adds a library of the specific constraint
     *
     * @param     string    $contraintName    Constraint name
     * @param     string    $libraryScript    Library script
     * @return    FieldsConstraints
     */
    public function addLibrary($contraintName, $libraryScript)
    {
        $this->libraries[$contraintName] = $libraryScript;

        return $this;
    }

    /**
     * Checks, whether constraint library has been added or not.
     *
     * @param     string    $contraintName
     * @return    boolean   Returns true if library has been added or false otherwise
     */
    public function hasLibrary($contraintName)
    {
        return !empty($this->libraries[$contraintName]);
    }
}
