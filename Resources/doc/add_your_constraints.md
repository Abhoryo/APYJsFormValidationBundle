Custom constraints
==================

## Method 1. Implement Constraint class

You can create a custom constraint by extending the base constraint class, Constraint.
The official symfony documentation describes the way how to create a simple validator
that checks whether string does contain only alphanumeric characters.

Read more [How to create a Custom Validation Constraint](http://symfony.com/doc/master/cookbook/validation/custom_constraint.html)

* You need to create a ContainsAlphanumeric class that extends `Symfony\Component\Validator\Constraint`
and implements validatedBy() method

* You need to create a ContainsAlphanumericValidator class that extends `Symfony\Component\Validator\ConstraintValidator`
and implements validate() method

* You should apply you constraint to entity field using any: yaml, annotation, xml or php method.

> **Note**: One additional step that you need to do, is to create javascript representation of the constraint and place it
> into `app/Resources/APYJsFormValidationBundle/views/Constraints/ContainsAlphanumericValidator.js.twig`.

## Method 2. Constraint which is based on getter method of Entity class

If you want to use constraints which are based on method of the entity class and represent javascript 
implementation of your php method, you need to follow naming conventions described below. 

Assume, you have an Entity `FooUserBundle:User` with constraint which is based on `isPasswordLegal` method

```php
<?php
//src/Foo/UserBundle/Entity/User.php
namespace Foo/UserBundle/Entity
class User
{
    //...
	
    /**
     * Checks if password does not equal username
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
If you want this constraint working on client side you have to create javascript handler for
this method which will implement the same logic and return the same value. The name of the template
should be `{EntityName}`.`MethodName`.js.twig and this file should be located in the `Getter` folder. 

```js
//src/Foo/UserBundle/Resources/views/Getters/User.isPasswordLegal.js.twig
//or this path can be overridden in case you want to have all getters in one place
//app/Resources/APYJsFormValidationBundle/views/Getters/User.isPasswordLegal.js.twig
function {{ name|raw }} () {
    var field_password = $("#{{ form.password.first.vars.id|e('js') }}");
    var field_username = $("#{{ form.username.vars.id|e('js') }}");
    var password_val = field_password.val(), username_val = field_username.val();

    return (password_val == '' || username_val == '' || username_val != password_val) ? true : false;
};
```

In this case, file name is `User.isPasswordLegal.js.twig`. Within the template, you can access two basic variable
* `name` - the name of the function that represents your getter method.
* `form` - FormView. You can use ID of the field to access its value on client side.

That's all. If you run your application you will find your constraint working on client side.
But what if you do not feel good with error message at the top of the form? There are good news since 
symfony 2.1. Look at [Form Goodness in Symfony 2.1] (http://symfony.com/blog/form-goodness-in-symfony-2-1)
You just need to add `error_mapping` option into your entity type class.

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

In this case error message will appear at the bottom of the field `password`.
