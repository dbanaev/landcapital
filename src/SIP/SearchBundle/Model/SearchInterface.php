<?php
/*
 * (c) Danil Banaev <status684@gmail.com>
 */
namespace SIP\SearchBundle\Model;

interface SearchInterface
{
    /**
     * @abstract
     * @return string
     */
    public function getItem($object);

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getTitle();
}