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
 * @author Vitaliy Demidov   <zend@i.ua>
 * @since  15 Aug 2012
 */
class Product
{
    /**
     * @Assert\NotBlank(message = "Name should not be blank.")
     * @Assert\Regex(pattern="/^[a-z \-]+$/i", message = "Name should contain only letters.")
     */
    protected $name;

    /**
     * @Assert\NotBlank(message = "Price should be provided.")
     * @Assert\Min(limit = 20, message = "")
     */
    protected $price;

    /**
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    protected $purchased;

    /**
     * @Assert\NotBlank(message = "Email should not be blank.")
     * @Assert\Email(checkMX = true, message = "This is not valid email.")
     */
    protected $email;

    /**
     * @Assert\NotBlank(message = "Password should not be blank.")
     */
    protected $password;

    /**
     * This field is used for test property_path FALSE.
     * Validation constraint for it should not appear in the script.
     *
     * @Assert\NotBlank()
     */
    protected $excluded;


    /**
     * @Assert\True(message = "Password should not be the same as name.")
     */
    public function isPasswordLegal()
    {
        return empty($this->name) || empty($this->password) || $this->password != $this->name ? true : false;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function getPurchased()
    {
        return $this->purchased;
    }

    public function setPurchased($purchased)
    {
        $this->purchased = $purchased;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getExcluded()
    {
        return $this->excluded;
    }

    public function setExcluded($excluded)
    {
        $this->excluded = $excluded;
    }

}