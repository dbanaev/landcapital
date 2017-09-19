<?php

namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class VillageAdmin extends Admin
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
            ->add('name', null, array('label' => 'sip_village_name'))
            ->add('alias', null, array('label' => 'sip_village_alias'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array('label' => 'sip_village_name'))
            ->add('locality', null, array('label' => 'sip_village_locality'))
            ->add('alias', null, array('label' => 'sip_village_alias'))
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
            ->with('General')
                ->add('locality', null, array(
                        'label' => 'sip_village_locality',
                        'required' => false,
                        'attr' => array(
                            'class' => 'form-control'
                        ))
                )
                ->add('name', null, array('label' => 'sip_village_name'))
                ->add('alias', null, array('label' => 'sip_village_alias'))
                ->add('text', 'ckeditor', array(
                        'label' => 'sip_village_text',
                        'config' => array(
                            'htmlEncodeOutput' => false,
                            'entities'         => false,
                            'allowedContent'   => true
                        )
                    )
                )
            ->add('coordinates', 'location_picker', array(
                    'label' => 'sip_village_coordinates',
                    'findButtonClass' => 'btn',
                    'required' => false
                ))
            ->end()
            ->with('SEO')
                ->add('title', null, array('label' => 'sip_seo_title'))
                ->add('description', null, array('label' => 'sip_seo_description'))
                ->add('keywords', null, array('label' => 'sip_seo_keywords'))
            ->end()
        ;
    }
}