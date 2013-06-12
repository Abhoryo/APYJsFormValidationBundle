<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\Functional;

use Symfony\Component\Filesystem\Filesystem;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * @author  Vitaliy Demidov   <zend@i.ua>
 * @since   15 Aug 2012
 */
class BaseTestCase extends WebTestCase
{

    private $asseticWriteTo;

    /**
     * Gets kernell
     *
     * @return AppKernel
     */
    public function getKernel()
    {
        return self::$kernel;
    }

    /**
     * Gets TMP path for testing
     *
     * @return string Returns TMP path for testing
     */
    public function getTemporaryPath()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'APYJsFormValidationBundle';
    }

    static protected function createKernel(array $options = array())
    {
        $kernel = new AppKernel(
            isset($options['config']) ? $options['config'] : 'config.yml'
        );

        return $kernel;
    }

    protected function setUp()
    {
        parent::setUp();

        $fs = new Filesystem();

        $tmpdir = $this->getTemporaryPath() . DIRECTORY_SEPARATOR . "web";
        if (is_dir($tmpdir)) {
            $fs->remove($tmpdir);
        }
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Checks exception
     *
     * @param \Symfony\Component\DomCrawler\Crawler $crawler
     */
    public function checkException($crawler)
    {
        $ex = $crawler->filter('div.text-exception');
        if ($ex->count()) {
            $text = preg_replace(array('/[\t ]+/', '/[\r\n]+/'), array(" ", "\n"), strip_tags($ex->first()->text()));
            $this->assertTrue(false, $text);
        }
    }
}