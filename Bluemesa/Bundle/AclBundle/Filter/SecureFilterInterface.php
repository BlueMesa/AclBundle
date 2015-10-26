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

/**
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
interface SecureFilterInterface {
    
    /**
     * Get access
     * 
     * @return string
     */
    public function getAccess();
    
    /**
     * Set access
     * 
     * @param string $access
     */
    public function setAccess($access);
    
    /**
     * Get permission array
     * 
     * @return array
     */
    public function getPermissions();
    
    /**
     * Get a user from the Security Context
     *
     * @return mixed
     */
    public function getUser();
}
