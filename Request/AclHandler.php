<?php

/*
 * This file is part of the ACL Bundle.
 * 
 * Copyright (c) 2017 BlueMesa LabDB Contributors <labdb@bluemesa.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Bluemesa\Bundle\AclBundle\Request;


use Bluemesa\Bundle\AclBundle\Doctrine\SecureObjectManagerInterface;
use Bluemesa\Bundle\AclBundle\Event\AclControllerEvents;
use Bluemesa\Bundle\AclBundle\Event\PermissionsActionEvent;
use Bluemesa\Bundle\AclBundle\Form\AclType;
use Bluemesa\Bundle\CoreBundle\Entity\Entity;
use Bluemesa\Bundle\CoreBundle\Request\AbstractHandler;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class AclHandler
 *
 * @DI\Service("bluemesa.acl.handler")
 *
 * @package Bluemesa\Bundle\AclBundle\Request
 * @author Radoslaw Kamil Ejsmont <radoslaw@ejsmont.net>
 */
class AclHandler extends AbstractHandler
{
    /**
     * This method calls a proper handler for the incoming request
     *
     * @param Request $request
     *
     * @return View
     * @throws \LogicException
     */
    public function handle(Request $request)
    {
        $action = $request->get('acl_action');
        switch($action) {
            case 'permissions':
                $result = $this->handlePermissionsAction($request);
                break;
            default:
                $message  = "The action '" . $action;
                $message .= "' is not one of the allowed CRUD actions ('index', 'show', 'new', 'edit', 'delete').";
                throw new \LogicException($message);
        }

        return $result;
    }

    /**
     * This method handles edit action requests.
     *
     * @param Request $request
     *
     * @return View
     */
    public function handlePermissionsAction(Request $request)
    {
        /** @var Entity $entity */
        $entity = $request->get('entity');
        $om = $this->registry->getManagerForClass($entity);

        dump($entity);
        dump($om);

        if (! $om instanceof SecureObjectManagerInterface) {
            throw new \LogicException("Permissions can only be modified in entities managed " .
                "by an instance of SecureObjectManagerInterface");
        }

        $form = $this->factory->create(AclType::class, $om->getACL($entity));

        $event = new PermissionsActionEvent($request, $entity, $form);
        $this->dispatcher->dispatch(AclControllerEvents::PERMISSIONS_INITIALIZE, $event);

        if (null !== $event->getView()) {
            return $event->getView();
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new PermissionsActionEvent($request, $entity, $form);
            $this->dispatcher->dispatch(AclControllerEvents::PERMISSIONS_SUBMITTED, $event);

            $om->updateACL($entity, $form->getData());

            $event = new PermissionsActionEvent($request, $entity, $form, $event->getView());
            $this->dispatcher->dispatch(AclControllerEvents::PERMISSIONS_SUCCESS, $event);

            if (null === $view = $event->getView()) {
                $route = $request->get('permissions_redirect_route');
                $view = View::createRouteRedirect($route, array('id' => $entity->getId()));
            }

        } else {
            $view = View::create(array('entity' => $entity, 'form' => $form->createView()));
        }

        $event = new PermissionsActionEvent($request, $entity, $form, $view);
        $this->dispatcher->dispatch(AclControllerEvents::PERMISSIONS_COMPLETED, $event);

        dump($event->getView());

        return $event->getView();
    }
}
