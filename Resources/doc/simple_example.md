A Full Simple Example
========================

## Step 1 : Create a bundle

In this example, we create a bundle `MyBundle` in the directory `src/MyProject`

```bash
$ php app/console generate:bundle --namespace=MyProject/MyBundle --format=yml
```

## Step 2: Create Routes

Add these two routes in the routing file of the bundle.

```yml
# src/MyProject/MyBundle/Resources/config/routing.yml
myform:
    pattern:  /myform
    defaults: { _controller: MyProjectMyBundle:Default:form }
	
myformsuccess:
    pattern:  /myformsuccess
    defaults: { _controller: MyProjectMyBundle:Default:success }
```

## Step 3: Create an product entity with constraints

> **Note!** You may create form which is not relate on Entity.
> Look at [Validation of the forms which are built manually.](simpleform_example.md)

```php
<?php
// src/MyProject/MyBundle/Entity/Product.php
namespace MyProject\MyBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    /**
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=20)
     */
    protected $price;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setPrice($price)
    {
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }
}
```

## Step 4: Create a template

```jinja
<!-- src/MyProject/MyBundle/Resources/views/Default/index.html.twig -->
<!-- MyProjectMyBundle:Default:index.html.twig -->

<!-- Include prerequisite librairies and bundles -->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('bundles/bazingaexposetranslation/js/translator.min.js') }}"></script>
<script type="text/javascript" src="{{ url('bazinga_exposetranslation_js', { 'domain_name': 'validators' }) }}"></script>

<!-- Call JsFormValidationBundle -->

{{ JSFV(form) }}

<!-- Display the form -->
<form action="{{ path('storeform') }}" method="post" {{ form_enctype(form) }}>
    {{ form_widget(form) }}
    <input type="submit" />
</form>

```

## Step 5: Create the Controller

```php
<?php
// src/MyProject/MyBundle/Controller/Default.php
namespace MyProject\MyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use MyProject\MyBundle\Entity\Product;

class DefaultController extends Controller
{
    public function formAction()
    {
        $product = new Product();
        $form = $this->createFormBuilder($product)
            ->add('name', 'text')
            ->add('price', 'money', array('currency' => 'USD'))
            ->getForm();

        $request = $this->get('request');
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                return $this->redirect($this->generateUrl('myformsuccess'));
            }
        }

        return $this->render('MyProjectMyBundle:Default:index.html.twig', array( 'form' => $form->createView() ));
    }

    public function successAction()
    {
        return new Response('Product posted');
    }
}
```

## Step 6: Clear the cache

Execute this command to clear the cache:

```bash
$ php app/console cache:clear
```

## Step 7: Usage

Go to your form page. ( ie : http://localhost/app_dev.php/myform )

* Click in and out of a field, validation messages appear.
* Put some text in the name field and the number 10 in the price field.
* Submit the form.
* A validation message for the price field appears. The submit action is prevented by the bundle when all fields are not correctly filled.

Read also:
[Validation of the forms which are built manually.](simpleform_example.md)
