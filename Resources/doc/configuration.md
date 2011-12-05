Reference
=========

```yml
# app/config.yml

apy_js_form_validation:
    enabled: true
    yui_js: false
    check_mode: both
    validation_bundle: APYJsFormValidationBundle
    script_directory: bundle/jsformvalidation/js/
    warmer_routes: [route1,route2]
```

* `enabled` is optional (Default: `true`). Set to `false` disable all javascript form validations if you use the Twig function.

* `yui_js` is optional (Default: `false`). Set to `true` enable yui compressor. `yui_js` assetic filter have to be defined.

* `check_mode` is optional (Default: `both`). Mode of the validation.
Set to `submit` enable a validation of a form on the submit action.
Set to `blur` enable a validation of a field of a form when the field lost the focus.
Set to `both` enable both validations of a form.

* `bundle` is optional (Default: `APYJsFormValidationBundle`). 
You can override the default implementation of the validation script in your bundle.

Here is the default template of the script for validation. 
`APYJsFormValidationBundle::JsFormValidation.js.twig`

* `script_directory` is optional (Default: `bundle/jsformvalidation/js/`). Define where scripts will be generated.

* `warmer_routes` is optional (Default: `~`). See [Assets warmer](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/assets_warmer.md)
