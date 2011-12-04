<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle;

final class JsfvEvents
{
    /**
     * The jsfv.pre_process event is thrown before creating constraints 
     * from metadata of a from's fields.
     * The event listener receives an 
     * APY\JsFormValidationBundle\Generator\PreProcessEvent instance.
     *
     * @var string
     */
    const preProcess = 'jsfv.pre_process';

    /**
     * The jsfv.post_process event is thrown after creating constraints 
     * from metadata of a from's fields.
     * The event listener receives an 
     * APY\JsFormValidationBundle\Generator\PostProcessEvent instance.
     *
     * @var string
     */
    const postProcess = 'jsfv.post_process';
}
