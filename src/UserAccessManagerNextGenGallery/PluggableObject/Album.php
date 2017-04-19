<?php

namespace UserAccessManagerNextGenGallery\PluggableObject;

use UserAccessManager\Config\Config;
use UserAccessManager\ObjectHandler\PluggableObject;
use UserAccessManager\UserGroup\UserGroup;
use UserAccessManagerNextGenGallery\Wrapper\NextGenGallery;

class Album extends PluggableObject
{
    const OBJECT_TYPE = 'ngg_album';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var NextGenGallery
     */
    private $nextGenGallery;

    /**
     * NextGenGalleryAlbum constructor.
     *
     * @param Config         $config
     * @param NextGenGallery $nextGenGallery
     */
    public function __construct(Config $config, NextGenGallery $nextGenGallery)
    {
        parent::__construct(self::OBJECT_TYPE);
        $this->config = $config;
        $this->nextGenGallery = $nextGenGallery;
    }

    /**
     * Returns the type name.
     *
     * @param string $objectId
     *
     * @return string
     */
    public function getObjectName($objectId)
    {
        $album = $this->nextGenGallery->getAlbum($objectId);
        return is_object($album) ? $album->name : '';
    }

    /**
     * Returns all albums assigned to the group.
     *
     * @param UserGroup $userGroup
     *
     * @return array
     */
    public function getFullObjects(UserGroup $userGroup)
    {
        return $userGroup->getAssignedObjects(Image::OBJECT_TYPE);
    }

    /**
     * Returns the recursive membership for the ngg album.
     *
     * @param UserGroup $userGroup
     * @param string    $objectId
     *
     * @return array
     */
    public function getRecursiveMembership(UserGroup $userGroup, $objectId)
    {
        return [];
    }
}
