<?php

/*
 * (c) Danil Banaev <status684@gmail.com>
 */

namespace SIP\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SearchController extends Controller {

    /**
     * @var \SIP\SearchBundle\Services\Search\SphinxSearch
     */
    protected $search;

    /**
     * @var \Knp\Component\Pager\Paginator
     */
    protected $paginator;

    /**
     * @Route("/result", name="sip_search_result")
     * @Template()
     * @return array
     */
    public function searchResultsAction(Request $request)
    {
        $words   = $request->get('search');
        $page    = $request->get('page', 1);
        $limit = $request->get('limit', 20);
        $renderParams  = array( 'searchResults' => array(),
            'searchCount'   => 0,
            'searchQuery'   => $words);

        if(!empty($words)) {
            $this->setLimits($request->get('page', 1), $limit);
            $query = $this->getSphinxSearch()->getSphinx()->escapeString($words);
            $this->getSphinxSearch()->getSphinx()->SetMatchMode(SPH_MATCH_EXTENDED);
            $res = $this->getSphinxSearch()->getSphinx()->query($query, 'objectIndex');
            if (!empty($res['matches'])) {
                $renderParams['objects'] = $this->buildObjects($res);
                $renderParams['searchCount'] = $res['total'];
                $renderParams['current_page_number'] = $request->get('page', 1);
                $renderParams['num_items_per_page'] = $limit;
            }
        }

        $renderParams['pagination'] = $this->get('knp_paginator')->paginate($renderParams['searchResults'], $page, $limit);
        $renderParams['pagination']->setTotalItemCount($renderParams['searchCount']);
        return $renderParams;
    }

    /**
     * @param int $page
     * @param int $perPage
     */
    public function setLimits($page, $perPage)
    {
        $offset = (int)$page;
        if ($offset) {
            $offset = ($offset - 1) * $perPage;
        }

        $this->getSphinxSearch()->getSphinx()->setLimits($offset, $perPage);
    }

    /**
     * @param array $res
     */
    public function buildObjects($res) {
        $result = [];
        foreach ($res['matches'] as $match) {
            if (isset($match['attrs']['entity'])) {
                $object = $this->getObject($match['attrs']['entity'], $match['attrs']['object_id']);
                if ($object) {
                    $result[] = $object;
                }
            }
        }
        return $result;
    }

    /**
     * @param string $entity
     * @param string $objectId
     * @return \SIP\SearchBundle\Model\SearchInterface
     */
    public function getObject($entity, $objectId) {
        /** @var \Doctrine\ORM\EntityRepository $rep */
        $rep = $this->getDoctrine()->getRepository($entity);
        return $rep->findOneById($objectId);
    }

    /**
     * @return \SIP\SearchBundle\Services\Search\SphinxSearch
     */
    public function getSphinxSearch() {
        if (!$this->search) {
            $this->search = $this->container->get('search.sphinxsearch.search');
        }

        return $this->search;
    }

    /**
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    public function getSecurityContext() {
        return $this->container->get('security.context');
    }

}
