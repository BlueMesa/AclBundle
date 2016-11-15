<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Bluemesa\Bundle\CoreBundle\Filter\ListFilterInterface;


/**
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SecureListFilter implements ListFilterInterface, SecureFilterInterface {

    /**
     * @var string
     */
    protected $access;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;


    /**
     * Construct SecureListFilter
     *
     * @param Request                        $request
     * @param AuthorizationCheckerInterface  $authorizationChecker
     * @param TokenStorageInterface          $tokenStorage
     */
    public function __construct(Request $request = null,
                                AuthorizationCheckerInterface $authorizationChecker = null,
                                TokenStorageInterface $tokenStorage = null)
    {
        $this->access = (null !== $request) ? $request->get('access', 'shared') : 'shared';
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAccess()
    {
        return $this->access;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }
        
    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        if ($this->access == 'private') {
            
            return array('OWNER');
            
        } elseif ($this->access == 'shared') {
            
            return array('OPERATOR');
            
        } elseif ($this->access == 'insecure') {
            
            return false;
            
        } else {
            if (null !== $this->authorizationChecker) {
                
                return $this->authorizationChecker->isGranted('ROLE_ADMIN') ? false : array('VIEW');
                
            } else {
                
                return array('VIEW');
            }
        } 
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        if ((null === $this->tokenStorage) || (null === $token = $this->tokenStorage->getToken())) {
            
            return;
        }

        if (!is_object($user = $token->getUser())) {
            
            return;
        }

        return $user;
    }
}
