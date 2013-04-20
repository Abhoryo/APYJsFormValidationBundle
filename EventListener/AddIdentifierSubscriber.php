<?php

/**
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\EventListener;

use APY\JsFormValidationBundle\Generator\FormValidationScriptGenerator;
use APY\JsFormValidationBundle\Generator\PostProcessEvent;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

/**
 * APY\JsFormValidationBundle\EventListener\AddIdentifierSubscriber
 *
 * @author   Vitaliy Demidov     <zend@i.ua>
 * @since    05 Aug 2012
 */
class AddIdentifierSubscriber implements EventSubscriberInterface
{

    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var FormValidationScriptGenerator
     */
    protected $jsfv;

    /**
     * Constructor
     * @param FormFactoryInterface $factory
     */
    public function __construct(FormFactoryInterface $factory, FormValidationScriptGenerator $jsfv)
    {
        $this->factory = $factory;
        $this->jsfv = $jsfv;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    /**
     * Insert entity identifier hidden field into form.
     *
     * This field is required in order to UniqueEntity constraint worked
     * properly on client side with update operation.
     *
     * @param    DataEvent    $event
     */
    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // This if statement let's us skip right over the null condition.
        if (null === $data || !is_object($data)) {
            return;
        }

        if ($form->getConfig()->getOption('compound') && $this->jsfv->hasUniqueEntityConstraint(get_class($data))) {
            //When entity is updated UniqueEntity constraint should ignore
            //entity with the same primary key id
            $identifierField = $this->jsfv->getParameter('identifier_field');
            $form->add($this->factory->createNamed(
                $identifierField,
                'hidden',
                json_encode($this->jsfv->getEntityIdentifierValue($data)),
                array(
                    'mapped' => false,
                )
            ));
        }
    }
}