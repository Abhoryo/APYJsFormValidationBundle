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

require_once __DIR__ . '/../bootstrap.php';

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author   Vitaliy Demidov   <zend@i.ua>
 * @since    15 Aug 2012
 */
class AppKernel extends Kernel
{
    private $config;

    private $coverage;

    public function __construct($config)
    {
        $this->rootDir = __DIR__ . DIRECTORY_SEPARATOR ;

        parent::__construct('test', true);

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($config)) {
            $config = $this->getRootDir() . '/TestBundle/Resources/config/' . $config;
        }

        if (!file_exists($config)) {
            throw new \RuntimeException(sprintf('The config file "%s" does not exist.', $config));
        }

        $this->config = $config;
    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \JMS\AopBundle\JMSAopBundle(),
            new \JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new \Bazinga\ExposeTranslationBundle\BazingaExposeTranslationBundle(),
            new TestBundle\TestBundle(),
            new \APY\JsFormValidationBundle\APYJsFormValidationBundle(),
            new \Liip\FunctionalTestBundle\LiipFunctionalTestBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->config);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'APYJsFormValidationBundle' . DIRECTORY_SEPARATOR . 'cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'APYJsFormValidationBundle' . DIRECTORY_SEPARATOR . 'logs';
    }

    public function serialize()
    {
        return serialize(array($this->config));
    }

    public function unserialize($str)
    {
        call_user_func_array(array($this, '__construct'), unserialize($str));
    }
}