<?php
/**
 * language.define.php
 *
 * Defines needed for the language
 *
 * PHP versions 5
 *
 * @category  UserAccessManager-NextGenGalleryExtension
 * @package   UserAccessManager-NextGenGalleryExtension
 * @author    Alexander Schneider <alexanderschneider85@googlemail.com>
 * @copyright 2008-2017 Alexander Schneider
 * @license   http://www.gnu.org/licenses/gpl-2.0.html  GNU General Public License, version 2
 * @version   SVN: $Id$
 * @link      http://wordpress.org/extend/plugins/user-access-manager/nextgen-gallery/
 */

// --- For user groups box ---
define('TXT_UAM_GROUP_TYPE_NGG_ALBUM', __('ngg albums', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAM_GROUP_TYPE_NGG_GALLERY', __('ngg galleries', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAM_GROUP_TYPE_NGG_IMAGE', __('ngg images', 'user-access-manager-nextgen-gallery-extension'));

// --- Error Messages ---
define('TXT_UAMNGG_UAM_MISSING', __('User Access Manager - NextGEN Gallery Extension: For this extension the User Access Manager is needed.', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_UAM_TO_LOW', __('User Access Manager - NextGEN Gallery Extension: Your version of the User Access Manager is not supported. You need at least version 2.0.0. Your version is: %s', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_NGG_MISSING', __('User Access Manager - NextGEN Gallery Extension: For this extension the NextGEN Gallery is needed.', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_NGG_TO_LOW', __('User Access Manager - NextGEN Gallery Extension: Your version of the NextGEN Gallery is not supported. You need at least version 1.7. Your version is: %s', 'user-access-manager-nextgen-gallery-extension'));

// --- Menu entry ---
define('TXT_UAMNGG_NGG_GALLERY_SETTING', __('NextGEN Gallery Settings', 'user-access-manager-nextgen-gallery-extension'));

// --- Settings ---
define('TXT_UAMNGG_SETTINGS', __('NextGEN Gallery Extension Settings', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_YES', __('Yes', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_NO', __('No', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_UPDATE_SETTINGS', __('Settings updated.', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_NGG_UPDATE_SETTING', __('Update settings', 'user-access-manager-nextgen-gallery-extension'));

// --- Settings - Album ---
define('TXT_UAMNGG_ALBUM_SETTING', __('Album Settings', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_ALBUM_SETTING_DESC', __('Set up the behaviour of locked albums', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_ALBUM', __('Hide album', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_ALBUM_DESC', __('Selecting "Yes" will hide albums if the user has no access. ', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_ALBUM_CONTENT', __('Album content', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_ALBUM_CONTENT_DESC', __('Displayed text as album content if user has no access.', 'user-access-manager-nextgen-gallery-extension'));

// --- Settings - Gallery ---
define('TXT_UAMNGG_GALLERY_SETTING', __('Gallery Settings', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_GALLERY_SETTING_DESC', __('Set up the behaviour of locked galleries', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_GALLERY', __('Hide Gallery', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_GALLERY_DESC', __('Selecting "Yes" will hide galleries if the user has no access. ', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_GALLERY_TITLE', __('Gallery title', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_GALLERY_TITLE_DESC', __('Displayed text as gallery title if user has no access.', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_GALLERY_TITLE', __('Hide gallery title', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_GALLERY_TITLE_DESC', __('Selecting "Yes" will show the text which is defined at "'.TXT_UAMNGG_GALLERY_TITLE.'" if user has no access. ', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_GALLERY_CONTENT', __('Gallery content', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_GALLERY_CONTENT_DESC', __('Displayed text as gallery content if user has no access.', 'user-access-manager-nextgen-gallery-extension'));

// --- Settings - Image ---
define('TXT_UAMNGG_IMAGE_SETTING', __('Image Settings', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_IMAGE_SETTING_DESC', __('Set up the behaviour of locked images', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_IMAGE', __('Hide image', 'user-access-manager-nextgen-gallery-extension'));
define('TXT_UAMNGG_HIDE_IMAGE_DESC', __('Selecting "Yes" will hide images if the user has no access. ', 'user-access-manager-nextgen-gallery-extension'));