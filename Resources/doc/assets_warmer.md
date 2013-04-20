Assets warmer
=============

During the cache warming, you can warm the assets of your forms and then use them directly in your templates.

To warm assets, you have to define them in the configuration file of your application.

```yml
# app/config.yml

apy_js_form_validation:
    script_directory: bundles/jsformvalidation/js/
    warmer_routes: [my_route1,my_route2]
```

* `script_directory` is optional, default value is `bundles/jsformvalidation/js/`
* `warmer_routes`, list of your routes.

------

When scripts are generated, you can use them is your templates.

```jinja
<!-- src/MyProject/MyBundle/Resources/views/Default/index.html.twig -->
<!-- MyProjectMyBundle:Default:index.html.twig -->

<!-- Include prerequisite librairies and bundles -->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('bundles/bazingaexposetranslation/js/translator.min.js') }}"></script>
<script type="text/javascript" src="{{ url('bazinga_exposetranslation_js', { 'domain_name': 'validators' }) }}"></script>
<script type="text/javascript" src="{{ asset('bundles/jsformvalidation/js/my_route1.js') }}"></script>

<!-- Display the form -->
<form action="{{ path('storeform') }}" method="post" {{ form_enctype(form) }}>
    {{ form_widget(form) }}
    <input type="submit" />
</form>

```

**Note:** If you use directly the path of your scripts, the enabled option in configuration has no effect.
