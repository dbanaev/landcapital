<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\ResourceBundle\Entity\Lists;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="SIP\ResourceBundle\Repository\SelectionRepository") 
 * @ORM\Table(name="sip_list_selection")
 */
class Selection
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Slug(fields={"name"})
     */
    protected $alias;

    /**
     * @ORM\ManyToMany(targetEntity="SIP\ResourceBundle\Entity\Object", mappedBy="selection")
     */
    protected $realty;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $keywords;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

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
     * Set name
     *
     * @param string $name
     * @return Selection
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->realty = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add realty
     *
     * @param \SIP\ResourceBundle\Entity\Object $realty
     * @return Selection
     */
    public function addRealty(\SIP\ResourceBundle\Entity\Object $realty)
    {
        $this->realty[] = $realty;

        return $this;
    }

    /**
     * Remove realty
     *
     * @param \SIP\ResourceBundle\Entity\Object $realty
     */
    public function removeRealty(\SIP\ResourceBundle\Entity\Object $realty)
    {
        $this->realty->removeElement($realty);
    }

    /**
     * Get realty
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRealty()
    {
        return $this->realty;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Selection
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Selection
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Selection
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Selection
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getName();
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Selection
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
}
