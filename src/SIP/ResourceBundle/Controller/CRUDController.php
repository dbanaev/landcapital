<?php

namespace SIP\ResourceBundle\Controller;

use Pix\SortableBehaviorBundle\Controller\SortableAdminController as Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class CRUDController extends Controller {

    public function toSiteAction()
    {
        $id = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        $locAlias = $object->getLocality()->getAlias();
        $roadAlias = $object->getLocality()->getRoad()->getAlias();

        return new RedirectResponse($this->generateUrl('sip_resource_object', array('road' => $roadAlias, 'locality' => $locAlias, 'id' => $id)));

    }
}