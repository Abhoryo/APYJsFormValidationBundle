<?php

namespace APY\JsFormValidationBundle\Tests\Functional\TestBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

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
}