Getting Started With JsFormValidationBundle
===========================================

This bundle generate automatically a script to perform validations of a form in javascript.

It use the same constraints defined with annotations for your entity or your document.

This bundle is `g11n` compatible.(i18n + L10n) 

**Compatibility**: Symfony 2.0+

## Prerequisite

* The JavaScript framework [jQuery](http://jquery.com/) is recommended.
* [BazingaExposeTranslationBundle](https://github.com/Bazinga/BazingaExposeTranslationBundle/blob/master/README.markdown) is mandatory. This bundle compute and translate messages in javascript.

## Installation

Please follow the steps given [here](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/installation.md) to install this bundle.

## Usage

This bundle is really easy to use. All you need is to call a twig function in your template.

`{{ JSFV(form) }}`

Template of a simple form:


    <!-- MyProjectMyBundle:Default:index.html.twig -->

	<!-- Include prerequisite librairies and bundles -->
	<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script type="text/javascript" src="{{ asset('bundles/bazingaexposetranslation/js/translation.js') }}"></script>
	<script type="text/javascript" src="{{ url('bazinga_exposetranslation_js', { 'domain_name': 'validators' }) }}"></script>

	<!-- Call JsFormValidationBundle -->
	{{ JSFV(form) }}

	<!-- Display the form -->
	<form action="{{ path('myform') }}" method="post" {{ form_enctype(form) }}>
		{{ form_widget(form) }}
		<input type="submit" />
	</form>


See a full simple example [here](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/simple_example.md).

The following documents are available:

1. [Installation](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/installation.md)
2. [Simple Example](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/simple_example.md)
3. [Twig Function](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/twig_function.md)
4. [Configuration](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/configuration.md)
5. [Assets warmer](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/assets_warmer.md)
6. [Overriding the bundle](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/overriding_the_bundle.md)
7. [Constraints warning](https://github.com/APY/APYJsFormValidationBundle/blob/master/Resources/doc/constraints_warning.md)

## TODO

* Script all possible constraints
* Manage php, yml and xml defined constraints
* Minify script with other compressor ?
* Implement validation script with other javascript framework ?
