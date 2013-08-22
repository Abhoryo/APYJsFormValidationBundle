<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\Functional\TestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity which is used for testing purposes
 *
 * @author Alexandr Sharamko <alexandr.sharamko@gmail.com>
 */
class Profile
{
    /**
     * @Assert\NotBlank(message = "Name should not be blank.")
     * @Assert\Regex(pattern="/^[a-z \-]+$/i", message = "Name should contain only letters.")
     */
    protected $name;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

}