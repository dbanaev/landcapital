<?php

namespace SIP\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SitemapController extends Controller
{
    private $limit = 1000;

    /**
     * @Route("/sitemap.xml", name="sip_sitemap_index")
     */
    public function indexAction(Request $request)
    {
        $urls = array();

        $objectRep = $this->getDoctrine()->getRepository('SIP\ResourceBundle\Entity\Object');
        
        $objectCount = $objectRep->createQueryBuilder('c')
            ->select('count(c)')
            ->where('c.publish = 1')
            ->getQuery()
            ->getSingleScalarResult();
        $objectCount = ceil($objectCount / $this->limit);
        
        $sitemaps = array(
            'main' => 1,
            'object' => $objectCount ? $objectCount : 1,            
        );

        foreach($sitemaps as $slug => $page){
            for($i = 1; $i <= $page; $i++){
                $urls[] = array(
                    'loc' => $request->getSchemeAndHttpHost() . "/sitemap_{$slug}_{$i}.xml",
                    'lastmod' => (date('Y-m-d').'T'.date('H:i:s', strtotime('-4hours')).'+04:00'),
                );
            }

        }
        $response = new Response($this->renderView('SIPResourceBundle:Sitemap:sitemap_index.xml.twig', array('urls' => $urls)));
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }

    /**
     * @Route("/sitemap_{slug}_{page}.xml", name="sip_sitemap")
     */
    public function sitemapAction(Request $request, $slug, $page = 1)
    {
        $urls = array();
        $em = $this->getDoctrine()->getManager();

        switch($slug){
            case 'main':
                $urls[] = array(
                    'loc' => $this->container->get('router')->generate('sip_resource_main', array(), true),
                    'lastmod' => (date('Y-m-d').'T'.date('H:i:s', strtotime('-4hours')).'+04:00'),
                    'changefreq' => 'daily',
                    'priority' => 1
                );
                $urls[] = array(
                    'loc' => $this->container->get('router')->generate('sip_resource_main_about', array(), true),
                    'lastmod' => (date('Y-m-d').'T'.date('H:i:s', strtotime('-4hours')).'+04:00'),
                    'changefreq' => 'daily',
                    'priority' => 1
                );
                $urls[] = array(
                    'loc' => $this->container->get('router')->generate('sip_resource_main_contacts', array(), true),
                    'lastmod' => (date('Y-m-d').'T'.date('H:i:s', strtotime('-4hours')).'+04:00'),
                    'changefreq' => 'daily',
                    'priority' => 1
                );
                break;
            
            case 'object':
                $objects = $em->getRepository('SIP\ResourceBundle\Entity\Object')
                    ->createQueryBuilder('c')
                    ->select('c')
                    ->where('c.publish=1')
                    ->setMaxResults($this->limit)
                    ->setFirstResult($this->limit * ($page-1))
                    ->getQuery()
                    ->getResult();

                foreach($objects as $object){
                    if ($object->getLocality()) {
                        $urls[] = array(
                            'loc' => $request->getSchemeAndHttpHost() . '/residential-estate/' . $object->getLocality()->getRoad()->getAlias() . '/' . $object->getLocality()->getAlias() . '/' . $object->getId(),
                            'lastmod' => (date('Y-m-d') . 'T' . date('H:i:s', strtotime('-4hours')) . '+04:00'),
                            'changefreq' => 'daily',
                            'priority' => 0.8
                        );
                    }
                }
                break;            
            default:
                throw new NotFoundHttpException();
        }

        $response = new Response($this->renderView('SIPResourceBundle:Sitemap:sitemap.xml.twig', array('urls' => $urls)));
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }
}