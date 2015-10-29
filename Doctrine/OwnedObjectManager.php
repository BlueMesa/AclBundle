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


/**
 * Ownership aware implementation of SecureObjectManager
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\Service("bluemesa.acl.doctrine.owned_manager")
 * @DI\Tag("bluemesa_core.object_manager")
 */
class OwnedObjectManager extends SecureObjectManager implements OwnedObjectManagerInterface
{
    use OwnedObjectManagerTrait;
    
    /**
     * Class managed by this ObjectManager
     */
    const MANAGED_CLASS = 'Bluemesa\Bundle\AclBundle\Entity\OwnedEntity';
}
