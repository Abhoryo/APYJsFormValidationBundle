<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\CacheWarmer;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use APY\JsFormValidationBundle\CacheWarmer\JsFormValidationCacheWarmer;

/**
 * JsFormValidationCacheWarmer Test
 *
 * @author    Vitaliy Demidov   <zend@i.ua>
 * @since     13 Aug 2012
 */
class JsFormValidationCacheWarmerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var JsFormValidationCacheWarmer
     */
    private $cacheWarmer;

    private $dir;

    private $routes;

    public function setUp()
    {
        $path = 'jsformvalidation';
        $this->dir = sys_get_temp_dir() . '/' . $path;
        $this->scripts = array('route1.js', 'route2.js', 'route3.js');
        $this->routes = array();
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0777);
        }
        foreach ($this->scripts as $script) {
            if (!is_file($this->dir . '/' . $script)) {
                touch($this->dir . '/' . $script);
            }
        }
        $stubRouter = $this->getMockBuilder('Symfony\\Component\\Routing\\Router')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dir = $this->dir;
        $stubRouter
            ->expects($this->any())
            ->method('generate')
            ->will($this->returnCallback(function($route, $pars, $absolute) use ($dir) {
                return $dir . "/" . $route;
            }))
        ;

        $stubContainer = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\Container')
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $stubContainer
            ->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValueMap(array(
                array('apy_js_form_validation.script_directory', $path),
                array('assetic.write_to', sys_get_temp_dir()),
                array('apy_js_form_validation.enabled', true),
                array('apy_js_form_validation.warmer_routes', $this->routes),
            )))
        ;

        $stubContainer
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('filesystem', Container::EXCEPTION_ON_INVALID_REFERENCE, new Filesystem()),
                array('router', Container::EXCEPTION_ON_INVALID_REFERENCE, $stubRouter)
            )))
        ;
        $this->cacheWarmer = new JsFormValidationCacheWarmer($stubContainer);
    }

    public function tearDown()
    {
        foreach ($this->scripts as $script) {
            if (is_file($this->dir . '/' . $script)) {
                @unlink($this->dir . '/' . $script);
            }
        }
        if (is_dir($this->dir)) {
            @rmdir($this->dir);
        }
        unset($this->cacheWarmer);
        unset($this->dir);
        unset($this->routes);
    }

    public function testWarmUp()
    {
        $this->cacheWarmer->warmUp('no use');
        foreach ($this->routes as $route) {
            $this->assertFileNotExists($this->dir . $route);
        }
    }

    public function testIsOptional()
    {
        $this->assertTrue($this->cacheWarmer->isOptional());
    }
}