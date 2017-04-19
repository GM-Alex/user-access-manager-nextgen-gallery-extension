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
     * Manipulates the output of a gallery.
     *
     * @param string $output    The output.
     * @param int    $galleryId The gallery id.
     *
     * @return string
     */
    public function showGalleryContent($output, $galleryId)
    {
        $aOptions = $this->nggConfig->getOptions();

        if ($this->accessHandler->checkObjectAccess(Gallery::OBJECT_TYPE, $galleryId) === false) {
            $output = $aOptions['gallery_content'];
        }

        return $output;
    }

    /**
     * Filters the images.
     *
     * @param array $images The images of the gallery.
     *
     * @return array
     */
    public function showGalleryImages(array $images)
    {
        $options = $this->nggConfig->getOptions();

        if ($options['hide_image'] == 'true') {
            foreach ($images as $key => $image) {
                if ($this->accessHandler->checkObjectAccess(Image::OBJECT_TYPE, $image->pid) === false) {
                    unset($images[$key]);
                }
            }
        }

        return $images;
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
        $sSuffix = 'uamfiletype='.Image::OBJECT_TYPE;

        if ($this->config->isPermalinksActive() === false
            && $this->config->lockFile() === true
        ) {
            $sPrefix = $this->wordpress->getHomeUrl('/').'?uamgetfile=';
            $gallery->previewurl = $sPrefix.$gallery->previewurl.'&'.$sSuffix;
        } else {
            $gallery->previewurl = $gallery->previewurl.'?'.$sSuffix;
        }

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

        if ($options['hide_gallery'] == 'true' || true) {
            foreach ($galleries as $galleryId) {
                if ($this->accessHandler->checkObjectAccess(Gallery::OBJECT_TYPE, $galleryId) === false) {
                    unset($galleries[$galleryId]);
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
    public function loadImage($image)
    {
        $suffix = 'uamfiletype='.Image::OBJECT_TYPE;

        if ($this->config->isPermalinksActive() === false
            && $this->config->lockFile() === true
        ) {
            //Adding '.jpg' to the prefix prevents thick box display error
            $sPrefix = $this->wordpress->getHomeUrl('/').'.jpg?uamgetfile=';

            $image->imageURL = $sPrefix.$image->imageURL.'&'.$suffix;
            $image->thumbURL = $sPrefix.$image->thumbURL.'&'.$suffix;
        } else {
            $image->imageURL = $image->imageURL.'?'.$suffix;
            $image->thumbURL = $image->thumbURL.'?'.$suffix;
        }
    }
}
