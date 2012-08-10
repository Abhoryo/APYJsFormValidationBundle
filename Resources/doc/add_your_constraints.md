Custom constraints
==================

## Method 1. Implement Constraint class

You can create a custom constraint by extending the base constraint class, Constraint.
In the official symfony documentation described the way how to create a simple validator
that checks if a string contains only alphanumeric characters.

Read more [How to create a Custom Validation Constraint](http://symfony.com/doc/master/cookbook/validation/custom_constraint.html)

* You need to create a ContainsAlphanumeric class that extends `Symfony\Component\Validator\Constraint`
and implement validatedBy() method

* You need to create a ContainsAlphanumericValidator class that extends `Symfony\Component\Validator\ConstraintValidator`
and implement validate() method

* You should apply you constraint to entity field using any: yaml, annotation, xml or php method.

> **Note**: One additional step, that you need to do, is to create javascript representation of the constraint and place it
> into `app/Resources/APYJsFormValidationBundle/views/Constraints/ContainsAlphanumericValidator.js.twig`.

## Method 2. Constraint which is based on getter method of Entity class

If you want to use constraints which are based on method of the entity class and represents javascript 
implementation of you php method, you need to follow the naming conventions. 

Assume, you have an Entity `FooUserBundle:User` with constraint which is based on `isPasswordLegal` method

```php
<?php
//src/Foo/UserBundle/Entity/User.php
namespace Foo/UserBundle/Entity
class User
{
	//...
	
	 /**
     * Checks if password is not equal username
     *
     * @Assert\True(groups={message = "Password should not match username.")
     */
    public function isPasswordLegal ()
    {
        return ((empty($this->password) || empty($this->username)) || 
            ($this->username != $this->password)) ? true : false;
    }
}
```

Here you have an entity with name `User` and method with name `isPasswordLegal`. 
If your want this constraint to be working on client side you have to create javascript handler for
this method which will implement the same logic and return the same value. The name of the template
should be `{EntityName}`.`MethodName`.js.twig and this file should be located in the `Getter` folder. 

```js
//src/Foo/UserBundle/Resources/views/Getters/User.isPasswordLegal.js.twig
//or this path can be overridden if you want have all getters in one place
//app/Resources/APYJsFormValidationBundle/views/Getters/User.isPasswordLegal.js.twig
function {{ name|raw }} () {
    var field_password = $("#{{ form.password.first.vars.id|e('js') }}");
    var field_username = $("#{{ form.username.vars.id|e('js') }}");
    var password_val = field_password.val(), username_val = field_username.val();

    return (password_val == '' || username_val == '' || username_val != password_val) ? true : false;
};
```

In your case file name is `User.isPasswordLegal.js.twig`. Within the template you can access two basic variable
* `name` - the name of the function that represents you getter method.
* `form` - FormView. You can use field id to access the form field value on client side.

That is all. If you run your application you will find your constraint is validated on client side.
But what if you do not want that error message appears at the top of the form? There are good news since 
symfony 2.1. See [Form Goodness in Symfony 2.1] (http://symfony.com/blog/form-goodness-in-symfony-2-1)
You need to just add `error_mapping` option into your entity type class.

```php
<?php
// src/Foo/UserBundle/Form/Type/UserType.php
namespace Foo\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class UserType extends AbstractType
{
	//...
	
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
        	//...
            'error_mapping' => array(
                'isPasswordLegal' => 'password',
                //...
            ),
        ));
    }
}
```

In this case error message will appear after field `password`.