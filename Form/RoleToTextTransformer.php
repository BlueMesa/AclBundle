<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Form;

use Symfony\Component\Form\DataTransformerInterface;


/**
 * Transforms role into its string representation
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class RoleToTextTransformer implements DataTransformerInterface
{
    /**
     * Transform role into string value
     *
     * @param  string  $role
     * @return string
     */
    public function transform($role)
    {
        $value = ucfirst(strtolower(str_replace(array("ROLE_","_"), array(""," "), $role)));
        
        return $value;
    }

    /**
     * Transform string value into role
     *
     * @param  string  $value
     * @return string
     */
    public function reverseTransform($value)
    {
        $role = "ROLE_" . str_replace(" ", "_", strtoupper($value));
        
        return $role;
    }
}
