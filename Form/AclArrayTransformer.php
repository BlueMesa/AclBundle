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
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Splits ACL array supplied by ObjectManager into user and role ACL arrays
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclArrayTransformer implements DataTransformerInterface
{
    /**
     * Splits ACL array into user and role ACL arrays
     *
     * @param  array  $value
     * @return array
     * @throws TransformationFailedException
     */
    public function transform($value)
    {
        $data = array(
            'user_acl' => array(),
            'role_acl' => array()
        );

        foreach($value as $acl_entry) {
            $identity = $acl_entry['identity'];
            if ($identity instanceof UserInterface) {
                $data['user_acl'][] = $acl_entry;
            } else if (is_string($identity)) {
                $data['role_acl'][] = $acl_entry;
            } else {
                throw new TransformationFailedException("Invalid ACL array supplied");
            }
        }

        return $data;
    }

    /**
     * Merges user and role ACL arrays into a single ACL array
     *
     * @param  array  $value
     * @return array
     * @throws TransformationFailedException
     */
    public function reverseTransform($value)
    {
        if ((! array_key_exists('user_acl', $value))||(! array_key_exists('role_acl', $value))) {
            throw new TransformationFailedException("Invalid ACL array supplied");
        }

        return array_merge($value['user_acl'], $value['role_acl']);
    }
}
