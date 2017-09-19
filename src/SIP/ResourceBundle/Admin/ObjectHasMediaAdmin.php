<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\Admin as BaseAdmin;

class ObjectHasMediaAdmin extends BaseAdmin
{
    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
        $collection->remove('acl');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('image', null, array('label' => 'sip_image'))
            ->addIdentifier('position', null, array('label' => 'sip_position'))
        ;

        parent::configureListFields($listMapper);
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('showImage', 'show_sonata_image', array(
                'label' => 'sip_image',
                'class' => 'SIP\ResourceBundle\Entity\Media\Media'
            ))
            ->add('image', 'sonata_type_model_list', array(
                    'label'        => 'sip_image',
                    'by_reference' => false,
                ),
                array(
                    'link_parameters' => array(
                        'context' => 'object',
                        'provider' => 'sonata.media.provider.image'
                    )
                )
            )
            ->add('position', 'hidden',
                array(
                    'label'        => 'sip_position',
                )
            )
            ->end();
    }
}