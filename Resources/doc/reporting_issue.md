Reporting a Bug
---------------

Whenever you find a bug in this bundle, we kindly ask you to report it. It helps us make a it better.

Before submitting a bug:

* Double-check the documentation1 to see if you're not misusing the functionality;

If your problem definitely looks like a bug, report it using the official
[bug tracker](https://github.com/Abhoryo/APYJsFormValidationBundle/issues)
and follow some basic rules:

* Use the title field to clearly describe the issue;

* Describe the steps needed to reproduce the bug with short code examples (providing a unit
test that illustrates the bug is best);

* Give as much details as possible about your environment (OS, PHP version, Symfony version,
enabled extensions, ...);

* (optional) Attach a patch. Before submitting a patch for inclusion, you need to run
the test suite `phpunit -c vendor/apy/jsfv-bundle/APY/JsFormValidationBundle`
to check that you have not broken anything.


## Running Tests

First of all you need to install dev dependencies.

```bash
# it assumes that you in the root folder of your app
$ ls composer.phar
composer.phar

$ cd vendor/apy/jsfv-bundle/APY/JsFormValidationBundle

$ ../../../../../composer.phar install --dev

# then you can run tests of apy/jsfv-bundle
$ phpunit -c .
PHPUnit 3.6.10 by Sebastian Bergmann.

Configuration read from root-folder-of-your-app\vendor\apy\jsfv-bundle\APY\JsFormValidationBundle\phpunit.xml.dist

....

Time: 9 seconds, Memory: 30.75Mb

OK (4 tests, 50 assertions)

```


###PHPUnit
To run the Symfony2 test suite, install PHPUnit 3.5.11 or later:

```bash
$ pear channel-discover pear.phpunit.de
$ pear channel-discover components.ez.no
$ pear channel-discover pear.symfony-project.com
$ pear install phpunit/PHPUnit
```

Thank You.