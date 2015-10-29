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

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


/**
 * OwnedObjectManagerTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait OwnedObjectManagerTrait
{   
    /**
     * Get object's owner
     *
     * @param  object                                             $object
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function getOwner($object)
    {
        $acl_array = $this->getACL($object);
        foreach ($acl_array as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if (($permission == MaskBuilder::MASK_OWNER)&&($identity instanceof UserInterface)) {
                
                return $identity;
            }
        }

        return null;
    }
    
    /**
     * Set object's owner
     *
     * @param  object                                             $objects
     * @param  Symfony\Component\Security\Core\User\UserInterface $owner
     */
    public function setOwner($objects, $owner)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->setOwner($object, $owner);
            }
        } else {
            $acl_array = $this->getACL($objects);
            $owner_found = false;
            foreach ($acl_array as $index => $entry) {
                $identity = $entry['identity'];
                $permission = $entry['permission'];
                if (($permission == MaskBuilder::MASK_OWNER)&&($identity instanceof UserInterface)) {
                    $owner_found = true;
                    if ($owner instanceof UserInterface) {
                        $acl_array[$index]['identity'] = $owner;
                    } else {
                        unset($acl_array[$index]);
                    }
                }
            }
            if (!$owner_found) {
                $acl_array[]= array('identity' => $owner, 'permission' => MaskBuilder::MASK_OWNER);
            }
            $this->updateACL($objects, $acl_array);
        }
    }
    
    /**
     * Get object's group
     *
     * @param  object $object
     * @return string
     */
    public function getGroup($object)
    {
        $acl_array = $this->getACL($object);
        foreach ($acl_array as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if (($permission == MaskBuilder::MASK_OWNER)&&(is_string($identity))) {
                
                return $identity;
            }
        }

        return null;
    }
    
    /**
     * Set object's group
     *
     * @param  object                                             $objects
     * @param  string                                             $group
     * @return Symfony\Component\Security\Core\User\UserInterface
     */
    public function setGroup($objects, $group)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->setGroup($object, $group);
            }
        } else {
            $acl_array = $this->getACL($objects);
            $group_found = false;
            foreach ($acl_array as $index => $entry) {
                $identity = $entry['identity'];
                $permission = $entry['permission'];
                if (($permission == MaskBuilder::MASK_OWNER)&&(is_string($identity))) {
                    $group_found = true;
                    if (is_string($group)) {
                        $acl_array[$index]['identity'] = $group;
                    } else {
                        unset($acl_array[$index]);
                    }
                }
            }
            if (!$group_found) {
                $acl_array[]= array('identity' => $group, 'permission' => MaskBuilder::MASK_OWNER);
            }
            $this->updateACL($objects, $acl_array);
        }
    }
}
