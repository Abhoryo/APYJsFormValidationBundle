Installation
============

## Step 1: Install BazingaExposeTranslationBundle

Please follow the steps given [here](https://github.com/willdurand/BazingaExposeTranslationBundle/blob/master/README.markdown) to install this bundle.

## Step 2: Download JsFormValidationBundle

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

## Step 3: Configure the Autoloader

Add the `APY` namespace to your autoloader:

```php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'APY' => __DIR__.'/../vendor/bundles',
));
```

## Step 4: Enable the bundles

Finally, enable the bundles in the kernel:

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
