Installation
============

## Step 1: Install BazingaExposeTranslationBundle

Please follow the steps given [here](https://github.com/willdurand/BazingaExposeTranslationBundle/blob/master/README.markdown) to install this bundle.

## Step 2: Download JsFormValidationBundle

### a) Using Composer (recommended)

To install APYJsFormValidationBundle with Composer just add the following to your composer.json file:

```
// composer.json
{
    // ...
    require: {
        // ...
        "apy/jsfv-bundle":"dev-master"
    }
}
```

> **Note**: dev-master version of this bundle is compatible only with symfony 2.1.x.
> Please use the deps file if you install this bundle for Symfony 2.0.x.

Then, you can install the new dependencies by running Composer's update
command from the directory where your composer.json file is located:

```bash
php composer.phar update
```

Now, Composer will automatically download all required files, and install them for you.
Let's go to step 3.


### b) Using deps (Symfony 2.0.x)

Ultimately, the JsFormValidationBundle files should be downloaded to the
`vendor/bundles/APY/JsFormValidationBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard Symfony2 method.

**Using the vendors script**

Add the following lines in your `deps` file:

```
[JsFormValidationBundle]
    git=git://github.com/Abhoryo/APYJsFormValidationBundle.git
    target=bundles/APY/JsFormValidationBundle
    version=2.0
```

Now, run the vendors script to download the bundle:

```bash
$ php bin/vendors install
```

**Using submodules**

If you prefer instead to use git submodules, the run the following:

```bash
$ git submodule add git://github.com/Abhoryo/APYJsFormValidationBundle.git vendor/bundles/APY/JsFormValidationBundle
$ git submodule update --init
```

**Configure the Autoloader**

Add the `APY` namespace to your autoloader:

```php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'APY' => __DIR__.'/../vendor/bundles',
));
```

## Step 3: Enable the bundles

Enable the bundles in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new APY\JsFormValidationBundle\APYJsFormValidationBundle(),
    );
}
```

## Step 4: Import the routes

Finally, register the routing in app/config/routing.yml:

```yml
# app/config/routing.yml
_apy_jsformvalidation:
    resource: "@APYJsFormValidationBundle/Resources/config/routing/routing.yml"
```
