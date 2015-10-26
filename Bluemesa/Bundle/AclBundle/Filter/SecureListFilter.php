<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class SecureListFilter implements ListFilterInterface, SecureFilterInterface {
    
    protected $access;
    
    protected $securityContext;
    
    /**
     * Construct SecureListFilter
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\Security\Core\SecurityContext $securityContext
     */
    public function __construct(Request $request = null, SecurityContextInterface $securityContext = null)
    {
        $this->access = (null !== $request) ? $request->get('access', 'shared') : 'shared';
        $this->securityContext = $securityContext;
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
            if (null !== $this->securityContext) {
                
                return $this->securityContext->isGranted('ROLE_ADMIN') ? false : array('VIEW');
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
        if ((null === $this->securityContext) || (null === $token = $this->securityContext->getToken())) {
            
            return;
        }

        if (!is_object($user = $token->getUser())) {
            
            return;
        }

        return $user;
    }
}
