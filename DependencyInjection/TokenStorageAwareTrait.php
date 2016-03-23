<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\DependencyInjection;

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * TokenStorageAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait TokenStorageAwareTrait {
    
    /**
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected $tokenStorage;
    
    /**
     * Set securityContext
     *
     * @DI\InjectParams({"tokenStorage" = @DI\Inject("security.token_storage", required=false)})
     * 
     * @param \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface  $tokenStorage
     */
    public function setTokenStorage(TokenStorageInterface $tokenStorage = null)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    /**
     * Get security context
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    protected function getTokenStorage()
    {
        return $this->tokenStorage;
    }
}
