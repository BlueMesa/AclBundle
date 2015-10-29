<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Doctrine;

use JMS\DiExtraBundle\Annotation as DI;
use Bluemesa\Bundle\CoreBundle\Doctrine\ObjectManager;


/**
 * ACL aware implementation of Doctrine\Common\Persistence\ObjectManagerDecorator
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("bluemesa.acl.doctrine.secure_manager")
 * @DI\Tag("bluemesa_core.object_manager")
 */
class SecureObjectManager extends ObjectManager implements SecureObjectManagerInterface
{
    use SecureObjectManagerTrait;
    
    /**
     * Class managed by this ObjectManager
     */
    const MANAGED_CLASS = 'Bluemesa\Bundle\AclBundle\Entity\SecureEntity';
    
    
    /**
     * Construct SecureObjectManager
     */
    public function __construct()
    {
        $this->isAutoAclEnabled = true;
    }
}
