Assets warmer
=============

During the cache warming, you can warm the assets of your forms and then use them directly in your templates.

To warm assets, you have to define them in the configuration file of your application.

```yml
# app/config.yml

apy_js_form_validation:
	script_directory: /bundle/jsformvalidation/js/
    assets_warmer:
        - { entity_class: Acme\StoreBundle\Entity\Product, form_name: my_form, validation_groups: group1, form_fields: ['my_field1', 'my_field2'] }
        - { entity_class: Acme\StoreBundle\Entity\Product, validation_groups: { group1, group2 } }
        - { entity_class: Acme\StoreBundle\Entity\Product, form_name: my_form }
```

script_directory is optional, default value is `/bundle/jsformvalidation/js/`

entity_class argument is required.
form_name argument is optional, default value is `form`
validation_groups is optional, default value is `Default`. It can be a string (one group) or an array of groups.
form_fields is optional, default value is `ALL`. It can be a string (one field) or an array of fields. Set this array as your list of fields in your form.

In controller: `$this->createFormBuilder($product)->add('name', 'text')->add('price', 'money', array('currency' => 'USD'))`

In configuration: `form_fields: ['name', 'price']`

------

Here is the pattern of a generated file:

`script_directory.entityClass_formName_validationGroup1+validationGroup2.js`

With these parameters:

script_directory: /bundle/jsformvalidation/js/
entityClass: MyProject\MyBundle\Entity\Product
formName: my_form
validationGroups: group1, group2

will turn into:

`/bundle/jsformvalidation/js/MyProjectMyBundleEntityProduct_my_form_group1+group2.js`

If there aren't validation groups defined, the `Default` group is used.

It will turn into:

`/bundle/jsformvalidation/js/MyProjectMyBundleEntityProduct_my_form_Default.js`

When scripts are generated, you can use them is your templates.

```xml
<!-- src/MyProject/MyBundle/Resources/views/Default/index.html.twig -->
<!-- MyProjectMyBundle:Default:index.html.twig -->

<!-- Include prerequisite librairies and bundles -->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('bundles/bazingaexposetranslation/js/translation.js') }}"></script>
<script type="text/javascript" src="{{ url('bazinga_exposetranslation_js', { 'domain_name': 'validators' }) }}"></script>
<script type="text/javascript" src="/bundle/jsformvalidation/js/MyProjectMyBundleEntityProduct_my_form_Default.js"></script>

<!-- Display the form -->
<form action="{{ path('storeform') }}" method="post" {{ form_enctype(form) }}>
    {{ form_widget(form) }}
    <input type="submit" />
</form>

```

**Note:** If you use directly the path of your scripts, the enabled option in configuration has no effect.
