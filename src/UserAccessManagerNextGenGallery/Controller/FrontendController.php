<?php
namespace UserAccessManagerNextGenGallery\Controller;

use UserAccessManager\AccessHandler\AccessHandler;
use UserAccessManager\Config\Config;
use UserAccessManager\Controller\Controller;
use UserAccessManager\Wrapper\Php;
use UserAccessManager\Wrapper\Wordpress;
use UserAccessManagerNextGenGallery\Config\Config as NggConfig;
use UserAccessManagerNextGenGallery\PluggableObject\Album;
use UserAccessManagerNextGenGallery\PluggableObject\Gallery;
use UserAccessManagerNextGenGallery\PluggableObject\Image;

class FrontendController extends Controller
{
    /**
     * @var NggConfig
     */
    private $nggConfig;

    /**
     * @var AccessHandler
     */
    private $accessHandler;

    /**
     * FrontendController constructor.
     *
     * @param Php           $php
     * @param Wordpress     $wordpress
     * @param Config        $config
     * @param NggConfig     $nggConfig
     * @param AccessHandler $accessHandler
     */
    public function __construct(
        Php $php,
        Wordpress $wordpress,
        Config $config,
        NggConfig $nggConfig,
        AccessHandler $accessHandler
    ) {
        parent::__construct($php, $wordpress, $config);
        $this->nggConfig = $nggConfig;
        $this->accessHandler = $accessHandler;
    }

    /**
     * Checks the possible values for ids and returns the id if available.
     *
     * @param $object
     * @param $checkValues
     *
     * @return mixed|null
     */
    private function getPossibleValues($object, $checkValues)
    {
        $id = null;

        foreach ($checkValues as $checkValue) {
            if ($id === null && isset($object->{$checkValue}) === true && empty($object->{$checkValue}) === false) {
                $value = $object->{$checkValue};
                $id = (is_array($value) === true) ? reset($value) : $value;
            }
        }

        return $id;
    }

    /**
     * Manipulates the output of a gallery.
     *
     * @param string               $content The output.
     * @param \C_Displayed_Gallery $gallery The gallery.
     *
     * @return string
     */
    public function showGalleryContent($content, $gallery)
    {
        $objectType = Gallery::OBJECT_TYPE;
        $contentOptionName = 'gallery_content';

        if ($gallery->source === 'albums') {
            $objectType = Album::OBJECT_TYPE;
            $contentOptionName = 'album_content';
        }

        $galleryId = $this->getPossibleValues($gallery, ['id', 'gid', 'container_ids', 'gallery_ids']);

        if ($this->accessHandler->checkObjectAccess($objectType, $galleryId) === false) {
            $options = $this->nggConfig->getOptions();
            $content = $options[$contentOptionName];
        }

        $options = $this->nggConfig->getOptions();

        if ($objectType === Gallery::OBJECT_TYPE && $options['hide_image'] === 'true') {
            foreach ($gallery->get_included_entities() as $image) {
                if ($this->accessHandler->checkObjectAccess(Image::OBJECT_TYPE, $image->pid) === false) {
                    if (is_array($gallery->exclusions) === false) {
                        $gallery->exclusions = [];
                    }

                    $gallery->exclusions[] = $image->pid;
                }
            }
        }

        return $content;
    }

    /**
     * Returns the url for the object.
     *
     * @param string $url
     * @param string $objectType
     * @param null   $objectId
     *
     * @return string
     */
    private function getImageUrl($url, $objectType, $objectId = null)
    {
        //Manipulate preview image
        $sSuffix = "uamfiletype={$objectType}";
        $sSuffix .= ($objectId !== null) ? "&uamextra={$objectId}" : '';

        if ($this->config->isPermalinksActive() === false
            && $this->config->lockFile() === true
        ) {
            $sPrefix = $this->wordpress->getHomeUrl('/').'?uamgetfile=';
            $url = $sPrefix.$url.'&'.$sSuffix;
        } else {
            $url = $url.'?'.$sSuffix;
        }

        return $url;
    }

    /**
     * Manipulates the gallery for a album.
     *
     * @param object $gallery The gallery.
     *
     * @return object
     */
    public function showGalleryObjectForAlbum($gallery)
    {
        $options = $this->nggConfig->getOptions();

        //Manipulate gallery title
        if ($options['hide_gallery_title'] == 'true'
            && $this->accessHandler->checkObjectAccess(Gallery::OBJECT_TYPE, $gallery->gid) === false
        ) {
            $gallery->title = $options['gallery_title'];
        }

        //Manipulate preview image
        $gallery->previewurl = $this->getImageUrl(
            $gallery->previewurl,
            Gallery::OBJECT_TYPE,
            $gallery->gid
        );

        return $gallery;
    }

    /**
     * Filters the galleries.
     *
     * @param array $galleries The galleries of the album.
     *
     * @return array
     */
    public function showGalleriesForAlbum(array $galleries)
    {
        $options = $this->nggConfig->getOptions();

        if ($options['hide_gallery'] == 'true') {
            foreach ($galleries as $key => $gallery) {
                if ($this->accessHandler->checkObjectAccess(Gallery::OBJECT_TYPE, $gallery->gid) === false) {
                    unset($galleries[$key]);
                }
            }
        }

        return $galleries;
    }

    /**
     * Manipulates the output of a album.
     *
     * @param string  $content The output.
     * @param integer $albumId The album id.
     *
     * @return string
     */
    public function showAlbumContent($content, $albumId)
    {
        $options = $this->nggConfig->getOptions();

        if ($this->accessHandler->checkObjectAccess(Album::OBJECT_TYPE, $albumId) === false) {
            $content = $options['album_content'];
        }

        return $content;
    }

    /**
     * Manipulates the image url.
     *
     * @param object $image The image object.
     */
    public function loadImage(&$image)
    {
        $image->imageURL = $this->getImageUrl(
            $image->imageURL,
            Image::OBJECT_TYPE,
            $image->id
        );

        $image->thumbURL = $this->getImageUrl(
            $image->thumbURL,
            Image::OBJECT_TYPE,
            $image->id
        );
    }

    /**
     * @param string    $url
     * @param \stdClass $image
     *
     * @return string
     */
    public function returnImageUrl($url, $image)
    {
        return $this->getImageUrl(
            $url,
            Image::OBJECT_TYPE,
            $image->pid
        );
    }
}
