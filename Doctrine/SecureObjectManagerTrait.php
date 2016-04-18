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
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Bluemesa\Bundle\AclBundle\DependencyInjection\AuthorizationCheckerAwareTrait;
use Bluemesa\Bundle\AclBundle\DependencyInjection\TokenStorageAwareTrait;
use Bluemesa\Bundle\AclBundle\DependencyInjection\UserProviderAwareTrait;
use Bluemesa\Bundle\AclBundle\DependencyInjection\AclProviderAwareTrait;
use Bluemesa\Bundle\AclBundle\DependencyInjection\UserAwareTrait;

/**
 * SecureObjectManagerTrait
 * 
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait SecureObjectManagerTrait
{
    use AuthorizationCheckerAwareTrait, TokenStorageAwareTrait, UserProviderAwareTrait, AclProviderAwareTrait, UserAwareTrait;
    
    /**
     * @var boolean
     */
    protected $isAutoAclEnabled;

    
    /**
     * Create ACL for object(s)
     *
     * @param object $objects
     * @param array  $acl_param
     */
    public function createACL($objects, $acl_param = null)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->createACL($object, $acl_param);
            }
        } else {
            $object = $objects;
            if (null === $acl_param) {
                $acl_param = $this->getDefaultACL($object);
            } elseif (($user = $acl_param) instanceof UserInterface) {
                $acl_param = $this->getDefaultACL($object, $user);
            }
            $objectIdentity = ObjectIdentity::fromDomainObject($object);
            $aclProvider = $this->aclProvider;
            $acl = $aclProvider->createAcl($objectIdentity);
            $this->insertAclEntries($acl, $acl_param);
            $aclProvider->updateAcl($acl);
        }
    }

    /**
     * Delete ACL for object(s)
     *
     * @param object $objects
     */
    public function removeACL($objects)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->removeACL($object);
            }
        } else {
            $object = $objects;
            $objectIdentity = ObjectIdentity::fromDomainObject($object);
            $aclProvider = $this->aclProvider;
            try {
                $aclProvider->deleteAcl($objectIdentity);
            } catch (AclNotFoundException $e) {}
        }
    }
    
    /**
     * Update ACL for object(s)
     *
     * @param object $objects
     * @param array  $acl_array
     */
    public function updateACL($objects, array $acl_array)
    {
        if ($objects instanceof Collection) {
            foreach ($objects as $object) {
                $this->updateACL($object, $acl_array);
            }
        } else {
            $object = $objects;
            $aclProvider = $this->aclProvider;
            $objectIdentity = ObjectIdentity::fromDomainObject($object);
            try {
                $acl = $aclProvider->findAcl($objectIdentity);
                if ($acl instanceof MutableAclInterface) {
                    $diff = $this->diffACL($acl, $acl_array);
                    $this->updateAclEntries($acl, $diff['update']);
                    $this->deleteAclEntries($acl, $diff['delete']);
                    $this->insertAclEntries($acl, $diff['insert']);
                    $aclProvider->updateAcl($acl);
                }
            } catch (AclNotFoundException $e) {
                $this->createACL($object, $acl_array);
            }
        }
    }
    
    /**
     * Get ACL for object
     *
     * @param  object  $object
     * @return array
     */
    public function getACL($object)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $aclProvider = $this->aclProvider;
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
            $acl_array = array();
            /**
             * @var integer        $index
             * @var EntryInterface $ace
             */
            foreach ($acl->getObjectAces() as $index => $ace) {
                $identity = $this->resolveIdentity($ace);
                $acl_array[$index] = array('identity' => $identity, 'permission' => $ace->getMask());
            }
        } catch (AclNotFoundException $e) {
            
            return array();
        }
        
        return $acl_array;
    }
    
    /**
     * Enable automatic ACL setting
     */
    public function enableAutoAcl()
    {
        $this->isAutoAclEnabled = true;
    }
    
    /**
     * Disable automatic ACL setting
     */
    public function disableAutoAcl()
    {
        $this->isAutoAclEnabled = false;
    }
    
    /**
     * Is automatic ACL setting enabled
     * 
     * @return boolean
     */
    public function isAutoAclEnabled()
    {
        return $this->isAutoAclEnabled;
    }
    
    /**
     * Resolve ACE itentity to User or Role
     * 
     * @param  EntryInterface  $ace
     * @return mixed
     */
    protected function resolveIdentity(EntryInterface $ace)
    {
        $securityIdentity = $ace->getSecurityIdentity();
        if ($securityIdentity instanceof UserSecurityIdentity) {
            $userProvider = $this->userProvider;
            try {
                
                return $userProvider->loadUserByUsername($securityIdentity->getUsername());
                
            } catch (UsernameNotFoundException $e) {}
        } elseif ($securityIdentity instanceof RoleSecurityIdentity) {
            
            return $securityIdentity->getRole();
        }

        return null;
    }
    
    /**
     * Compare ACLs
     * 
     * @param  MutableAclInterface  $acl
     * @param  array                $acl_array
     * @return array
     */
    protected function diffACL(MutableAclInterface $acl, array $acl_array)
    {
        $insert = $acl_array;
        $update = array();
        $delete = array();
        /**
         * @var integer $index
         * @var EntryInterface $ace
         */
        foreach ($acl->getObjectAces() as $index => $ace) {
            $identity = $this->resolveIdentity($ace);
            $mask = $ace->getMask();
            $found = false;
            foreach ($acl_array as $key => $acl_entry) {
                if ($acl_entry['identity'] == $identity) {
                    $found = true;
                    if ($acl_entry['permission'] != $mask) {
                        $update[$index] = $acl_entry;
                    }
                    unset($insert[$key]);
                }
            }
            if (! $found) {
                $delete[$index] = array('identity' => $identity, 'permission' => $mask);
            }
        }
        
        return array('insert' => $insert, 'update' => $update, 'delete' => $delete);
    }
    
    /**
     * Update ACL entries
     * 
     * @param MutableAclInterface  $acl
     * @param array                $update
     */
    protected function updateAclEntries(MutableAclInterface $acl, array $update)
    {
        foreach ($update as $index => $entry) {
            $acl->updateObjectAce($index, $entry['permission']);
        }
    }
    
    /**
     * Delete ACL entries
     * 
     * @param MutableAclInterface  $acl
     * @param array                $delete
     */
    protected function deleteAclEntries(MutableAclInterface $acl, array $delete)
    {
        foreach (array_reverse($delete, true) as $index => $entry) {
            $acl->deleteObjectAce($index);
        }
    }
    
    /**
     * Insert ACL entries
     * 
     * @param MutableAclInterface  $acl
     * @param array                $insert
     */
    protected function insertAclEntries(MutableAclInterface $acl, array $insert)
    {
        foreach ($insert as $entry) {
            $identity = $entry['identity'];
            $permission = $entry['permission'];
            if ($identity instanceof UserInterface) {
                $identity = UserSecurityIdentity::fromAccount($identity);
            } elseif (is_string($identity)) {
                $identity = new RoleSecurityIdentity($identity);
            }
            $acl->insertObjectAce($identity, $permission);
        }
    }
    
    /**
     * Get default ACL
     * 
     * @param  object         $object
     * @param  UserInterface  $user
     * @return array
     */
    public function getDefaultACL($object = null, $user = null)
    {
        $user = (null === $user) ? $this->getUser() : $user;
        $acl = array();
        
        if (null !== $user) {
            $acl[] = array('identity' => $user,
                           'permission' => MaskBuilder::MASK_OWNER);
        }
        
        $acl[] = array('identity' => 'ROLE_USER',
                       'permission' => MaskBuilder::MASK_VIEW);
        
        return $acl;
    }
}
