<?php
/*
 * (c) JackDavyd
 */
namespace SIP\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use SIP\ResourceBundle\Form\Type\ContactType;
use SIP\ResourceBundle\Form\Type\FrendType;
use Symfony\Component\HttpFoundation\Cookie;
use SIP\ResourceBundle\Entity\Bid;
use SIP\ResourceBundle\Entity\Frend;

class ObjectController extends Controller
{
    /**
     * @Route("/_ajax/map/objects", name="sip_resource_ajax_map_object")
     * @Template()
     * @return array
     */
    public function ajaxMapObjectsAction(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Repository\ObjectRepository $repository */
        $repository = $em->getRepository('SIP\ResourceBundle\Entity\Object');
        /** @var \SIP\ResourceBundle\Entity\Object $object */
        $objects = $repository->search($request)->getQuery()->getResult();

        $map = [];
        foreach ($objects as $object) {
            if ($object->getCoordinates()) {
                if ($object->getDealType() == 'sell') {
                    switch ($object->getType()) {
                        case 'land':
                            $icon = 'land.png';
                            break;
                        default:
                            $icon = 'estate_gr.png';
                    }
                } else {
                    $icon = 'estate.png';
                }
                $latLngArr = explode(',', $object->getCoordinates());
                $latLng = array(
                    (float) $latLngArr[0],
                    (float) $latLngArr[1],
                );
                $map[] = [
                    'latLng' => $latLng,
                    'options' => array(
                        'icon' => "/bundles/sipresource/images/{$icon}",
                        'shadow' => '/bundles/sipresource/images/shadow.png'
                    ),
                    'data' => $this->render('SIPResourceBundle::mapMarker.hml.twig', array('data' => $object))->getContent()
                ];
            }
        }

        return new JsonResponse($map);
    }

    /**
     * @Route("residential-estate/{road}/{locality}/{id}", name="sip_resource_object")
     * @Template()
     * @return array
     */
    public function indexAction(Request $request)
    {
        $id = (int) $request->get('id');
        if (!$id) {
            throw $this->createNotFoundException('The product does not exist');
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \SIP\ResourceBundle\Entity\Object $qb */
        $qb = $em->getRepository('SIP\ResourceBundle\Entity\Object');

        $object = $qb->getСardObject((int) $id);

        $similar = $qb->similarObjects($object, 6);

        $bidModel = new Bid;
        $bidModel->setObject($object);
        $form = $this->createForm(new ContactType(), $bidModel);

        $frend = new Frend();
        $frend->setSubject($object->getName());
        $frendform = $this->createForm(new FrendType(), $frend);

        if ($request->isMethod('POST')) {
            if (isset($request->request->all()['contact'])) {
                if ($this->_sendForm($form, $request, $object)) {
                    if ((int) $request->get('ajaxMode') == 1) {
                        return new JsonResponse(['message' => 'Ваша заявка отправлена, с вами свяжутся.']);
                    } else {
                        $request->getSession()->getFlashBag()->add('success', 'Ваша заявка отправлена, с вами свяжутся.');
                    }
                }
            }

            if (isset($request->request->all()['frend'])) {
                if ($this->_sendFrendForm($frendform, $request, $object)) {
                    if ((int) $request->get('ajaxMode') == 2) {
                        return new JsonResponse(['message' => 'Письмо отправлено на ' . $frendform->get('email')->getData() . '.']);
                    } else {
                        $request->getSession()->getFlashBag()->add('success', 'Письмо отправлено.');
                    }
                }
            }
        }

        $fetured = count(json_decode($request->cookies->get('feature')));

        return ['object' => $object, 'similars' => $similar, 'form' => $form->createView(), 'fetured' => $fetured, 'frendform' => $frendform->createView()];
    }

    /**
     * @Route("/_ajax/featured/object", name="sip_resource_ajax_featured_object")
     * @return array
     */
    public function ajaxFeaturedObjectAction(Request $request)
    {
        $old = is_array(json_decode($request->cookies->get('feature'), true)) ? json_decode($request->cookies->get('feature'), true) : [];
        $id = $request->get('id');
        if ($id) {
            if (in_array($id, $old)) {
                $message = 'Этот обьект уже есть в избранном';
                return new JsonResponse(['count' => count($old), 'message' => $message]);
            }
            array_push($old, $id);
            $old = array_unique($old);
            $message = 'Обьект добавлен в избранное';
            $count = count(json_decode($request->cookies->get('feature'), true)) + 1;
            $response = new JsonResponse(['count' => $count, 'message' => $message]);
            $response->headers->setCookie(new Cookie('feature', json_encode($old)));
            return $response;
        }
        $message = 'Ошибка сохранения. Попробуйте позже.';
        $count = count(json_decode($request->cookies->get('feature'), true));
        return new JsonResponse(['count' => $count, 'message' => $message]);
    }

    /**
     * @Route("/_ajax/unfeatured/object", name="sip_resource_ajax_unfeatured_object")
     * @return array
     */
    public function ajaxUnfeaturedObjectAction(Request $request) {
        $old = is_array(json_decode($request->cookies->get('feature'), true)) ? json_decode($request->cookies->get('feature'), true) : [];
        $id = $request->get('id');
        if ($id) {
            $key = array_search($id, $old);
            if ($key !== null) {
                unset($old[$key]);
                $message = 'Обьект удален из избранного';
                $count = count(json_decode($request->cookies->get('feature'), true)) > 0 ? count(json_decode($request->cookies->get('feature'), true)) - 1 : 0;
                $response = new JsonResponse(['count' => $count, 'message' => $message]);
                $response->headers->setCookie(new Cookie('feature', json_encode($old)));
                return $response;
            } else {
                $message = 'Обьект не найден в избранном';
                $count = count(json_decode($request->cookies->get('feature'), true));
                return new JsonResponse(['count' => $count, 'message' => $message]);
            }
        }
        $message = 'Ошибка сохранения. Попробуйте позже.';
        $count = count(json_decode($request->cookies->get('feature'), true));
        return new JsonResponse(['count' => $count, 'message' => $message]);
    }

    /**
     * @Route("/favorites", name="sip_resource_object_favorites")
     * @Template()
     * @return array
     */
    public function favoritesAction(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \SIP\ResourceBundle\Repository\ObjectRepository $repository */
        $repository = $em->getRepository('SIP\ResourceBundle\Entity\Object');

        return['favorites' => $repository->favoritesObjects()];
    }

    /**
     * @Route("residential-estate/{road}/{locality}/{id}/print", name="sip_resource_object_print")
     * @Template()
     * @return array
     */
    public function printAction(Request $request)
    {
        $id = (int) $request->get('id');
        if (!$id) {
            throw $this->createNotFoundException('The product does not exist');
        }
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var \SIP\ResourceBundle\Entity\Object $qb */
        $qb = $em->getRepository('SIP\ResourceBundle\Entity\Object');

        $object = $qb->getСardObject((int) $id);

        return ['object' => $object];
    }

    /**
     * @param $form \SIP\ResourceBundle\Form\Type\ContactType
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return boolean
     */
    private function _sendForm($form, $request, $object)
    {
        $result = false;        
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
            $message = \Swift_Message::newInstance()
                    ->setSubject('Заявка на звонок')
                    ->setFrom($form->get('email')->getData())
                    ->setTo('contact@example.com')
                    ->setBody(
                    $this->renderView(
                            'SIPResourceBundle:Mail:contact.html.twig', array(
                        'name' => $form->get('name')->getData(),
                        'email' => $form->get('email')->getData(),
                        'object'=> $object
                            )
                    )
            );

            if ($this->get('mailer')->send($message)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @param $form \SIP\ResourceBundle\Form\Type\FrendType
     * @param $request \Symfony\Component\HttpFoundation\Request
     * @return boolean
     */
    private function _sendFrendForm($frendform, $request, $object)
    {
        $result = false;
        if ($frendform->bind($request) && $frendform->isValid()) {
            $tmpFileName = __DIR__ . "/../../../../app/cache/" . uniqid() . ".html";
            file_put_contents($tmpFileName, $this->renderView(
                            'SIPResourceBundle:Mail:frend.html.twig', array(
                        'object' => $object,
                            )
            ));

            $attachment = \Swift_Attachment::fromPath($tmpFileName);
            $message = \Swift_Message::newInstance()
                    ->setSubject($frendform->get('subject')->getData())
                    ->setFrom(array(
                        $this->container->getParameter('mail_from_email') => $this->container->getParameter('mail_from_email_name')
                    ))
                    ->setTo($frendform->get('email')->getData())
                    ->setBody($frendform->get('body')->getData())
            ;
            $message->attach($attachment);

            if ($this->get('mailer')->send($message)) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($frendform->getData());
                $em->flush();
                $result = true;
            }

            $transport = $this->container->get('mailer')->getTransport();
            $spool = $transport->getSpool();
            $spool->flushQueue($this->container->get('swiftmailer.transport.real'));

            unlink($tmpFileName);
        }
        return $result;
    }

}
