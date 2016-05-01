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

use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Bluemesa\Bundle\CoreBundle\Form\EntityTypeaheadType;

/**
 * Bootstrap user typeahead form control
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 * 
 * @DI\FormType
 */
class UserTypeaheadType extends EntityTypeaheadType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults(array(
            'class' => 'VIB\UserBundle\Entity\User',
            'choice_label' => 'username',
            'data_route' => 'vib_user_ajax_choices'
        ));
    }
}
