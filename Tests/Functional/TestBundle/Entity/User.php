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

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User Entity which is used for testing purposes
 *
 * @author Vitaliy Demidov   <zend@i.ua>
 * @since  17 Aug 2012
 *
 * @ORM\Table(name="user")
 * @ORM\Entity()
 * @UniqueEntity(fields="username", message="Username you want is already taken.")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="username", type="string", length=128, unique = true)
     */
    public $username;

    /**
     * Gets Id
     *
     * @return int
     */
    public function getId()
    {
        $this->id;
    }

    /**
     * Sets id
     *
     * @param    int    $id
     * @throws   \RuntimeException
     */
    public function setId($id)
    {
        if (!empty($this->id)) {
            throw new \RuntimeException("Only updates allowed.");
        }
        $this->id = $id;
    }

    /**
     * Gets username
     *
     * @return   string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets username
     *
     * @param    string    $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
}