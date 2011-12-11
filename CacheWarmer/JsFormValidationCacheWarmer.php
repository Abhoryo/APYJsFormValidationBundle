<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class JsFormValidationCacheWarmer implements CacheWarmerInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function warmUp($cacheDir)
    {
        // Delete all files
        $scriptPath = $this->container->getParameter('apy_js_form_validation.script_directory');
        $scriptRealPath = $this->container->getParameter('assetic.write_to').'/'.$scriptPath;
        $this->container->get('filesystem')->remove($scriptRealPath);

        $enabled = $this->container->getParameter('apy_js_form_validation.enabled');
        if ($enabled == true) {
            $warmer_routes = $this->container->getParameter('apy_js_form_validation.warmer_routes');

            foreach ($warmer_routes as $warmer_route) {
                file_get_contents($this->container->get('router')->generate($warmer_route, array(), true));
            }
        }
    }

    public function isOptional()
    {
        return true;
    }
}
