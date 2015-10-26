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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


/**
 * UserAceType class
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class UserAceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "user_ace";
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('identity', 'user_typeahead', array(
                        'horizontal' => false,
                        'label_render' => false,
                        'required'  => true,
                        'show_legend' => false,
                        'horizontal_input_wrapper_class' => '',
                        'widget_form_group_attr' => array('class' => 'col-sm-5'),
                        'error_bubbling' => true,
                        'widget_addon_prepend' => array(
                            'icon' => 'user',
                     )))
                ->add('permission', 'choice', array(
                        'horizontal' => false,
                        'label_render' => false,
                        'required'  => true,
                        'show_legend' => false,
                        'horizontal_input_wrapper_class' => '',
                        'error_bubbling' => true,
                        'widget_form_group_attr' => array('class' => 'col-sm-4'),
                        'choices' => array(
                            0 => 'None',
                            MaskBuilder::MASK_VIEW => 'View',
                            MaskBuilder::MASK_EDIT => 'Edit',
                            MaskBuilder::MASK_OPERATOR => 'Operator',
                            MaskBuilder::MASK_MASTER => 'Master',
                            MaskBuilder::MASK_OWNER => 'Owner',
                     )));
    }
}
