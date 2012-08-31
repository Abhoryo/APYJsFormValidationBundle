<?php

/*
 * This file is part of the JsFormValidationBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\JsFormValidationBundle\Tests\Fixtures;

use Doctrine\ORM\Mapping\ClassMetadata;
use APY\JsFormValidationBundle\Tests\Functional\TestBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixture loader
 *
 * @author Vitaliy Demidov  <zend@i.ua>
 * @since  18 Aug 2012
 */
class LoadUserData implements FixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setId(1);
        $user->setUsername('taken');
        $manager->persist($user);

        $metadataUser = $manager->getClassMetaData(get_class($user));
        $metadataUser->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);

        $manager->flush();

        $metadataUser->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_AUTO);
    }
}
