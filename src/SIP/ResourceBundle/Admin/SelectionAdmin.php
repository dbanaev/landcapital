<?php

namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SelectionAdmin extends Admin
{
    /**
     * @var int
     */
    public $last_position = 0;

    /**
     * @var \Pix\SortableBehaviorBundle\Services\PositionHandler
     */
    protected $positionService;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'position',
    );

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
        $collection->remove('acl');
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'sip_selection_name'))
            ->add('alias', null, array('label' => 'sip_selection_alias'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'sip_selection_name'))
            ->add('alias', null, array('label' => 'sip_selection_alias'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit'   => array(),
                    'delete' => array(),
                    'move'   => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('name', null, array('label' => 'sip_selection_name'))
                ->add('alias', null, array('label' => 'sip_selection_alias'))
            ->end()
            ->with('SEO')
                ->add('title', null, array('label' => 'sip_seo_title'))
                ->add('description', null, array('label' => 'sip_seo_description'))
                ->add('keywords', null, array('label' => 'sip_seo_keywords'))
            ->end()
        ;
    }

    /**
     * @param \Pix\SortableBehaviorBundle\Services\PositionHandler $positionHandler
     */
    public function setPositionService(\Pix\SortableBehaviorBundle\Services\PositionHandler $positionHandler)
    {
        $this->positionService = $positionHandler;
    }
}
