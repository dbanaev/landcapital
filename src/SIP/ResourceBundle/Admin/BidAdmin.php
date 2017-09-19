<?php

namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class BidAdmin extends Admin
{
    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
        $collection->remove('acl');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('created', null, array('label' => 'sip_bid_created'))
            ->add('email', null, array('label' => 'sip_bid_email'))
            ->add('phone', null, array('label' => 'sip_bid_phone'))
            ->add('name', null, array('label' => 'sip_bid_name'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'sip_bid_name'))
            ->addIdentifier('email', null, array('label' => 'sip_bid_email'))
            ->add('object', null, array('label' => 'sip_bid_object'))
            ->add('created', null, array('label' => 'sip_bid_created'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
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
            ->add('name', null, array('label' => 'sip_bid_name'))
            ->add('email', null, array('label' => 'sip_bid_email'))
            ->add('phone', null, array('label' => 'sip_bid_phone'))
            ->add('object', null, array('label' => 'sip_bid_object'))
        ;
    }
}
