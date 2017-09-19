<?php
/*
 * (c) Sultanov Timur <info@sip-projects.com>
 */
namespace SIP\ResourceBundle\Entity\Media;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="SIP\ContentAbstractBundle\Repository\BaseContentRepository")
 * @ORM\Table(name="sip_object_has_media")
 * @ORM\HasLifecycleCallbacks
 */
class ObjectHasMedia
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="SIP\ResourceBundle\Entity\Object", inversedBy="gallery")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id")
     */
    protected $object;

    /**
     * @ORM\ManyToOne(targetEntity="\SIP\ResourceBundle\Entity\Media\Media", cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    protected $image;

    protected $showImage;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position;

    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param \SIP\ResourceBundle\Entity\Object $object
     * @return $this
     */
    public function setObject(\SIP\ResourceBundle\Entity\Object $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get product
     *
     * @return \SIP\ResourceBundle\Entity\Object
     */
    public function getObject()
    {
        return $this->object;
    }


    /**
     * Set image
     *
     * @param \SIP\ResourceBundle\Entity\Media\Media $image
     */
    public function setImage(\SIP\ResourceBundle\Entity\Media\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \SIP\ResourceBundle\Entity\Media\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    public function setShowImage($image) {}

    public function getShowImage()
    {
        return $this->image;
    }

    /**
     * @ORM\PostUpdate()
     */
    public function PostUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $event)
    {
        if ($this->getObject() === null) {
            $event->getEntityManager()->remove($this);
        }
    }
}
