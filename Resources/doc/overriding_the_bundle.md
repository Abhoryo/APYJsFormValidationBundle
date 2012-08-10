Overriding the bundle
=====================

## Validation

You can define the bundle of the validation script in configuration.

```yml
# app/config.yml

apy_js_form_validation:
    validation_bundle: MyProjectMyBundle
```

Validation script is defined in a template file so you can also overriding it like a normal template.

`app/Resources/JsFormValidationBundle/views/JsFormValidation.js.twig`

## Field Constraints

The contraints are pure javascript but you can overriding them.
Constraints script are defined in template files so you can overriding them like a normal template.

* `app/Resources/JsFormValidationBundle/views/Constraints/MinValidator.js.twig`
* `app/Resources/JsFormValidationBundle/views/Constraints/NotBlankValidator.js.twig`

## Custom Constraints

The same as previous. If your constraint class name is `YouConstraintValidator` you should
place javascript code for it into
`app/Resources/JsFormValidationBundle/views/Constraints/YouConstraintValidator.js.twig`.

## Getters which is used with constraints

Default location of the javascript handlers based on getters is

* `src/Foo/BundleName/Resources/views/Getters/EntityName.MethodName.js.twig`

It can be overriden by

* `app/Resources/JsFormValidationBundle/views/Getters/EntityName.MethodName.js.twig`

See [How to use your custom constraints based on entity getter method](add_your_constraints.md)

## Controller

You can override [controller](./../../../Controller/Controller.php) that is used for UniqueEntity constraint.
In this case you should implement an action in your own controller with the specific route and ignore
[step 4](installation.md) of the current installation guide.
