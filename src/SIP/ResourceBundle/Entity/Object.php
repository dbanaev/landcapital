<?php

/*
 * (c) Danil Banaev <status684@gmail.com>
 */

namespace SIP\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="SIP\ResourceBundle\Repository\ObjectRepository") 
 * @ORM\Table(name="content_object")
 * @ORM\HasLifecycleCallbacks
 */
class Object
{
    const TYPE_HOUSE = 'house';
    const TYPE_TOWNHOUSE = 'townhouse';
    const TYPE_LAND = 'land';
    const TYPE_APARTAMENT = 'apartment';
    const TYPE_OFFICE = 'office';
    const TYPE_FLAT = 'flat';
    const TYPE_PENTHOUSE = 'penthouse';
    const TYPE_VILLA = 'villa';
    const TYPE_MANSION = 'mansion';
    const TYPE_AZS = 'azs';

    const DEAL_TYPE_SELL = 'sell';
    const DEAL_TYPE_RENT = 'rent';

    const ESTATE_TYPE_RESIDENTIAL = 'residential';
    const ESTATE_TYPE_CITY = 'city';
    const ESTATE_TYPE_COMMERCIAL = 'commercial';
    const ESTATE_TYPE_ABOARD = 'abroad';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="SIP\ResourceBundle\Entity\Lists\Selection", inversedBy="realty")
     * @ORM\JoinTable(name="sip_list_realty_selection",
     *      joinColumns={@ORM\JoinColumn(name="object_id", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="selection_id", referencedColumnName="id", nullable=false)}
     * )
     */
    protected $selection;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer", name="price_from")
     */
    protected $priceFrom;

    /**
     * @ORM\Column(type="integer", name="price_to")
     */
    protected $priceTo;

    /**
     * @ORM\ManyToOne(targetEntity="SIP\ResourceBundle\Entity\Media\Media",cascade={"persist"})
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    protected $image;

    protected $showImage;

    /**
     * @ORM\ManyToOne(targetEntity="SIP\ResourceBundle\Entity\Media\Media",cascade={"persist"})
     * @ORM\JoinColumn(name="second_image_id", referencedColumnName="id")
     */
    protected $secondImage;

    /**
     * @ORM\OneToMany(targetEntity="SIP\ResourceBundle\Entity\Media\ObjectHasMedia", mappedBy="object", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $gallery;
    protected $galleryMultiple;

    /**
     * @ORM\OneToMany(targetEntity="SIP\ResourceBundle\Entity\Bid", mappedBy="object", cascade={"persist"}, orphanRemoval=true)
     */
    protected $bids;

    /**
     * @ORM\ManyToOne(targetEntity="SIP\ResourceBundle\Entity\Lists\Locality",cascade={"persist"})
     * @ORM\JoinColumn(name="locality_id", referencedColumnName="id")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $locality;

    /**
     * @ORM\ManyToOne(targetEntity="SIP\ResourceBundle\Entity\Lists\Village",cascade={"persist"})
     * @ORM\JoinColumn(name="village_id", referencedColumnName="id", nullable=true)
     * @ORM\OrderBy({"name" = "ASC"})
     */
    protected $village;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $coordinates;

    /**
     * @ORM\ManyToOne(targetEntity="SIP\ResourceBundle\Entity\Lists\Currency",cascade={"persist"})
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id", nullable=false)
     */
    protected $currency;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $distance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $house;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $area;

    /**
     * @ORM\Column(type="text", name="add_info", nullable=true)
     */
    protected $addInfo;

    /**
     * @ORM\Column(type="text", name="land_info", nullable=true)
     */
    protected $landInfo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $layout;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $communication;

    /**
     * @ORM\Column(type="text", name="print_info", nullable=true)
     */
    protected $printInfo;

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
     * @ORM\Column(type="string", name="deal_type", nullable=true)
     */
    protected $dealType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="string", name="estate_type", nullable=true)
     */
    protected $estateType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @ORM\Column(type="boolean", name="is_forest", nullable=true)
     * 
     */
    protected $isForest;

    /**
     * @ORM\Column(type="boolean", name="is_near_water", nullable=true)
     */
    protected $isNearWater;

    /**
     * @ORM\Column(type="boolean", name="is_full", nullable=true)
     */
    protected $isFull;

    /**
     * @ORM\Column(type="boolean", name="is_under_finish", nullable=true)
     */
    protected $isUnderFinish;

    /**
     * @ORM\Column(type="boolean", name="is_furnished", nullable=true)
     */
    protected $isFurnished;

    /**
     * @ORM\Column(type="boolean", name="is_security", nullable=true)
     */
    protected $isSecurity;

    /**
     * @ORM\Column(type="boolean", name="is_has_pool", nullable=true)
     */
    protected $isHasPool;

    /**
     * @ORM\Column(type="boolean", name="is_undeveloped", nullable=true)
     */
    protected $isUndeveloped;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    protected $position;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $publish;

    /**
     * Constructor
     */
    public function __construct() {
        $this->gallery = new \Doctrine\Common\Collections\ArrayCollection();
        $this->selection = new \Doctrine\Common\Collections\ArrayCollection();
        $this->locality = new \Doctrine\Common\Collections\ArrayCollection();
        $this->coordinates = '55.723213,37.28301';
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set id
     *
     * @return integer 
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Object
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set priceFrom
     *
     * @param integer $priceFrom
     * @return Object
     */
    public function setPriceFrom($priceFrom) {
        $this->priceFrom = $priceFrom;

        return $this;
    }

    /**
     * Get priceFrom
     *
     * @return integer 
     */
    public function getPriceFrom() {
        return $this->priceFrom;
    }

    /**
     * Set priceTo
     *
     * @param integer $priceTo
     * @return Object
     */
    public function setPriceTo($priceTo) {
        $this->priceTo = $priceTo;

        return $this;
    }

    /**
     * Get priceTo
     *
     * @return integer 
     */
    public function getPriceTo() {
        return $this->priceTo;
    }

    /**
     * Set currency
     *
     * @param integer $currency
     * @return Object
     */
    public function setCurrency($currency) {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return integer 
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * Set image
     *
     * @param \SIP\ResourceBundle\Entity\Media\Media $image
     * @return Object
     */
    public function setImage(\SIP\ResourceBundle\Entity\Media\Media $image = null) {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \SIP\ResourceBundle\Entity\Media\Media 
     */
    public function getImage() {
        return $this->image;
    }

    /**
     * Add gallery
     *
     * @param \SIP\ResourceBundle\Entity\Media\ObjectHasMedia $gallery
     * @return Object
     */
    public function addGallery(\SIP\ResourceBundle\Entity\Media\ObjectHasMedia $gallery) {
        $this->gallery[] = $gallery;

        return $this;
    }

    /**
     * Remove gallery
     *
     * @param \SIP\ResourceBundle\Entity\Media\ObjectHasMedia $gallery
     */
    public function removeGallery(\SIP\ResourceBundle\Entity\Media\ObjectHasMedia $gallery) {
        $this->gallery->removeElement($gallery);
    }

    /**
     * Get gallery
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGallery() {
        return $this->gallery;
    }

    public function getGalleryMultiple() {
        return $this->galleryMultiple;
    }

    public function setGalleryMultiple($galleryMultiple) {
        $this->galleryMultiple = $galleryMultiple;
    }

    /**
     * Set coordinates
     *
     * @param string $coordinates
     * @return Object
     */
    public function setCoordinates($coordinates) {
        $this->coordinates = $coordinates;

        return $this;
    }

    /**
     * Get coordinates
     *
     * @return string 
     */
    public function getCoordinates() {
        return $this->coordinates;
    }

    /**
     * Set distance
     *
     * @param integer $distance
     * @return Object
     */
    public function setDistance($distance) {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer 
     */
    public function getDistance() {
        return $this->distance;
    }

    /**
     * Set house
     *
     * @param integer $house
     * @return Object
     */
    public function setHouse($house) {
        $this->house = $house;

        return $this;
    }

    /**
     * Get house
     *
     * @return integer 
     */
    public function getHouse() {
        return $this->house;
    }

    /**
     * Set area
     *
     * @param integer $area
     * @return Object
     */
    public function setArea($area) {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return integer 
     */
    public function getArea() {
        return $this->area;
    }

    /**
     * Set addInfo
     *
     * @param string $addInfo
     * @return Object
     */
    public function setAddInfo($addInfo) {
        $this->addInfo = $addInfo;

        return $this;
    }

    /**
     * Get addInfo
     *
     * @return string 
     */
    public function getAddInfo() {
        return $this->addInfo;
    }

    /**
     * Set landInfo
     *
     * @param string $landInfo
     * @return Object
     */
    public function setLandInfo($landInfo) {
        $this->landInfo = $landInfo;

        return $this;
    }

    /**
     * Get landInfo
     *
     * @return string 
     */
    public function getLandInfo() {
        return $this->landInfo;
    }

    /**
     * Set layout
     *
     * @param string $layout
     * @return Object
     */
    public function setLayout($layout) {
        $this->layout = $layout;

        return $this;
    }

    /**
     * Get layout
     *
     * @return string 
     */
    public function getLayout() {
        return $this->layout;
    }

    /**
     * Set communication
     *
     * @param string $communication
     * @return Object
     */
    public function setCommunication($communication) {
        $this->communication = $communication;

        return $this;
    }

    /**
     * Get communication
     *
     * @return string 
     */
    public function getCommunication() {
        return $this->communication;
    }

    /**
     * Set printInfo
     *
     * @param string $printInfo
     * @return Object
     */
    public function setPrintInfo($printInfo) {
        $this->printInfo = $printInfo;

        return $this;
    }

    /**
     * Get printInfo
     *
     * @return string 
     */
    public function getPrintInfo() {
        return $this->printInfo;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Object
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Object
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Object
     */
    public function setKeywords($keywords) {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords() {
        return $this->keywords;
    }

    /**
     * Add selection
     *
     * @param \SIP\ResourceBundle\Entity\Lists\Selection $selection
     * @return Object
     */
    public function addSelection(\SIP\ResourceBundle\Entity\Lists\Selection $selection) {
        $this->selection[] = $selection;

        return $this;
    }

    /**
     * Remove selection
     *
     * @param \SIP\ResourceBundle\Entity\Lists\Selection $selection
     */
    public function removeSelection(\SIP\ResourceBundle\Entity\Lists\Selection $selection) {
        $this->selection->removeElement($selection);
    }

    /**
     * Get selection
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSelection() {
        return $this->selection;
    }

    /**
     * Set locality
     *
     * @param \SIP\ResourceBundle\Entity\Lists\Locality $locality
     * @return Object
     */
    public function setLocality(\SIP\ResourceBundle\Entity\Lists\Locality $locality = null) {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return \SIP\ResourceBundle\Entity\Lists\Locality 
     */
    public function getLocality() {
        return $this->locality;
    }

    /**
     * Set village
     *
     * @param \SIP\ResourceBundle\Entity\Lists\Village $village
     * @return Object
     */
    public function setVillage(\SIP\ResourceBundle\Entity\Lists\Village $village = null) {
        $this->village = $village;

        return $this;
    }

    /**
     * Get village
     *
     * @return \SIP\ResourceBundle\Entity\Lists\Village 
     */
    public function getVillage() {
        return $this->village;
    }

    public function setShowImage($image) {
        
    }

    public function getShowImage() {
        return $this->image;
    }

    /**
     * @return string
     */
    public function __toString() {
        return (string) $this->getName();
    }

    /**
     * Set dealType
     *
     * @param string $dealType
     * @return Object
     */
    public function setDealType($dealType) {
        $this->dealType = $dealType;

        return $this;
    }

    /**
     * Get dealType
     *
     * @return string 
     */
    public function getDealType() {
        return $this->dealType;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Object
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set estateType
     *
     * @param string $estateType
     * @return Object
     */
    public function setEstateType($estateType) {
        $this->estateType = $estateType;

        return $this;
    }

    /**
     * Get estateType
     *
     * @return string 
     */
    public function getEstateType() {
        return $this->estateType;
    }

    public static function getTypes() {
        return array(
            self::TYPE_HOUSE => 'Дом',
            self::TYPE_TOWNHOUSE => 'Таунхаус',
            self::TYPE_LAND => 'Участок',
            self::TYPE_FLAT => 'Квартира'
        );
    }
    
    public static function getAdminTypes() {
        return array(
            self::TYPE_HOUSE => 'Дом',
            self::TYPE_TOWNHOUSE => 'Таунхаус',
            self::TYPE_LAND => 'Участок',            
            self::TYPE_OFFICE => 'Офис',
            self::TYPE_FLAT => 'Квартира',
        );
    }

    public static function getEstateTypes() {
        return array(
            self::ESTATE_TYPE_RESIDENTIAL => 'Загородная',
            self::ESTATE_TYPE_CITY => 'Городская',
            self::ESTATE_TYPE_COMMERCIAL => 'Коммерческая',
            self::ESTATE_TYPE_ABOARD => 'Зарубежная'
        );
    }

    public static function getDealTypes() {
        return array(
            self::DEAL_TYPE_SELL => 'Продажа',
            self::DEAL_TYPE_RENT => 'Аренда'
        );
    }
    
    /**
     * Get Created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }
    
    /**
     * Set Created
     *
     * @param \DateTime $Created
     * @return Object
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
    
    /**
     * Get Updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    
    /**
     * Set Updated
     *
     * @param \DateTime $Updated
     * @return Object
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }


    /**
     * Set secondImage
     *
     * @param \SIP\ResourceBundle\Entity\Media\Media $secondImage
     * @return Object
     */
    public function setSecondImage(\SIP\ResourceBundle\Entity\Media\Media $secondImage = null)
    {
        $this->secondImage = $secondImage;

        return $this;
    }

    /**
     * Get secondImage
     *
     * @return \SIP\ResourceBundle\Entity\Media\Media 
     */
    public function getSecondImage()
    {
        return $this->secondImage;
    }

    /**
     * @ORM\PrePersist
     */
    public function PrePersist() {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();

        if (count($this->getGallery()) > 1) {
            foreach ($this->getGallery() as $gallery) {
                $this->setSecondImage($gallery->getImage());
            }
        } else {
            $this->setSecondImage($this->getImage());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function PreUpdate() {
        $this->updated = new \DateTime();
        if (count($this->getGallery()) > 1) {
            foreach ($this->getGallery() as $gallery) {
                $this->setSecondImage($gallery->getImage());
            }
        } else {
            $this->setSecondImage($this->getImage());
        }
    }

    /**
     * @return array
     */
    public static function getMinMaxValues()
    {
        return array(
            'min_distance' => 0,
            'max_distance' => 150,
            'min_area'  => 5,
            'max_area'  => 100,
            'min_house' => 50,
            'max_house' => 1000,
            'min_price' => 0,
            'max_price' => 10000000
        );
    }

    /**
     * Set isForest
     *
     * @param boolean $isForest
     * @return Object
     */
    public function setIsForest($isForest)
    {
        $this->isForest = $isForest;

        return $this;
    }

    /**
     * Get isForest
     *
     * @return boolean 
     */
    public function getIsForest()
    {
        return $this->isForest;
    }

    /**
     * Set isNearWater
     *
     * @param boolean $isNearWater
     * @return Object
     */
    public function setIsNearWater($isNearWater)
    {
        $this->isNearWater = $isNearWater;

        return $this;
    }

    /**
     * Get isNearWater
     *
     * @return boolean 
     */
    public function getIsNearWater()
    {
        return $this->isNearWater;
    }

    /**
     * Set isFull
     *
     * @param boolean $isFull
     * @return Object
     */
    public function setIsFull($isFull)
    {
        $this->isFull = $isFull;

        return $this;
    }

    /**
     * Get isFull
     *
     * @return boolean 
     */
    public function getIsFull()
    {
        return $this->isFull;
    }

    /**
     * Set isUnderFinish
     *
     * @param boolean $isUnderFinish
     * @return Object
     */
    public function setIsUnderFinish($isUnderFinish)
    {
        $this->isUnderFinish = $isUnderFinish;

        return $this;
    }

    /**
     * Get isUnderFinish
     *
     * @return boolean 
     */
    public function getIsUnderFinish()
    {
        return $this->isUnderFinish;
    }

    /**
     * Set isSecurity
     *
     * @param boolean $isSecurity
     * @return Object
     */
    public function setIsSecurity($isSecurity)
    {
        $this->isSecurity = $isSecurity;

        return $this;
    }

    /**
     * Get isSecurity
     *
     * @return boolean 
     */
    public function getIsSecurity()
    {
        return $this->isSecurity;
    }

    /**
     * Set isFurnished
     *
     * @param boolean $isFurnished
     * @return Object
     */
    public function setIsFurnished($isFurnished)
    {
        $this->isFurnished = $isFurnished;

        return $this;
    }

    /**
     * Get isFurnished
     *
     * @return boolean 
     */
    public function getIsFurnished()
    {
        return $this->isFurnished;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Object
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
     * Set publish
     *
     * @param boolean $publish
     * @return Object
     */
    public function setPublish($publish)
    {
        $this->publish = $publish;

        return $this;
    }

    /**
     * Get publish
     *
     * @return boolean 
     */
    public function getPublish()
    {
        return $this->publish;
    }

    /**
     * Set isHasPool
     *
     * @param boolean $isHasPool
     * @return Object
     */
    public function setIsHasPool($isHasPool)
    {
        $this->isHasPool = $isHasPool;

        return $this;
    }

    /**
     * Get isHasPool
     *
     * @return boolean 
     */
    public function getIsHasPool()
    {
        return $this->isHasPool;
    }

    /**
     * Set isUndeveloped
     *
     * @param boolean $isUndeveloped
     * @return Object
     */
    public function setIsUndeveloped($isUndeveloped)
    {
        $this->isUndeveloped = $isUndeveloped;

        return $this;
    }

    /**
     * Get isUndeveloped
     *
     * @return boolean 
     */
    public function getIsUndeveloped()
    {
        return $this->isUndeveloped;
    }

    /**
     * Add bids
     *
     * @param \SIP\ResourceBundle\Entity\Bid $bids
     * @return Object
     */
    public function addBid(\SIP\ResourceBundle\Entity\Bid $bids)
    {
        $this->bids[] = $bids;

        return $this;
    }

    /**
     * Remove bids
     *
     * @param \SIP\ResourceBundle\Entity\Bid $bids
     */
    public function removeBid(\SIP\ResourceBundle\Entity\Bid $bids)
    {
        $this->bids->removeElement($bids);
    }

    /**
     * Get bids
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBids()
    {
        return $this->bids;
    }
}
