<?php

/*
 * This file is part of the BluemesaCoreBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Bluemesa\Bundle\CoreBundle\Validator\Constraints\UniqueOwnerIdentity;
use Bluemesa\Bundle\CoreBundle\Validator\Constraints\UniqueIdentities;

/**
 * AclType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "acl";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user_acl', 'collection', array(
                        'type' => new UserAceType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'show_legend' => false,
                        'label' => 'Users',
                        'widget_add_btn' => array('label' => false, 'icon' => 'plus'),
                        'options' => array(
                            'label' => false,
                            'widget_remove_btn' => array('label' => false, 'icon' => 'times')),
                        'constraints' => array(
                            new UniqueOwnerIdentity('Only one user can be the owner.'),
                            new UniqueIdentities('Each user can be specified only once.'))))
                ->add('role_acl', 'collection', array(
                        'type' => new RoleAceType(),
                        'allow_add' => true,
                        'allow_delete' => true,
                        'prototype' => true,
                        'show_legend' => false,
                        'label' => 'Groups',
                        'widget_add_btn' => array('label' => false, 'icon' => 'plus'),
                        'options' => array(
                            'label' => false,
                            'widget_remove_btn' => array('label' => false, 'icon' => 'times')),
                        'constraints' => array(
                            new UniqueOwnerIdentity('Only one group can be the owner.'),
                            new UniqueIdentities('Each group can be specified only once.'))));
    }
}
