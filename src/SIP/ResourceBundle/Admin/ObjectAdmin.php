<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\Admin as BaseAdmin;

use SIP\ResourceBundle\Entity\Object;
use SIP\ResourceBundle\Entity\Media\ObjectHasMedia;
use SIP\ResourceBundle\Entity\Media\Media;
use Genemu\Bundle\FormBundle\Gd\File\Image;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ObjectAdmin extends BaseAdmin
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
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
    }

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
        $collection->remove('acl');
        $collection->add('move', $this->getRouterIdParameter() . '/move/{position}');

        $collection->add('toSite', $this->getRouterIdParameter().'/toSite');

    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $loc = $this->getEntityManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Locality')->createQueryBuilder('l');
        $loc->orderBy('l.name','ASC');
        $vil = $this->getEntityManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Village')->createQueryBuilder('l');
        $vil->orderBy('l.name','ASC');

        if ($this->isGranted('ROLE_SIP_RESOURCE_OBJECT_ADMIN_ADMIN')) {
            $formMapper
                ->tab('General')
                    ->with('Publish')
                        ->add('publish', null, array(
                            'label' => false,
                            'attr' => array(
                                'class' => 'form-control'
                            )
                        ))
                    ->end()
                ->end();
        }

        $formMapper
            ->tab('General')
                ->with('General')
                    ->add('name', null, array(
                        'label' => 'sip_object_name',
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                    ->add('type', 'genemu_jqueryselect2_choice', array(
                        'label' => 'sip_object_type',
                        'attr' => array(
                            'class' => 'form-control article_type',
                        ),
                        'choices' => Object::getAdminTypes()
                    ))
                    ->add('estateType', 'genemu_jqueryselect2_choice', array(
                        'label' => 'sip_object_estateType',
                        'attr' => array(
                            'class' => 'form-control article_type',
                        ),
                        'choices' => Object::getEstateTypes()
                    ))
                    ->add('dealType', 'genemu_jqueryselect2_choice', array(
                        'label' => 'sip_object_dealType',
                        'attr' => array(
                            'class' => 'form-control article_type',
                        ),
                        'choices' => Object::getDealTypes()
                    ))
                    ->add('showImage', 'show_sonata_image', array(
                            'label' => 'sip_image',
                            'class' => 'SIP\ResourceBundle\Entity\Media\Media'
                        )
                    )
                    ->add('image', 'sonata_type_model_list', array(
                            'label' => 'sip_object_image',
                            'required' => false,
                            'attr' => array(
                                'class' => 'form-control'
                            )
                        ),
                        array(
                            'link_parameters' => array(
                                'context' => 'object',
                                'provider' => 'sonata.media.provider.image'
                            )
                        )
                    )
                    ->add('selection', null, array(
                        'label' => 'sip_object_selection',
                        'required' => false,
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                ->end()
                ->with('Price')
                    ->add('priceFrom', null, array(
                        'label' => 'sip_object_priceFrom',
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                    ->add('priceTo', null, array(
                        'label' => 'sip_object_priceTo',
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                    ->add('currency', null, array(
                        'label' => 'sip_object_currency',
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                ->end()
                ->with('Gallery')
                    ->add('galleryMultiple', 'multiple_upload_file', array(
                            'label'            => 'sip_object_galleryMultiple',
                            'maxNumberOfFiles' => 20,
                            'loadHistory'      => false,
                            'multiple'         => true
                        )
                    )
                    ->add('gallery', 'sonata_type_collection', array(
                            'label'              => 'sip_object_gallery',
                            'cascade_validation' => true,
                            'by_reference'       => false,
                            'required'           => false,
                            'attr'               => array(
                                'class' => 'form-control'
                            )
                        ), array(
                            'edit'         => 'inline',
                            'inline'       => 'table',
                            'sortable'     => 'position',
                            'admin_code'   => 'sip.resource.objecthasmedia.admin',
                        )
                    )
                ->end()
            ->end()
            ->tab('Position')
                ->with('General')
                    ->add('locality', null, array(
                        'label' => 'sip_object_locality',
                        'attr' => array(
                            'class' => 'form-control local-select-admin'
                        ),
                        'query_builder' => $loc
                    ))
                    ->add('village', null, array(
                        'label' => 'sip_object_village',
                        'required' => false,
                        'attr' => array(
                            'class' => 'form-control vill-select-admin'
                        ),
                        'query_builder' => $vil
                    ))
                    ->add('coordinates', 'location_picker', array(
                        'label' => 'sip_object_coordinates',
                        'findButtonClass' => 'btn',
                        'required' => false
                    ))
                    ->add('distance', null, array(
                        'label' => 'sip_object_distance',
                        'attr' => array(
                            'class' => 'form-control'
                        )
                    ))
                ->end()
            ->end()
            ->tab('Additionally')
                ->with('General')
                    ->add('house', null, array('label' => 'sip_object_house'))
                    ->add('area', null, array('label' => 'sip_object_area'))
                    ->add('addInfo', 'ckeditor', array(
                            'label' => 'sip_object_addInfo',
                            'config' => array(
                                'htmlEncodeOutput' => false,
                                'entities'         => false,
                                'allowedContent'   => true
                            )
                        )
                    )
                    ->add('landInfo', 'ckeditor', array(
                            'label' => 'sip_object_landInfo',
                            'config' => array(
                                'htmlEncodeOutput' => false,
                                'entities'         => false,
                                'allowedContent'   => true
                            )
                        )
                    )
                    ->add('layout', 'ckeditor', array(
                            'label' => 'sip_object_layout',
                            'config' => array(
                                'htmlEncodeOutput' => false,
                                'entities'         => false,
                                'allowedContent'   => true
                            )
                        )
                    )
                    ->add('communication', 'ckeditor', array(
                            'label' => 'sip_object_communication',
                            'config' => array(
                                'htmlEncodeOutput' => false,
                                'entities'         => false,
                                'allowedContent'   => true
                            )
                        )
                    )
                    ->add('printInfo', 'ckeditor', array(
                            'label' => 'sip_object_printInfo',
                            'config' => array(
                                'htmlEncodeOutput' => false,
                                'entities'         => false,
                                'allowedContent'   => true
                            )
                        )
                    )
            ->end()
                ->with('SEO')
                    ->add('title', null, array('label' => 'sip_seo_title'))
                    ->add('description', null, array('label' => 'sip_seo_description'))
                    ->add('keywords', null, array('label' => 'sip_seo_keywords'))
                ->end()
                ->with('Options')
                    ->add('isForest', null, array('label' => 'sip_object_isForest'))
                    ->add('isNearWater', null, array('label' => 'sip_object_isNearWater'))
                    ->add('isFull', null, array('label' => 'sip_object_isFull'))
                    ->add('isUnderFinish', null, array('label' => 'sip_object_isUnderFinish'))
                    ->add('isFurnished', null, array('label' => 'sip_object_isFurnished'))
                    ->add('isSecurity', null, array('label' => 'sip_object_isSecurity'))
                    ->add('isHasPool', null, array('label' => 'sip_object_isHasPool'))
                    ->add('isUndeveloped', null, array('label' => 'sip_object_isUndeveloped'))
                ->end()
            ->end()
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, array('label' => 'ID'))
            ->addIdentifier('image', null, array(
                'label' => 'sip_image',
                'template' => 'SonataMediaBundle:MediaAdmin:list_image.html.twig'
            ))
            ->addIdentifier('name', null, array('label' => 'sip_object_name'))
            ->add('publish', null, array('label' => 'sip_object_publish', 'editable' => true))
            ->add('locality', null, array('label' => 'sip_object_locality'))
            ->add('village', null, array('label' => 'sip_object_village'))
            ->add('type', null,
                array('template' => 'SIPResourceBundle:Admin:list_choice.html.twig',
                    'choices' => Object::getTypes(),
                    'label' => 'sip_object_type'
                )
            )
            ->add('dealType', null,
                array('template' => 'SIPResourceBundle:Admin:list_choice.html.twig',
                    'choices' => Object::getDealTypes(),
                    'label' => 'sip_object_dealType'
                )
            )
            ->add('_action', 'actions', array('actions' => array(
                'edit' => array(),
                'delete' => array(),
                'move'   => array('template' => 'PixSortableBehaviorBundle:Default:_sort.html.twig'),
                'toSite' => array('template' => 'SIPResourceBundle:Admin:list__action_toSite.html.twig')
            )))
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $loc = $this->getEntityManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Locality')->createQueryBuilder('l');
        $loc->orderBy('l.name','ASC');
        $vil = $this->getEntityManager()->getRepository('SIP\ResourceBundle\Entity\Lists\Village')->createQueryBuilder('l');
        $vil->orderBy('l.name','ASC');

        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add('name', null, array('label' => 'sip_object_name'))
            ->add('publish', null, array('label' => 'sip_object_publish'))
            ->add('locality', null, array('label' => 'sip_object_locality',
                'field_options' => array(
                    'query_builder' => $loc
                )
            ))
            ->add('village', null, array('label' => 'sip_object_village',
                'field_options' => array(
                    'query_builder' => $vil
                )
            ))
            ->add('type', null, array('label' => 'sip_object_type','field_options' => array('choices' => Object::getTypes()), 'field_type' => 'choice'))
            ->add('dealType', null, array('label' => 'sip_object_dealType','field_options' => array('choices' => Object::getDealTypes()), 'field_type' => 'choice'))
            ->add('estateType', null, array('label' => 'sip_object_estateType','field_options' => array('choices' => Object::getEstateTypes()), 'field_type' => 'choice'))
            ->add('isForest', null, array('label' => 'sip_object_isForest'))
            ->add('isNearWater', null, array('label' => 'sip_object_isNearWater'))
            ->add('isFull', null, array('label' => 'sip_object_isFull'))
            ->add('isUnderFinish', null, array('label' => 'sip_object_isUnderFinish'))
            ->add('isFurnished', null, array('label' => 'sip_object_isFurnished'))
            ->add('isSecurity', null, array('label' => 'sip_object_isSecurity'))
        ;
    }

    /**
     * @param \SIP\ResourceBundle\Entity\Object $object
     * @return mixed
     */
    public function preUpdate($object)
    {
        $this->createMedias($object);
    }

    /**
     * @param \SIP\ResourceBundle\Entity\Object $object
     * @return mixed
     */
    public function postPersist($object)
    {
        $this->createMedias($object);
    }

    public function createMedias(Object $object)
    {
        if ($object->getGalleryMultiple()) {
            foreach ($object->getGalleryMultiple() as $mediaItem) {
                $objectHasMedia = new ObjectHasMedia();
                $media = $this->getMediaByImage($mediaItem);
                $objectHasMedia->setImage($media);
                $objectHasMedia->setObject($object);
                $this->getEntityManager()->persist($objectHasMedia);
            }
        }
    }

    public function getMediaByImage(Image $image)
    {
        $media = new Media;
        $media->setName($image->getFilename());
        $media->setBinaryContent($image);
        $media->setProviderName('sonata.media.provider.image');
        $media->setContext('object');
        $this->container->get('sonata.media.manager.media')->save($media);

        $thumbnailPath = str_replace($image->getFilename(),
            'thumbnails/' . $image->getFilename(),
            $image->getRealPath());

        @unlink($image->getRealPath());
        @unlink($thumbnailPath);

        return $media;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->container->get('doctrine');
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->getDoctrine()->getManager();
        }

        return $this->em;
    }

    /**
     * @param \Pix\SortableBehaviorBundle\Services\PositionHandler $positionHandler
     */
    public function setPositionService(\Pix\SortableBehaviorBundle\Services\PositionHandler $positionHandler)
    {
        $this->positionService = $positionHandler;
    }
}