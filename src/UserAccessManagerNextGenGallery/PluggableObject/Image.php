<?php

namespace UserAccessManagerNextGenGallery\PluggableObject;

use UserAccessManager\Config\Config;
use UserAccessManager\ObjectHandler\PluggableObject;
use UserAccessManager\UserGroup\UserGroup;
use UserAccessManagerNextGenGallery\Wrapper\NextGenGallery;

class Image extends PluggableObject
{
    const OBJECT_TYPE = 'ngg_image';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var NextGenGallery
     */
    private $nextGenGallery;

    /**
     * NextGenGalleryImage constructor.
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
        $image = $this->nextGenGallery->getImage($objectId);
        return is_object($image) ? $image->filename : '';
    }

    /**
     * Returns all images assigned to the group.
     *
     * @param UserGroup $userGroup
     *
     * @return array
     */
    public function getFullObjects(UserGroup $userGroup)
    {
        $images = $userGroup->getAssignedObjects(self::OBJECT_TYPE);
        $galleries = $userGroup->getAssignedObjects(Gallery::OBJECT_TYPE);
        $albums = $userGroup->getAssignedObjects('nggAlbum');

        foreach ($albums as $albumId => $albumType) {
            $albumPictures = $this->nextGenGallery->find_images_in_album($albumId);
        }

        foreach ($galleries as $galleryId => $galleryType) {
            $galleryImages = $this->nextGenGallery->getImagesByGallery($galleryId);
        }

        return $images;
    }

    /**
     * Returns the recursive membership for the ngg image.
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
            $image = $this->nextGenGallery->getImage($objectId);
            $isMember = $userGroup->isObjectMember(
                Gallery::OBJECT_TYPE,
                $image->galleryid,
                $recursiveMemberShip
            );

            if ($isMember === true) {
                if (count($recursiveMemberShip) === 0) {
                    $recursiveMemberShip[Gallery::OBJECT_TYPE] = [$image->galleryid => $image->galleryid];
                }

                $albums = [];
                $allAlbums = $this->nextGenGallery->getAllAlbums();

                foreach ($allAlbums as $key => $album) {
                    $galleries = $album->sortorder;
                    $galleriesMap = array_flip($galleries);

                    if (isset($galleriesMap[$image->galleryid]) === true) {
                        $albums[$key] = $album;
                    }
                }

                foreach ($albums as $album) {
                    if ($userGroup->isObjectMember(Album::OBJECT_TYPE, $album->id)) {
                        if (isset($recursiveMemberShip[Album::OBJECT_TYPE]) === false) {
                            $recursiveMemberShip[Album::OBJECT_TYPE] = [];
                        }

                        $recursiveMemberShip[Album::OBJECT_TYPE][$album->id] = $album->id;
                    }
                }
            }
        }

        return$recursiveMemberShip;
    }
}
