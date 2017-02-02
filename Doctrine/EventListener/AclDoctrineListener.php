<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Doctrine\EventListener;

use Bluemesa\Bundle\AclBundle\Doctrine\SecureObjectManagerInterface;
use Bluemesa\Bundle\AclBundle\Entity\SecureEntityInterface;
use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use JMS\DiExtraBundle\Annotation as DI;


/**
 * DoctrineAclListener handles insertion of ACL entries upon persistance
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\DoctrineListener(
 *     events = {"preRemove", "postPersist", "preUpdate", "postUpdate"}
 * )
 */
class AclDoctrineListener {
    
    /**
     * @var ObjectManagerRegistry
     */
    protected $registry;
    
    
    /**
     * Construct AclDoctrineListener
     * 
     * @DI\InjectParams({
     *     "registry" = @DI\Inject("bluemesa.core.doctrine.registry"),
     * })
     * 
     * @param ObjectManagerRegistry  $registry
     */
    public function __construct(ObjectManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * 
     * @param LifecycleEventArgs  $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        
        if ($object instanceof SecureEntityInterface) {
            $om = $this->registry->getManagerForClass($object);
            if (($om instanceof SecureObjectManagerInterface)&&($om->isAutoAclEnabled())) {
                $om->removeACL($object);
            }
        }
    }
    
    /**
     * 
     * @param LifecycleEventArgs  $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        
        if ($object instanceof SecureEntityInterface) {
            $om = $this->registry->getManagerForClass($object);
            if (($om instanceof SecureObjectManagerInterface)&&($om->isAutoAclEnabled())) {
                $om->createACL($object);
            }
        }
    }

    /**
     *
     * @param PreUpdateEventArgs  $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $object = $args->getObject();

        if (($object instanceof SecureEntityInterface)&&($args->hasChangedField('id'))) {
            $om = $this->registry->getManagerForClass($object);
            if (($om instanceof SecureObjectManagerInterface)&&($om->isAutoAclEnabled())) {
                $om->removeACL($object);
            }
        }
    }

    /**
     *
     * @param LifecycleEventArgs  $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $em = $args->getEntityManager();

        if ($object instanceof SecureEntityInterface) {
            $changeSet = $em->getUnitOfWork()->getEntityChangeSet($object);
            if (array_key_exists('id', $changeSet)) {
                $om = $this->registry->getManagerForClass($object);
                if (($om instanceof SecureObjectManagerInterface)&&($om->isAutoAclEnabled())) {
                    $om->createACL($object);
                }
            }
        }
    }
}
