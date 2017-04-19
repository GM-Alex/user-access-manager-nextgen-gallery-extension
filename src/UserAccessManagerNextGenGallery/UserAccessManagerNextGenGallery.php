<?php
namespace UserAccessManagerNextGenGallery;

use UserAccessManager\AccessHandler\AccessHandler;
use UserAccessManager\Config\Config;
use UserAccessManager\Database\Database;
use UserAccessManager\FileHandler\FileHandler;
use UserAccessManager\ObjectHandler\ObjectHandler;
use UserAccessManager\UserAccessManager;
use UserAccessManager\Wrapper\Wordpress;
use UserAccessManagerNextGenGallery\Controller\AdminController;
use UserAccessManagerNextGenGallery\Controller\FrontendController;
use UserAccessManagerNextGenGallery\PluggableObject\Album;
use UserAccessManagerNextGenGallery\PluggableObject\Gallery;
use UserAccessManagerNextGenGallery\PluggableObject\Image;
use UserAccessManagerNextGenGallery\Wrapper\NextGenGallery;

/**
 * Class UserAccessManagerNextGenGallery
 */
class UserAccessManagerNextGenGallery
{
    /**
     * @var Wordpress
     */
    private $wordpress;

    /**
     * @var Database
     */
    private $database;
    
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var AccessHandler
     */
    private $accessHandler;

    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var FrontendController
     */
    private $frontendController;

    /**
     * @var AdminController
     */
    private $adminController;

    /**
     * @var NextGenGallery
     */
    private $nextGenGallery;

    /**
     * UserAccessManagerNextGenGallery constructor.
     *
     * @param Wordpress             $wordpress
     * @param NextGenGallery        $nextGenGallery
     * @param Database              $database
     * @param Config                $config
     * @param ObjectHandler         $objectHandler
     * @param AccessHandler         $accessHandler
     * @param FileHandler           $fileHandler
     * @param FrontendController    $frontendController
     * @param AdminController       $adminController
     */
    public function __construct(
        Wordpress $wordpress,
        NextGenGallery $nextGenGallery,
        Database $database,
        Config $config,
        ObjectHandler $objectHandler,
        AccessHandler $accessHandler,
        FileHandler $fileHandler,
        FrontendController $frontendController,
        AdminController $adminController
    ) {
        $this->wordpress = $wordpress;
        $this->database = $database;
        $this->config = $config;
        $this->objectHandler = $objectHandler;
        $this->accessHandler = $accessHandler;
        $this->fileHandler = $fileHandler;
        $this->frontendController = $frontendController;
        $this->adminController = $adminController;
        $this->nextGenGallery = $nextGenGallery;

        $this->addHooks();
        $this->registerPluggableObjects();
    }

    /**
     * Add the hooks.
     */
    private function addHooks()
    {
        if (version_compare(UserAccessManager::VERSION, '2.0.0') === -1) {
            $this->wordpress->addAction(
                'admin_notices',
                function () {
                    echo '<div id="message" class="error"><p><strong>'
                        .sprintf(TXT_UAMNGG_UAM_TO_LOW, UserAccessManager::VERSION)
                        .'</strong></p></div>';
                }
            );

            return;
        }

        $this->wordpress->addAction('uam_add_sub_menu', function () {
            $this->wordpress->addSubmenuPage(
                'uam_user_group',
                TXT_UAMNGG_NGG_GALLERY_SETTING,
                TXT_UAMNGG_NGG_GALLERY_SETTING,
                'read',
                'uam_ngg_settings',
                [$this->adminController, 'printSettingsPage']
            );
        });

        // Actions

        $this->wordpress->addAction(
            'ngg_manage_gallery_custom_column',
            [$this->adminController, 'showGalleryColumn'],
            10,
            2
        );
        $this->wordpress->addAction('ngg_manage_gallery_fields', [$this->adminController, 'showGalleryEditForm'], 11);
        $this->wordpress->addAction('ngg_update_gallery', [$this->adminController, 'updateGallery']);

        $this->wordpress->addAction('ngg_edit_album_settings', [$this->adminController, 'showAlbumEditForm']);
        $this->wordpress->addAction('ngg_update_album', [$this->adminController, 'updateAlbum']);
        $this->wordpress->addAction('ngg_display_album_item_content', [$this->adminController, 'showAlbumItemContent']);

        $this->wordpress->addAction('update_option_permalink_structure', [$this, 'updatePermalink']);
        $this->wordpress->addAction('uam_update_options', [$this, 'updateUamSettings']);

        $this->wordpress->addFilter('ngg_manage_images_number_of_columns', function ($count) {
            $count++;

            $this->wordpress->addFilter(
                "ngg_manage_images_column_{$count}_header",
                [$this->adminController, 'showImageHeadColumn']
            );

            $this->wordpress->addFilter(
                "ngg_manage_images_column_{$count}_content",
                [$this->adminController, 'showImageColumn'],
                10,
                2
            );

            return $count;
        });

        // Filter
        //TODO not existing anymore
        $this->wordpress->addFilter(
            'ngg_show_gallery_content',
            [$this->frontendController, 'showGalleryContent'],
            10,
            2
        );
        //TODO not existing anymore
        $this->wordpress->addFilter(
            'ngg_picturelist_object',
            [$this->frontendController, 'showGalleryImages'],
            10,
            2
        );
        $this->wordpress->addFilter(
            'ngg_show_album_content',
            [$this->frontendController, 'showAlbumContent'],
            10,
            2
        );
        $this->wordpress->addFilter(
            'ngg_album_galleryobject',
            [$this->frontendController, 'showGalleryObjectForAlbum'],
            10
        );

        //TODO not existing anymore
        $this->wordpress->addFilter(
            'ngg_album_galleries',
            [$this->frontendController, 'showGalleriesForAlbum'],
            10
        );
        $this->wordpress->addFilter('ngg_manage_gallery_columns', [$this->adminController, 'showGalleryHeadColumn']);

        $this->wordpress->addFilter('ngg_get_image', [$this->frontendController, 'loadImage']);
    }

    /**
     * Registers the pluggable objects.
     */
    private function registerPluggableObjects()
    {
        $nextGenGalleryImagePluggableObject = new Image(
            $this->config,
            $this->nextGenGallery
        );
        $this->objectHandler->registerPluggableObject($nextGenGalleryImagePluggableObject);

        $nextGenGalleryGalleryPluggableObject = new Gallery(
            $this->config,
            $this->nextGenGallery
        );
        $this->objectHandler->registerPluggableObject($nextGenGalleryGalleryPluggableObject);

        $nextGenGalleryAlbumPluggableObject = new Album(
            $this->config,
            $this->nextGenGallery
        );
        $this->objectHandler->registerPluggableObject($nextGenGalleryAlbumPluggableObject);
    }
    
    /**
     * The activation function.
     */
    public function activate()
    {
        if ($this->config->lockFile() === true) {
            $this->createHtaccessFiles();
        }
    }

    /**
     * The deactivation function.
     */
    public function deactivate()
    {
        $this->removeHtaccessFiles();
    }

    /**
     * The function for the update_option_permalink_structure action.
     */
    public function updatePermalink()
    {
        $this->createHtaccessFiles();
    }

    /**
     * The function for the uam_update_options action.
     */
    public function updateUamSettings()
    {
        if ($this->config->lockFile() === false) {
            $this->removeHtaccessFiles();
        } else {
            $this->createHtaccessFiles();
        }
    }

    /**
     * Returns the gallery directory.
     *
     * @return string
     */
    private function getGalleryDir()
    {
        $nextGenGalleryOptions = $this->nextGenGallery->getOptions();
        $dir = str_replace("\\", "/", ABSPATH);
        $dir .= $nextGenGalleryOptions['gallerypath'];

        return $dir;
    }

    /**
     * Creates the htaccess files.
     */
    private function createHtaccessFiles()
    {
        $dir = $this->getGalleryDir();
        $this->fileHandler->createFileProtection($dir, Image::OBJECT_TYPE);
    }

    /**
     * Remove the protection files.
     */
    private function removeHtaccessFiles()
    {
        $dir = $this->getGalleryDir();
        $this->fileHandler->deleteFileProtection($dir);
    }
}
