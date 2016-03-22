<?php

/*
 * This file is part of the BluemesaAclBundle.
 * 
 * Copyright (c) 2016 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bluemesa\Bundle\AclBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

use Bluemesa\Bundle\CoreBundle\Controller\CRUDController;
use Bluemesa\Bundle\CoreBundle\Form\AclType;


/**
 * Base class for secure CRUD operations
 *
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
abstract class SecureCRUDController extends CRUDController
{
    use SecureController;

    /**
     * {@inheritdoc}
     *
     * @Route("/")
     * @Route("/list/{filter}")
     * @Template()
     * @Secure(roles="ROLE_USER, ROLE_ADMIN")
     */
    public function listAction(Request $request)
    {
        return parent::listAction($request);
    }
    
    /**
     * {@inheritdoc}
     *
     * @Route("/show/{id}")
     * @Template()
     */
    public function showAction($id)
    {
        $entity = $this->getEntity($id);
        $this->verifyPermission($entity, 'VIEW');
        $om = $this->getObjectManager();
        $owner = $om->getOwner($entity);
        
        return array_merge(parent::showAction($entity), array('owner' => $owner));
    }

    /**
     * {@inheritdoc}
     *
     * @Route("/new")
     * @Template()
     * @Secure(roles="ROLE_USER, ROLE_ADMIN")
     */
    public function createAction(Request $request)
    {
        return parent::createAction($request);
    }

    /**
     * {@inheritdoc}
     * 
     * @Route("/edit/{id}")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $this->verifyPermission($entity, 'EDIT');
        
        return parent::editAction($request, $entity);
    }

    /**
     * {@inheritdoc}
     *
     * @Route("/delete/{id}")
     * @Template()
     */
    public function deleteAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $this->verifyPermission($entity, 'DELETE');
        
        return parent::deleteAction($request, $entity);
    }

    /**
     * Edit entity permissions
     *
     * @Route("/permissions/{id}")
     * @Template()
     *
     * @param  mixed                                      $id
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function permissionsAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $this->verifyPermission($entity, 'MASTER');
        $om = $this->getObjectManager();
        $data = $this->splitAcl($om->getACL($entity));        
        $form = $this->createForm(new AclType(), $data);
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                $acl_array = array_merge($data['user_acl'], $data['role_acl']);
                $om->updateACL($entity, $acl_array);
                $message = 'Changes to ' . $this->getEntityName() . ' ' . $entity . ' permissions were saved.';
                $this->addSessionFlash('success', $message);
                $route = str_replace("_permissions", "_show", $request->attributes->get('_route'));
                $url = $this->generateUrl($route, array('id' => $entity->getId()));

                return $this->redirect($url);
            }
        }

        return array('form' => $form->createView(), 'entity' => $entity);
    }
    
    /**
     * Verify that user has permission on entity
     * 
     * @param type $entity
     * @param type $permission
     * @throws Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function verifyPermission($entity, $permission)
    {
        $securityContext = $this->getSecurityContext();
        if (!($securityContext->isGranted('ROLE_ADMIN')||$securityContext->isGranted($permission, $entity))) {
            throw new AccessDeniedException();
        }
    }
    
    /**
     * Merge user and group ACLs into single ACL
     * 
     * @param array $acl_array
     * @return array
     */
    protected function splitAcl($acl_array)
    {
        $data = array(
            'user_acl' => array(),
            'role_acl' => array()
        );
                
        foreach($acl_array as $acl_entry) {
            $identity = $acl_entry['identity'];
            if ($identity instanceof UserInterface) {
                $data['user_acl'][] = $acl_entry;
            } else if (is_string($identity)) {
                $data['role_acl'][] = $acl_entry;
            }
        }
        
        return $data;
    }
}
