Getting Started With JsFormValidationBundle
===========================================
**Version**: 2.1
[![Build Status](https://secure.travis-ci.org/Abhoryo/APYJsFormValidationBundle.png?branch=master)](http://travis-ci.org/Abhoryo/APYJsFormValidationBundle)

**Compatibility**: Symfony 2.1+

This bundle generate automatically a script to perform validations of a form in javascript.

It use the same constraints defined with annotations in your entity or your document.

This bundle is `g11n` compatible.(i18n + L10n) 


## Prerequisite

* [BazingaExposeTranslationBundle](https://github.com/willdurand/BazingaExposeTranslationBundle) is mandatory. This bundle compute and translate messages in javascript.
* A JavaScript framework is recommended. [jQuery](http://jquery.com/), [Mootools](http://mootools.net), [Prototype](http://prototypejs.org), [Yui](http://yuilibrary.com/), [Dojo](http://dojotoolkit.org), [Zepto](http://zeptojs.com) and [ExtJs](http://sencha.com/products/extjs/) are already supported.
(Create an issue if you want other)

## Installation

Please follow the steps given [here](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/installation.md) to install this bundle.

## Usage

This bundle is really easy to use. All you need is to call a twig function in your template.

`{{ JSFV(form) }}`

Template of a simple form:


    <!-- MyProjectMyBundle:Default:index.html.twig -->

	<!-- Include prerequisite librairies and bundles -->
	<script type="text/javascript" src="__YOUR_FRAMEWORK_URL__"></script>
	<script type="text/javascript" src="{{ asset('bundles/bazingaexposetranslation/js/translator.min.js') }}"></script>
	<script type="text/javascript" src="{{ url('bazinga_exposetranslation_js', { 'domain_name': 'validators' }) }}"></script>

	<!-- Call JsFormValidationBundle -->
	{{ JSFV(form) }}

	<!-- Display the form -->
	<form action="{{ path('myform') }}" method="post" {{ form_enctype(form) }}>
		{{ form_widget(form) }}
		<input type="submit" />
	</form>


-See a full simple example [here](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/simple_example.md).

 The following documents are available:

* [Installation](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/installation.md)
* [Simple Example](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/simple_example.md)
* [Custom Constraints](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/add_your_constraints.md)
* [Twig Function](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/twig_function.md)
* [Configuration](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/configuration.md)
* [Assets warmer](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/assets_warmer.md)
* [Events](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/events.md)
* [Overriding the bundle](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/overriding_the_bundle.md)
* [Constraints warning](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/constraints_warning.md)
* [Reporting a Bug](https://github.com/Abhoryo/APYJsFormValidationBundle/blob/master/Resources/doc/reporting_issue.md)



## TODO

* Script all possible constraints
* Manage php, yml and xml defined constraints