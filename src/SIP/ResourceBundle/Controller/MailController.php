<?php

/*
 * (c) Danil Banaev <status684@gmail.com>
 */

namespace SIP\ResourceBundle\Controller;

use SIP\ResourceBundle\Entity\Subscriber;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller {

    /**
     * @Route("/mail/subscribe", name="sip_resource_mail_subscribe")
     * @Template()
     * @return array
     */
    public function subscribeAction(Request $request) {
        if ($request->request->has('email')) {
            $subscriber = new Subscriber();
            $subscriber->setEmail($request->request->get('email'));
            
            try {
                $this->getDoctrine()->getManager()->persist($subscriber);
                $this->getDoctrine()->getManager()->flush();                

                $message = \Swift_Message::newInstance()
                        ->setSubject('Hello Email')
                        ->setFrom('send@example.com')
                        ->setTo('recipient@example.com')
                        ->setBody(
                        $this->renderView(
                                'SIPResourceBundle:Mail:subscribe.txt.twig', array('id' => $subscriber->getId())
                        )
                        )
                ;
                $this->get('mailer')->send($message);
                return new JsonResponse(
                        array('success' => true)
                );
            } catch (\Exception $e) {
                return new JsonResponse(array(
                    'error' => $e->getMessage()
                ));
            }
        }

        return new JsonResponse(array(
            'error' => 'Email is required param'
        ));
    }

    /**
     * @Route("/mail/subscribe/{id}", requirements={"id" = "\d+"}, name="sip_resource_mail_unsubscribe")
     * @Template()
     * @return array
     */
    public function unSubscribeAction($id) {
        $subscribe = $this->getDoctrine()->getManager()->find('SIP\ResourceBundle\Entity\Subscriber', $id);

        $mail = '';
        if ($subscribe) {
            $mail = $subscribe->getEmail();
            try {
                $this->getDoctrine()->getManager()->remove($subscribe);
                $this->getDoctrine()->getManager()->flush();
                return array(
                    'email' => $mail,
                    'success' => true
                );
            } catch (\Exception $e) {
                return array(
                    'email' => $mail,
                    'error' => $e->getMessage()
                );
            }
        } else {
            return array(
                'email' => $mail,
                'error' => 'Can not find email!'
            );
        }
    }

}
