Reference
=========

```yml
# app/config.yml

apy_js_form_validation:
    enabled: true
    yui_js: false
    check_modes: [submit, blur]
    javascript_framework: jquery
    validation_bundle: APYJsFormValidationBundle
    script_directory: bundles/jsformvalidation/js/
    warmer_routes: [route1,route2]
    identifier_field: jsfv_identifier
```

* `enabled` is optional (Default: `true`). Set to `false` disable all javascript form validations if you use the Twig function.

* `yui_js` is optional (Default: `false`). Set to `true` enable yui compressor. `yui_js` assetic filter have to be defined.

* `check_mode` **DEPRECATED** use `check_modes` instead.

* `check_modes` is optional (Default: ["submit", "blur"]). Modes of the validation.
Add `submit` to enable a validation of a form on the submit action.
Add `blur` to enable a validation of a field of a form when the field lost the focus.
Add `change` to enable a validation of a field of a form when the field is changed. This will behave mostly the same as `blur`, but will avoid race condition and false validation errors to appear with widgets like jQueryUI datepicker.

* `javascript_framework` is recommended (Default: `jquery`). Javascript framework used by the validation script.
Choices: `jquery`, `mootools`, `prototype`, `yui3`, `dojo`, `zepto` or `extjs`

* `validation_bundle` is optional (Default: `APYJsFormValidationBundle`).
You can override the default implementation of the validation script and its framework variants in your bundle.

Here is the template of the common script for validation.
`APYJsFormValidationBundle::JsFormValidation.js.twig`

Here is a framework template of a the common script for validation.
`APYJsFormValidationBundle:Frameworks:JsFormValidation.your_framework.js.twig`

* `script_directory` is optional (Default: `bundles/jsformvalidation/js/`). Define where scripts will be generated.

* `warmer_routes` is optional (Default: `~`). See [Assets warmer](assets_warmer.md)

* `identifier_field` is optional (Default: `jsfv_identifier`).
Defines the name of the hidden field which serves to convey the primary identifier value for UniqueEntity constraint request.
This value will be ignored when entity is updated.
