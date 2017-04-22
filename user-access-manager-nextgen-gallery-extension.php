<?php
/**
 * Plugin Name: User Access Manager - NextGEN Gallery Extension
 * Plugin URI: http://www.gm-alex.de/projects/wordpress/plugins/user-access-manager/nextgen-gallery/
 * Author URI: http://www.gm-alex.de/
 * Version: 1.0.0-Beta
 * Author: Alexander Schneider
 * Description: With this plugin you can use the user access manager to control the access for the NextGen Gallery.
 *
 * user-access-manager-nextgen-gallery-extension.php
 *
 * PHP versions 5
 *
 * @category  UserAccessManager-NextGenGalleryExtension
 * @package   UserAccessManager-NextGenGalleryExtension
 * @author    Alexander Schneider <alexanderschneider85@gmail.com>
 * @copyright 2008-2013 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $Id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/nextgen-gallery/
*/
require_once 'includes/language.php';
require_once 'autoload.php';

$locale = apply_filters('plugin_locale', get_locale(), 'user-access-manager-nextgen-gallery-extension');
load_textdomain(
    'user-access-manager-nextgen-gallery-extension',
    WP_LANG_DIR.'/user-access-manager/user-access-manager-'.$locale.'.mo'
);
load_plugin_textdomain(
    'user-access-manager-nextgen-gallery-extension',
    false,
    plugin_basename(dirname(__FILE__)).'/languages'
);

if (function_exists('add_action')) {
    add_action('uam_init', function (\UserAccessManager\UserAccessManager $userAccessManager) {
        //Check requirements
        /*global $ngg;

        if (empty($ngg) === true) {
            add_action(
                'admin_notices',
                function () {
                    echo '<div id="message" class="error"><p><strong>'
                        .TXT_UAMNGG_NGG_MISSING
                        .'</strong></p></div>';
                }
            );

            return;
        } elseif (version_compare($ngg->version, '1.7') === -1) {
            add_action(
                'admin_notices',
                function () use ($ngg) {
                    echo '<div id="message" class="error"><p><strong>'
                        .sprintf(TXT_UAMNGG_NGG_TO_LOW, $ngg->version)
                        .'</strong></p></div>';
                }
            );

            return;
        }*/

        $nggConfig = new \UserAccessManagerNextGenGallery\Config\Config($userAccessManager->getWordpress());
        $nextGenGallery = new \UserAccessManagerNextGenGallery\Wrapper\NextGenGallery();

        $frontendController = new \UserAccessManagerNextGenGallery\Controller\FrontendController(
            $userAccessManager->getPhp(),
            $userAccessManager->getWordpress(),
            $userAccessManager->getConfig(),
            $nggConfig,
            $userAccessManager->getAccessHandler()
        );

        $adminController = new \UserAccessManagerNextGenGallery\Controller\AdminController(
            $userAccessManager->getPhp(),
            $userAccessManager->getWordpress(),
            $userAccessManager->getConfig(),
            $nggConfig,
            $userAccessManager->getDatabase(),
            $userAccessManager->getObjectHandler(),
            $userAccessManager->getAccessHandler()
        );

        $userAccessManagerNextGenGallery = new \UserAccessManagerNextGenGallery\UserAccessManagerNextGenGallery(
            $userAccessManager->getWordpress(),
            $nextGenGallery,
            $userAccessManager->getDatabase(),
            $userAccessManager->getConfig(),
            $userAccessManager->getObjectHandler(),
            $userAccessManager->getAccessHandler(),
            $userAccessManager->getFileHandler(),
            $userAccessManager->getFileObjectFactory(),
            $frontendController,
            $adminController
        );

        // deactivation
        register_deactivation_hook(__FILE__, function () use ($userAccessManagerNextGenGallery) {
            $userAccessManagerNextGenGallery->deactivate();
        });
    });
}

// install
register_activation_hook(__FILE__, function () {
    global $userAccessManager;

    $nggConfig = new \UserAccessManagerNextGenGallery\Config\Config($userAccessManager->getWordpress());
    $nextGenGallery = new \UserAccessManagerNextGenGallery\Wrapper\NextGenGallery();
    $frontendController = new \UserAccessManagerNextGenGallery\Controller\FrontendController(
        $userAccessManager->getPhp(),
        $userAccessManager->getWordpress(),
        $userAccessManager->getConfig(),
        $nggConfig,
        $userAccessManager->getAccessHandler()
    );
    $adminController = new \UserAccessManagerNextGenGallery\Controller\AdminController(
        $userAccessManager->getPhp(),
        $userAccessManager->getWordpress(),
        $userAccessManager->getConfig(),
        $nggConfig,
        $userAccessManager->getDatabase(),
        $userAccessManager->getObjectHandler(),
        $userAccessManager->getAccessHandler()
    );
    $userAccessManagerNextGenGallery = new \UserAccessManagerNextGenGallery\UserAccessManagerNextGenGallery(
        $userAccessManager->getWordpress(),
        $nextGenGallery,
        $userAccessManager->getDatabase(),
        $userAccessManager->getConfig(),
        $userAccessManager->getObjectHandler(),
        $userAccessManager->getAccessHandler(),
        $userAccessManager->getFileHandler(),
        $userAccessManager->getFileObjectFactory(),
        $frontendController,
        $adminController
    );
    $userAccessManagerNextGenGallery->activate();
});
