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

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
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
        $this->routes = array(
            'local_host' => array("/", array(), array()),
        );
        if (!is_dir($this->dir)) {
            mkdir($this->dir, 0777);
        }
        foreach ($this->scripts as $script) {
            if (!is_file($this->dir . '/' . $script)) {
                touch($this->dir . '/' . $script);
            }
        }

        if (@file_get_contents("http://localhost") === false) {
            //skips warmer_routes routine if route action is unavailable on local server
            $this->routes = array();
        }
        $routes = $this->getRoutes('local_host', new Route("/"));

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
                array('apy_js_form_validation.warmer_routes', array_keys($this->routes)),
            )))
        ;

        $stubContainer
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('filesystem', Container::EXCEPTION_ON_INVALID_REFERENCE, new Filesystem()),
                array('router', Container::EXCEPTION_ON_INVALID_REFERENCE, $this->getGenerator($routes))
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

    protected function getGenerator(RouteCollection $routes, array $parameters = array(), $logger = null)
    {
        $context = new RequestContext('/');
        foreach ($parameters as $key => $value) {
            $method = 'set'.$key;
            $context->$method($value);
        }
        $generator = new UrlGenerator($routes, $context, $logger);

        return $generator;
    }

    protected function getRoutes($name, Route $route)
    {
        $routes = new RouteCollection();
        $routes->add($name, $route);

        return $routes;
    }
}