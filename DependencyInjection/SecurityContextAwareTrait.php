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
use Symfony\Component\Security\Core\SecurityContextInterface;


/**
 * SecurityContextAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait SecurityContextAwareTrait {
    
    /**
     * @var Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;
    
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
     * Get security context
     *
     * @return Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected function getSecurityContext()
    {
        return $this->securityContext;
    }
}
