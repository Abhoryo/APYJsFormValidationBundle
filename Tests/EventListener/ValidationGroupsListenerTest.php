<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\EventListener;

use APY\JsFormValidationBundle\EventListener\ValidationGroupsListener,
    Symfony\Component\Form\FormInterface;

/**
 * ValidationGroupsListener Test
 *
 * @author    Vitaliy Demidov   <zend@i.ua>
 * @since     31 Aug 2012
 */
class ValidationGroupsListenerTest extends \PHPUnit_Framework_TestCase
{

    public static function providerOnJsfvProcess()
    {
        $fields1 = array(
            'name' => array(
              'NotBlank' => array('groups' => array('g1')),
            ),
            'email' => array(
              'NotBlank' => array('groups' => array('g2')),
              'Email' => null
            ),
            'description' => array(
              'NotBlank' => array('groups' => array('Default', 'g1')),
            )
        );
        $data = array(
            array(
                $fields1,
                array('g1'),
                array('name' => 1, 'email' => 0, 'description' => 1),
            ),
            array(
                $fields1,
                array('Default'),
                array('name' => 0, 'email' => 1, 'description' => 1),
            ),
            array(
                $fields1,
                array('Default', 'g2'),
                array('name' => 0, 'email' => 2, 'description' => 1),
            ),
            array(
                $fields1,
                function(FormInterface $form) {
                    return array('g1');
                },
                array('name' => 1, 'email' => 2, 'description' => 1),
            )
        );
        foreach ($data as $i => $pars) {
            foreach ($pars[0] as $field => $aConstraints) {
                if ($aConstraints === null) continue;
                $c = new \stdClass();
                $c->constraints = array();
                foreach ($aConstraints as $name => $options) {
                    $class = 'Symfony\\Component\\Validator\\Constraints\\' . $name;
                    $c->constraints[] = new $class ($options);
                }
                $data[$i][0][$field] = $c;
                unset($c);
            }
        }
        return $data;
    }

    /**
     * @dataProvider providerOnJsfvProcess
     */
    public function testOnJsfvPreProcess($fields, $validationGroups, $result)
    {
        $stubFormView = $this->getMock('Symfony\\Component\\Form\\FormView');
        $stubFormView->children = array_combine(array_keys($fields), array_pad(array(), count($fields), null));
        $stubFormView->vars = array(
            'validation_groups' => $validationGroups
        );

        $stubMetadata = $this->getMock('Symfony\\Component\\Validator\\Mapping\\ClassMetadata', null, array(
            'Entity'
        ));
        $stubMetadata->properties = $fields;

        $stubEvent = $this->getMock('APY\\JsFormValidationBundle\\Generator\\PreProcessEvent', null, array(
            $stubFormView, $stubMetadata
        ));

        $listener = new ValidationGroupsListener();
        $listener->onJsfvPreProcess($stubEvent);

        foreach ($stubMetadata->properties as $k => $v) {
            $this->assertEquals($result[$k], count($v->constraints));
        }
    }
}