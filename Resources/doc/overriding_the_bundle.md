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

## Constraints

The contraints are pure javascript but you can overriding them.
Constraints script are defined in template files so you can overriding them like a normal template.

* `app/Resources/JsFormValidationBundle/views/Constraints/MinValidator.js.twig`
* `app/Resources/JsFormValidationBundle/views/Constraints/NotBlankValidator.js.twig`
