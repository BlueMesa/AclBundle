<?php

/*
 * This file is part of the ACL Bundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\AclBundle\Controller\Annotations;


/**
 * Action Annotation
 *
 * @Annotation
 * @Target("METHOD")
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class Action
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $redirect_route;


    /**
     * Action Annotation constructor.
     */
    public function __construct()
    {
        $this->name = null;
        $this->redirect_route = null;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRedirectRoute()
    {
        return $this->redirect_route;
    }
}
