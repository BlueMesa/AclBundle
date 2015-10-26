<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Listener;

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Bluemesa\Bundle\AclBundle\Doctrine\SecureObjectManagerInterface;
use Bluemesa\Bundle\AclBundle\Entity\SecureEntityInterface;
use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry;

/**
 * Description of DoctrineAclListener
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\DoctrineListener(
 *     events = {"preRemove", "postPersist"}
 * )
 */
class AclDoctrineListener {
    
    /**
     * @var Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry
     */
    protected $registry;
    
    
    /**
     * Construct AclDoctrineListener
     * 
     * @DI\InjectParams({
     *     "registry" = @DI\Inject("bluemesa.core.doctrine.registry"),
     * })
     * 
     * @param Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManagerRegistry  $registry
     */
    public function __construct(ObjectManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    /**
     * 
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
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
     * @param Doctrine\ORM\Event\LifecycleEventArgs $args
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
}
