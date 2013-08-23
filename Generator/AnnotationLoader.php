<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Generator;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\Validator\Exception\MappingException;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\GroupSequenceProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader as Loader;

class AnnotationLoader extends Loader
{
    /**
     * {@inheritDoc}
     */
    public function loadClassMetadata(ClassMetadata $metadata)
    {
        $reflClass = $metadata->getReflectionClass();
        $className = $reflClass->name;
        $loaded = false;

        foreach ($this->reader->getClassAnnotations($reflClass) as $constraint) {
            if ($constraint instanceof GroupSequence) {
                $metadata->setGroupSequence($constraint->groups);
            } elseif ($constraint instanceof GroupSequenceProvider) {
                $metadata->setGroupSequenceProvider(true);
            } elseif ($constraint instanceof Constraint) {
                $metadata->addConstraint($constraint);
            }

            $loaded = true;
        }

        foreach ($reflClass->getProperties() as $property) {
            foreach ($this->reader->getPropertyAnnotations($property) as $constraint) {
                if ($constraint instanceof Constraint) {
                    $metadata->addPropertyConstraint($property->name, $constraint);
                }

                $loaded = true;
            }
        }

        foreach ($reflClass->getMethods() as $method) {
            foreach ($this->reader->getMethodAnnotations($method) as $constraint) {
                if ($constraint instanceof Constraint) {
                    if (preg_match('/^(get|is)(.+)$/i', $method->name, $matches)) {
                        $metadata->addGetterConstraint(lcfirst($matches[2]), $constraint);
                    } else {
                        throw new MappingException(sprintf('The constraint on "%s::%s" cannot be added. Constraints can only be added on methods beginning with "get" or "is".', $className, $method->name));
                    }
                }

                $loaded = true;
            }
        }

        return $loaded;
    }
}
