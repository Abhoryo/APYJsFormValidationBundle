Twig function
==========================

The twig function JSFV generates the script and returns the path of the script inside a script tag.

```jinja
{{ JSFV(form) }}
```

will turn into:

```xml
<script type="text/javascript" src="/bundle/jsformvalidation/js/myRoute_myForm.js"></script>
```

The script isn't regenerated if the script already exists.

**Note:** If the bundle is disabled, this Twig function returns nothing.

---------

JSFV function accepts a boolean argument. Sets to true, the Twig function returns only the path of the script.

```jinja
{{ JSFV(form, true) }}
```

will turn into:

`/bundle/jsformvalidation/js/myRoute_myForm.js`

So you can use this too:

```jinja
<script type="text/javascript" src="{{ JSFV(form, true) }}"></script>
```

**Note:** The bundle uses the asset helper function.

**Note:** If the bundle is disabled, this Twig function returns `/bundle/jsformvalidation/js/no_jsfv_script.js`.
