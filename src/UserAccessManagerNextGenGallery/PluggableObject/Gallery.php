<?php

namespace UserAccessManagerNextGenGallery\PluggableObject;

use UserAccessManager\Config\Config;
use UserAccessManager\ObjectHandler\PluggableObject;
use UserAccessManager\UserGroup\UserGroup;
use UserAccessManagerNextGenGallery\Wrapper\NextGenGallery;

class Gallery extends PluggableObject
{
    const OBJECT_TYPE = 'ngg_gallery';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var NextGenGallery
     */
    private $nextGenGallery;

    /**
     * NextGenGalleryGallery constructor.
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
        $gallery = $this->nextGenGallery->getGallery($objectId);
        return is_object($gallery) ? $gallery->title : '';
    }

    /**
     * Returns the assigned albums.
     *
     * @param int $objectId
     *
     * @return array
     */
    private function getAssignedAlbums($objectId)
    {
        $albums = [];
        $allAlbums = $this->nextGenGallery->getAllAlbums();

        foreach ($allAlbums as $key => $album) {
            $galleries = $album->sortorder;
            $galleriesMap = array_flip($galleries);

            if (isset($galleriesMap[$objectId]) === true) {
                $albums[$key] = $album;
            }
        }

        return $albums;
    }

    /**
     * Returns all galleries assigned to the group.
     *
     * @param UserGroup $userGroup
     *
     * @return array
     */
    public function getFullObjects(UserGroup $userGroup)
    {
        $galleries = $userGroup->getAssignedObjects(self::OBJECT_TYPE);
        $albums = $userGroup->getAssignedObjects(Album::OBJECT_TYPE);

        foreach ($albums as $albumId => $albumType) {
            $album = $this->nextGenGallery->getAlbum($albumId);
            $albumGalleries = $album->sortorder;
            $albumGalleriesMap = array_flip($albumGalleries);

            foreach ($albumGalleriesMap as $galleryId) {
                $galleries[$galleryId] = self::OBJECT_TYPE;
            }
        }

        return $galleries;
    }

    /**
     * Returns the recursive membership for the ngg gallery.
     *
     * @param UserGroup $userGroup
     * @param string    $objectId
     *
     * @return array
     */
    public function getRecursiveMembership(UserGroup $userGroup, $objectId)
    {
        $recursiveMemberShip = [];

        if ($this->config->lockRecursive() === true) {
            $albums = $this->getAssignedAlbums($objectId);

            foreach ($albums as $album) {
                if ($userGroup->isObjectMember(Album::OBJECT_TYPE, $album->id)) {
                    if (isset($recursiveMemberShip[Album::OBJECT_TYPE]) === false) {
                        $recursiveMemberShip[Album::OBJECT_TYPE] = [];
                    }

                    $recursiveMemberShip[Album::OBJECT_TYPE][$album->id] = $album->id;
                }
            }
        }

        return $recursiveMemberShip;
    }
}
