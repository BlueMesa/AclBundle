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

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

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
    /**
     * Class managed by this ObjectManager
     */
    const MANAGED_CLASS = 'Bluemesa\Bundle\AclBundle\Entity\SecureEntity';

    /**
     * @var Symfony\Component\Security\Core\User\UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var Symfony\Component\Security\Acl\Model\MutableAclProviderInterface
     */
    protected $aclProvider;
    
    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;
    
    /**
     * @var boolean
     */
    protected $isAutoAclEnabled;
    
    /**
     * Construct SecureObjectManager
     */
    public function __construct()
    {
        $this->isAutoAclEnabled = true;
    }
    
    /**
     * Set userProvider
     *
     * @DI\InjectParams({"userProvider" = @DI\Inject("user_provider")})
     * 
     * @param Symfony\Component\Security\Core\User\UserProviderInterface  $userProvider
     */
    public function setUserProvider(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }
    
    /**
     * Set aclProvider
     *
     * @DI\InjectParams({"aclProvider" = @DI\Inject("security.acl.provider")})
     * 
     * @param Symfony\Component\Security\Acl\Model\AclProviderInterface  $aclProvider
     */
    public function setAclProvider(MutableAclProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }
    
    /**
     * Set securityContext
     *
     * @DI\InjectParams({"securityContext" = @DI\Inject("security.context", required=false)})
     * 
     * @param Symfony\Component\Security\Core\SecurityContextInterface   $securityContext
     */
    public function setSecurityContext(SecurityContextInterface $securityContext = null)
    {
        $this->securityContext = $securityContext;
    }
    
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
                $diff = $this->diffACL($acl, $acl_array);
                $this->updateAclEntries($acl, $diff['update']);
                $this->deleteAclEntries($acl, $diff['delete']);
                $this->insertAclEntries($acl, $diff['insert']);
                $aclProvider->updateAcl($acl);
            } catch (AclNotFoundException $e) {
                $this->createACL($object, $acl_array);
            }
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getACL($object)
    {
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $aclProvider = $this->aclProvider;
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
            $acl_array = array();
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
     * {@inheritdoc}
     */
    public function enableAutoAcl()
    {
        $this->isAutoAclEnabled = true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function disableAutoAcl()
    {
        $this->isAutoAclEnabled = false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isAutoAclEnabled()
    {
        return $this->isAutoAclEnabled;
    }
    
    /**
     * Resolve ACE itentity to User or Role
     * 
     * @param type $ace
     * @return mixed
     */
    protected function resolveIdentity($ace)
    {
        $securityIdentity = $ace->getSecurityIdentity();
        if ($securityIdentity instanceof UserSecurityIdentity) {
            $userProvider = $this->userProvider;
            try {
                
                return $userProvider->loadUserByUsername($securityIdentity->getUsername());
                
            } catch (UsernameNotFoundException $e) {
                
                return null;
            }
        } elseif ($securityIdentity instanceof RoleSecurityIdentity) {
            
            return $securityIdentity->getRole();
        }
    }
    
    /**
     * Compare ACLs
     * 
     * @param type $acl
     * @param array $acl_array
     * @return array
     */
    protected function diffACL($acl, array $acl_array)
    {
        $insert = $acl_array;
        $update = array();
        $delete = array();
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
     * @param type $acl
     * @param array $update
     */
    protected function updateAclEntries($acl, array $update)
    {
        foreach ($update as $index => $entry) {
            $acl->updateObjectAce($index, $entry['permission']);
        }
    }
    
    /**
     * Delete ACL entries
     * 
     * @param type $acl
     * @param array $delete
     */
    protected function deleteAclEntries($acl, array $delete)
    {
        foreach (array_reverse($delete, true) as $index => $entry) {
            $acl->deleteObjectAce($index, $entry['permission']);
        }
    }
    
    /**
     * Insert ACL entries
     * 
     * @param type $acl
     * @param array $insert
     */
    protected function insertAclEntries($acl, array $insert)
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
     * Get user from the Security Context
     *
     * @throws \LogicException If SecurityBundle is not available
     * 
     * @return mixed
     */
    protected function getUser()
    {
        if (null === $this->securityContext) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->securityContext->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            return;
        }

        return $user;
    }
    
    /**
     * {@inheritdoc}
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
