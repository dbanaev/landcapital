<?php

namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class FrendAdmin extends Admin
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
            ->add('created', null, array('label' => 'sip_frend_created'))
            ->add('email', null, array('label' => 'sip_frend_email'))
            ->add('body', null, array('label' => 'sip_frend_text'))
            ->add('subject', null, array('label' => 'sip_frend_subject'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('body', null, array('label' => 'sip_frend_text'))
            ->addIdentifier('email', null, array('label' => 'sip_frend_email'))
            ->add('subject', null, array('label' => 'sip_frend_subject'))
            ->add('created', null, array('label' => 'sip_frend_created'))
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
            ->add('subject', null, array('label' => 'sip_frend_subject'))
            ->add('email', null, array('label' => 'sip_frend_email'))
            ->add('created', 'genemu_jquerydate', array(
                'label' => 'sip_frend_created',
                'widget' => 'single_text'
                ))
            ->add('body', 'ckeditor', array(
                    'label' => 'sip_bid_text',
                    'config' => array(
                        'htmlEncodeOutput' => false,
                        'entities'         => false,
                        'allowedContent'   => true
                    )
                )
            )
        ;
    }
}
