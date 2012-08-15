<?php

/**
 * This file is part of the JsFormValidationBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author   Vitaliy Demidov  <zend@i.ua>
 * @since    29 July 2012
 */

namespace APY\JsFormValidationBundle\Generator;

use Symfony\Component\Form\FormView;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Mapping\GetterMetadata;

class GettersLibraries
{

    /**
     * Constraint libararies
     * @var array
     */
    public $libraries;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var FormView
     */
    protected $formView;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, FormView $formView)
    {
        $this->container = $container;
        $this->formView = $formView;
    }

    /**
     * Gets getter name which is used as part of the library name.
     *
     * @param GetterMetadata $getterMetadata Getter Metadata
     * @param string         $glue           optional  glue token
     * @return string Returns getter name
     */
    public function getKey (GetterMetadata $getterMetadata, $glue = '.')
    {
        return sprintf("%s%s%s", end((explode(chr(92), $getterMetadata->getClassName()))), $glue,
            $getterMetadata->getName());
    }

    /**
     * Gets Bundle name using entity reference.
     *
     * @param GetterMetadata $getterMetadata
     * @return string Bundle Name
     */
    public function getBundle(GetterMetadata $getterMetadata)
    {
        $all = $this->container->getParameter('kernel.bundles');
        preg_match("/^(.+)\\\\([^\\\\]+Bundle)(\\\\.+?)$/", $getterMetadata->getClassName(), $m);
        if (!empty($m[2]) && array_key_exists($m[2], $all)) {
            $bundle = $m[2];
        } else {
            $chunks = explode(chr(92), $getterMetadata->getClassName());
            $bundle = $chunks[0] . $chunks[1];
        }
        return $bundle;
    }

    /**
     * Finds library for getter
     *
     * This function uses conventional search.
     * At first turn, it searches template in the same bundle as entity is.
     * If nothing is found it tries to find in the APYJsFormValidationBundle.
     *
     * @param   GetterMetadata $getterMetadata
     * @return  string|null Returns template for specific getter if exists or false otherwise
     */
    public function findLibrary(GetterMetadata $getterMetadata)
    {
        $validationBundle = $this->container->get('jsfv.generator')->getValidationBundle();
        $templating = $this->container->get('templating');
        foreach (array($this->getBundle($getterMetadata), $validationBundle) as $bundle) {
            $template = $bundle . ':Getters:' . $this->getKey($getterMetadata) . '.js.twig';
            if ($templating->exists($template)) {
                return $template;
            }
        }
        return null;
    }

    /**
     * Gets the libraries
     *
     * @return array Returns array of the libraries
     */
    public function all()
    {
        return $this->libraries;
    }

    /**
     * Adds a library of the specific constraint
     *
     * @param     GetterMetadata     $getterMetadata  Getter Metadata
     * @return    GettersConstraints
     */
    public function add($getterMetadata)
    {
        $libraryName = $this->getKey($getterMetadata);
        $libraryScript = $this->findLibrary($getterMetadata);
        if ($libraryScript !== null) {
            $template = $this->container->get('templating')->render($libraryScript, array(
                'name' => $this->getKey($getterMetadata, '_'),
                'form' => $this->formView,
            ));
            $this->libraries[$libraryName] = $template;
        }
        return $this;
    }

    /**
     * Checks, whether constraint library has been added or not.
     *
     * @param     GetterMetadata     $getterMetadata  Getter Metadata
     * @return    boolean Returns true if library has been added or false otherwise
     */
    public function has($getterMetadata)
    {
        return !empty($this->libraries[$this->getKey($getterMetadata)]);
    }
}