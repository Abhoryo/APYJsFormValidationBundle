<?php

/**
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Controller;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

/**
 * APYJsFormValidationBundle:MainController
 *
 * @author Vitaliy Demidov  <zend@i.ua>
 * @since  31 July 2012
 */
class Controller
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Default constructor.
     *
     * @param ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * Gets an EntityManager
     * @return EntityManager Returns EntityManager
     */
    protected function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Validates UniqueEntity Constraint
     *
     * @return    Response   Returns json response
     * @author    Vitaliy Demidov   <zend@i.ua>
     * @since     31 July 2012
     */
    public function uniqueEntityAction()
    {
        $request = $this->container->get('request');
        $entity = $request->request->get('entity');
        $value = $request->request->get('value');
        $target = $request->request->get('target');
        $ignore = $request->request->get('ignore');
        $ignore = empty($ignore) ? null : json_decode($ignore, true);
        $a = new \stdClass();
        try {
            if (!empty($target) && !empty($entity) && $this->hasUniqueEntityConstraint($entity, $target)) {
                $a->isUnique = $this->isUnique($entity, $target, $value, $ignore);
            } else {
                return $this->getAjaxResponse($a, 'Invalid arguments.');
            }
        } catch (\Exception $e) {
            return $this->getAjaxResponse($a, 'Failure');
        }

        return $this->getAjaxResponse($a);
    }

    /**
     * Check DNS records corresponding to a given Internet host name or IP address
     *
     * @return    Response  Returns json response
     * @author    Vitaliy Demidov   <zend@i.ua>
     * @since     10 Aug 2012
     */
    public function checkMxAction()
    {
        $a = new \stdClass();
        $request = $this->container->get('request');
        $address = $request->get('address');
        $type = $request->get('type') ?: "MX";
        if (empty($address)) {
            return $this->getAjaxResponse($a, 'Invalid arguments.');
        }
        try {
            $a->type = $type;
            $a->result = \checkdnsrr($address, $type);
        } catch (\Exception $e) {
            return $this->getAjaxResponse($a, 'Failure');
        }
        return $this->getAjaxResponse($a);
    }

    /**
     * Verifies if requested field has UniqueEntity constraint for desired entity.
     *
     * @param      string     $entityClass    Entity class name
     * @param      string     $field          Field name
     * @return     boolean    Returns TRUE if field is allowed
     * @author     Vitaliy Demidov   <zend@i.ua>
     * @since      31 July 2012
     */
    protected function hasUniqueEntityConstraint($entityClass, $field)
    {
        $scriptGenerator = $this->container->get('jsfv.generator');
        $metadata = $scriptGenerator->getClassMetadata($entityClass);
        if (!empty($metadata->constraints)) {
            foreach ($metadata->constraints as $constraint) {
                $constraintName = end((explode(chr(92), get_class($constraint))));
                if ($constraintName == 'UniqueEntity') {
                    if (is_array($constraint->fields) && in_array($field, $constraint->fields) ||
                        is_string($constraint->fields) && $field == $constraint->fields) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Gets json response
     *
     * @param      object    $a        response object
     * @param      string    $error    error message
     * @return     Response
     */
    protected function getAjaxResponse($a, $error = null)
    {
        if (!empty($error)) {
            $a->error = $error;
            $a->status = 'error';
        } else {
            $a->status = 'ok';
        }
        return new Response(json_encode($a));
    }

    /**
     * Checks uniqueness of value for the target field.
     *
     * If ignore parameter is provided, entity with this id will be ignored.
     *
     * @param     string   $entity    Entity name (for example FooUserBundle:User)
     * @param     string   $target    field name
     * @param     mixed    $value     field value
     * @param     mixed    $ignore    optional  Primary identifier values of the record which should be ignored
     * @return    boolean  Returns true if value is unique. It returns false if entity with such field's value does exist.
     * @author    Vitaliy Demidov  <zend@i.ua>
     * @since     31 July 2012
     */
    protected function isUnique($entity, $target, $value, $ignore = null)
    {
        $em = $this->getEntityManager();

        $entityMetadata = $em->getClassMetadata($entity);
        if (empty($entityMetadata)) {
            throw new \RuntimeException("Invalid entity class: " . (string)$entity);
        }
        $identifier = $entityMetadata->getIdentifier();

        $qb = $em->createQueryBuilder();
        $qb->select('u')->from($entity, 'u');

        $and = array($qb->expr()->eq('u.' . $target, ':value'));

        if ($ignore !== null && is_array($ignore) && !empty($identifier)) {
            $criterias = array();
            foreach ($identifier as $i => $field) {
                $v = isset($ignore[$i]) ? $ignore[$i] : null;
                if ($v === null) {
                    $criterias[] = $qb->expr()->isNull('u.' . $field);
                } else {
                    $criterias[] = $qb->expr()->eq('u.' . $field, ':key' . $i);
                    $qb->setParameter('key' . $i, $v);
                }
            }
        }

        if (!empty($criterias)) {
            $and[] = $qb->expr()->not(\call_user_func_array(array($qb->expr(), 'andX'), $criterias));
        }

        $qb->where(\call_user_func_array(array($qb->expr(), 'andX'), $and))
           ->setParameter('value', $value);

        $query = $qb->getQuery();

        try {
            $res = $query->getSingleResult();
        } catch (NoResultException $e) {
            $res = null;
        }

        return $res === null ? true : false;
    }
}