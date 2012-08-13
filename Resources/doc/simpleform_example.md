## Validation of the forms which are built manually.

The bundle supports validation of simple forms which are built manually, but not on Entity.

Have a look at controller:

```php
<?php
//...
class DefaultController
{
	/**
	 * @Route("/your-route", name="your_route")
	 * @Template
	 */
	public function yourAction()
	{
	    $request = $this->getRequest();
	
	    $defaultData = array(
	        'username' => '',
	        'password' => '',
	        'email'    => ''
	    );
	    $collectionConstraint = new Collection(array(
	        'username' => new NotBlank(),
	        'password' => new NotBlank(),
	        'email'    => array(
	            new NotBlank(),
	            new Email(array('checkMX' => true)),
	        ),
	    ));
	
	    $form = $this->createFormBuilder(
	            $defaultData,
	            array(
	                'validation_constraint' => $collectionConstraint,
	            )
	        )
	        ->add('username', 'text')
	        ->add('password', 'password')
	        ->add('email', 'email')
	        ->getForm()
	    ;
	
	    if ($request->getMethod() == 'POST') {
	        $form->bind($request);
	        if ($form->isValid()) {
	            //...
	        }
	    }
	    return array(
	        'form' => $form->createView(),
	    )
	}
}
```

JSFormValidationBundle is working fine:

```twig
<form id="{{ form.vars.id }}" action="{{ path('your_route') }}" method="post" {{ form_enctype(form) }}>
<fieldset>
    {{ form_errors(form) }}
    
    {{ form_row(form.username) }}
    {{ form_row(form.password) }}
    {{ form_row(form.email) }}
    
    {{ form_rest(form) }}
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</fieldset>
</form>

<!-- Call JsFormValidationBundle -->
{{ JSFV(form) }}
```
