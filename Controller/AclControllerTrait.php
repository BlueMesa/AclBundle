<?php

/*
 * This file is part of the ACL Bundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\AclBundle\Controller;


use Bluemesa\Bundle\AclBundle\Request\AclHandler;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

trait AclControllerTrait
{
    /**
     * @return AclHandler
     */
    public function getAclHandler()
    {
        if (! $this instanceof ContainerAwareTrait) {
            throw new \LogicException("Calling class be an instance of ContainerAwareTrait");
        }

        return $this->container->get('bluemesa.acl.handler');
    }
}
