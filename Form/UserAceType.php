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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Validator\Constraints\Choice;


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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('identity', UserTypeaheadType::class, array(
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
                ->add('permission', ChoiceType::class, array(
                        'horizontal' => false,
                        'label_render' => false,
                        'required'  => true,
                        'show_legend' => false,
                        'horizontal_input_wrapper_class' => '',
                        'error_bubbling' => true,
                        'widget_form_group_attr' => array('class' => 'col-sm-4'),
                        'choices' => array(
                            'None'     => 0,
                            'View'     => MaskBuilder::MASK_VIEW,
                            'Edit'     => MaskBuilder::MASK_EDIT,
                            'Operator' => MaskBuilder::MASK_OPERATOR,
                            'Master'   => MaskBuilder::MASK_MASTER,
                            'Owner'    => MaskBuilder::MASK_OWNER,
                     )));
    }
}
