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

use Bluemesa\Bundle\AclBundle\Doctrine\OwnedObjectManager;
use Bluemesa\Bundle\AclBundle\Doctrine\OwnedObjectManagerInterface;
use Bluemesa\Bundle\AclBundle\Doctrine\SecureObjectManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

use Bluemesa\Bundle\CoreBundle\Controller\CRUDController;
use Bluemesa\Bundle\AclBundle\Form\AclType;


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
    public function showAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $this->verifyPermission($entity, 'VIEW');
        /** @var SecureObjectManagerInterface|OwnedObjectManagerInterface $om */
        $om = $this->getObjectManager();
        if ($om instanceof OwnedObjectManager) {
            $owner = $om->getOwner($entity);
            $result = array_merge(parent::showAction($request, $entity), array('owner' => $owner));
        } else {
            $result = parent::showAction($request, $entity);
        }
        
        return $result;
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
     * @param  Request   $request
     * @param  mixed     $id
     * @return Response
     */
    public function permissionsAction(Request $request, $id)
    {

        $entity = $this->getEntity($id);
        $this->verifyPermission($entity, 'MASTER');
        /** @var SecureObjectManagerInterface|OwnedObjectManagerInterface $om */
        $om = $this->getObjectManager();
        $form = $this->createForm(AclType::class, $om->getACL($entity));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $om->updateACL($entity, $form->getData());
            $message = 'Changes to ' . $this->getEntityName() . ' ' . $entity . ' permissions were saved.';
            $this->addSessionFlash('success', $message);
            $route = str_replace("_permissions", "_show", $request->attributes->get('_route'));
            $url = $this->generateUrl($route, array('id' => $entity->getId()));

            return $this->redirect($url);
        }

        return array('form' => $form->createView(), 'entity' => $entity);
    }
    
    /**
     * Verify that user has permission on entity
     * 
     * @param  object                 $entity
     * @param  string                 $permission
     * @throws AccessDeniedException
     */
    protected function verifyPermission($entity, $permission)
    {
        if (!($this->isGranted('ROLE_ADMIN') ||
            $this->isGranted($permission, $entity))) {

            throw new AccessDeniedException();
        }
    }
}
