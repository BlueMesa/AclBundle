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


/**
 * AclProviderAwareTrait
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
trait AclProviderAwareTrait {
    
    /**
     * @var Symfony\Component\Security\Acl\Model\MutableAclProviderInterface
     */
    protected $aclProvider;
    
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
}
