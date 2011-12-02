Twig function
==========================

The twig function JSFV generate the script and returns the path of the script inside a script balise.

```jinja
{{ JSFV(form) }}
```

will turn into:

```xml
<script type="text/javascript" src="/bundle/jsformvalidation/js/my_route.js"></script>
```

The script isn't regenerate if the script already exists.

**Note:** If the bundle is disabled, this Twig function returns nothing.

---------

JSFV function accepts a boolean argument. Sets to true, the Twig function returns only the path of the script.

```jinja
{{ JSFV(form, true) }}
```

will turn into:

`/bundle/jsformvalidation/js/my_route.js`

So you can use this too:

```jinja
<script type="text/javascript" src="{{ asset( JSFV(form, true) ) }}"></script>
```

**Note:** If the bundle is disabled, this Twig function returns `/bundle/jsformvalidation/js/no_jsfv_script.js`.
