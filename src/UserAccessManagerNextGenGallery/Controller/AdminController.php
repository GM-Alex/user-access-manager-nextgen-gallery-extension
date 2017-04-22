<?php
namespace UserAccessManagerNextGenGallery\Controller;

use UserAccessManager\AccessHandler\AccessHandler;
use UserAccessManager\Config\Config;
use UserAccessManager\Controller\AdminObjectController;
use UserAccessManager\Database\Database;
use UserAccessManager\ObjectHandler\ObjectHandler;
use UserAccessManager\Wrapper\Php;
use UserAccessManager\Wrapper\Wordpress;
use UserAccessManagerNextGenGallery\Config\Config as NggConfig;
use UserAccessManagerNextGenGallery\PluggableObject\Album;
use UserAccessManagerNextGenGallery\PluggableObject\Gallery;
use UserAccessManagerNextGenGallery\PluggableObject\Image;

class AdminController extends AdminObjectController
{
    /**
     * @var NggConfig
     */
    private $nggConfig;

    /**
     * AdminController constructor.
     *
     * @param Php           $php
     * @param Wordpress     $wordpress
     * @param Config        $config
     * @param NggConfig     $nggConfig
     * @param Database      $database
     * @param ObjectHandler $objectHandler
     * @param AccessHandler $accessHandler
     */
    public function __construct(
        Php $php,
        Wordpress $wordpress,
        Config $config,
        NggConfig $nggConfig,
        Database $database,
        ObjectHandler $objectHandler,
        AccessHandler $accessHandler
    ) {
        parent::__construct($php, $wordpress, $config, $database, $objectHandler, $accessHandler);
        $this->nggConfig = $nggConfig;
    }

    /**
     * Add additional content to the album box.
     *
     * @param int $galleryId The gallery id.
     *
     * @return null
     */
    public function showAlbumItemContent($galleryId)
    {
        $content = $this->getPluggableColumn(
            Gallery::OBJECT_TYPE,
            $galleryId
        );

        return '<p><b>'
            .__('UAM User Groups', 'user-access-manager-nextgen-gallery-extension')
            .":&nbsp;</b>{$content}</p>";
    }

    /**
     * Adds a column header to the gallery columns.
     *
     * @param array $columns The gallery columns.
     *
     * @return array
     */
    public function showGalleryHeadColumn($columns)
    {
        $columns[self::COLUMN_NAME] = __('UAM User Groups', 'user-access-manager-nextgen-gallery-extension');

        return $columns;
    }

    /**
     * Add the column content for the uamAccess column.
     *
     * @param string  $column    The column name.
     * @param integer $galleryId The gallery id.
     */
    public function showGalleryColumn($column, $galleryId)
    {
        if ($column === self::COLUMN_NAME) {
            echo $this->getPluggableColumn(
                Gallery::OBJECT_TYPE,
                $galleryId
            );
        }
    }

    /**
     * Gets the right user groups after the update.
     *
     * @param string $objectType
     * @param int    $objectId
     * @param null   $postUserGroups
     *
     * @return null|\UserAccessManager\UserGroup\UserGroup[]
     */
    private function getUserGroupsAfterUpdate($objectType, $objectId, $postUserGroups = null)
    {
        $userGroups = null;
        $postUserGroups = ($postUserGroups !== null) ?
            $postUserGroups : $this->getRequestParameter(self::DEFAULT_GROUPS_FORM_NAME);

        if ($postUserGroups !== null) {
            $postUserGroupsMap = array_flip($postUserGroups);
            $userGroups = $this->getFilteredUserGroups();

            foreach ($userGroups as $userGroup) {
                if (isset($postUserGroupsMap[$userGroup->getId()]) === false
                    && $userGroup->isLockedRecursive($objectType, $objectId) === false
                ) {
                    unset($userGroups[$userGroup->getId()]);
                }
            }
        }

        return $userGroups;
    }

    /**
     * Adds a column header to the image columns.
     *
     * @return array
     */
    public function showImageHeadColumn()
    {
        return __('UAM User Groups', 'user-access-manager-nextgen-gallery-extension');
    }

    /**
     * Add the column content for the uamAccess column.
     *
     * @param string $output  The output.
     * @param object $image   The image.
     *
     * @return string
     */
    public function showImageColumn($output = '', $image = null)
    {
        $userGroups = $this->getRequestParameter('uam_ngg_image_user_groups', []);
        $userGroups = (isset($userGroups[$image->pid]) === true) ? $userGroups[$image->pid] : null;

        $content = $this->showPluggableGroupSelectionForm(
            Image::OBJECT_TYPE,
            $image->pid,
            "uam_ngg_image_user_groups[{$image->pid}]",
            $this->getUserGroupsAfterUpdate(Image::OBJECT_TYPE, $image->pid, $userGroups)
        );

        return $output.$content;
    }

    /**
     * Shows the user group selection form at the album settings page.
     *
     * @param integer $albumId The id of the album.
     */
    public function showAlbumEditForm($albumId)
    {
        $content = '<tr><th>';
        $content .= __('UAM User Groups', 'user-access-manager-nextgen-gallery-extension').'<br/>';

        $content .= $this->showPluggableGroupSelectionForm(
            Album::OBJECT_TYPE,
            $albumId,
            null,
            $this->getUserGroupsAfterUpdate(Album::OBJECT_TYPE, $albumId)
        );

        $content .= '</th></tr>';

        echo $content;
    }

    /**
     * Saves the user groups for the album.
     *
     * @param integer $albumId The id of the album.
     */
    public function updateAlbum($albumId)
    {
        $this->savePluggableObjectData(
            Album::OBJECT_TYPE,
            $albumId
        );
    }

    /**
     * Shows the user group selection form at the gallery settings page.
     *
     * @param array $fields
     *
     * @return array
     */
    public function showGalleryEditForm(array $fields)
    {
        $fields['right']['uam'] = [
            'id' => 'uam',
            'label' => __('UAM User Groups', 'user-access-manager-nextgen-gallery-extension'),
            'tooltip' => null,
            'callback' => function ($gallery) {
                echo $this->showPluggableGroupSelectionForm(
                    Gallery::OBJECT_TYPE,
                    $gallery->gid,
                    null,
                    $this->getUserGroupsAfterUpdate(Gallery::OBJECT_TYPE, $gallery->gid)
                );
            }
        ];

        return $fields;
    }

    /**
     * Saves the user groups for the gallery.
     *
     * @param integer $galleryId The id of the gallery.
     */
    public function updateGallery($galleryId)
    {
        $this->savePluggableObjectData(
            Gallery::OBJECT_TYPE,
            $galleryId
        );

        $picturesUserGroups = $this->getRequestParameter('uam_ngg_image_user_groups', []);

        foreach ($picturesUserGroups as $pictureUserGroups) {
            $this->savePluggableObjectData(
                Image::OBJECT_TYPE,
                $galleryId,
                $pictureUserGroups
            );
        }
    }

    /**
     * Updates the settings
     */
    public function updateUamNggSettingsAction()
    {
        $this->verifyNonce('uamNggUpdateSettings');
        $options = $this->nggConfig->getOptions();

        foreach ($options as $option => $value) {
            $newValue = $this->getRequestParameter('uamngg_'.$option);

            if ($newValue !== null) {
                $options[$option] = $newValue;
            }
        }

        $this->nggConfig->setOptions($options);
        $this->setUpdateMessage(TXT_UAMNGG_UPDATE_SETTINGS);
    }

    /**
     * Returns the options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->nggConfig->getOptions();
    }

    /**
     * Prints the settings page.
     */
    public function printSettingsPage()
    {
        $currentAdminPage = $this->getRequestParameter('page');

        if ($currentAdminPage === 'uam_ngg_settings') {
            $this->processAction();
            $path = realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'View');
            $file = $path.DIRECTORY_SEPARATOR.'AdminSettings.php';
            $this->php->includeFile($this, $file);
        }
    }
}
