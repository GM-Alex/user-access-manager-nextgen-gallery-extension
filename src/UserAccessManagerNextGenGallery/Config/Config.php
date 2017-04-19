<?php
namespace UserAccessManagerNextGenGallery\Config;

use UserAccessManager\Wrapper\Wordpress;

class Config
{
    const OPTIONS_NAME = 'uamNggAdminOptions';

    /**
     * @var null|array
     */
    private $options = null;

    public function __construct(Wordpress $wordpress)
    {
        $this->wordpress = $wordpress;
    }

    /**
     * Returns the current settings
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->options === null) {
            $defaultOptions = [
                'hide_album' => 'false',
                'album_content' => __(
                    'Sorry you have no rights to view this album!',
                    'user-access-manager-nextgen-gallery-extension'
                ),
                'hide_gallery' => 'false',
                'hide_gallery_title' => 'false',
                'gallery_title' => __('No rights!', 'user-access-manager-nextgen-gallery-extension'),
                'gallery_content' => __(
                    'Sorry you have no rights to view this gallery!',
                    'user-access-manager-nextgen-gallery-extension'
                ),
                'hide_image' => 'false',
            ];

            $options = (array)$this->wordpress->getOption(self::OPTIONS_NAME);

            foreach ($options as $sKey => $mOption) {
                $defaultOptions[$sKey] = $mOption;
            }

            $this->wordpress->updateOption(self::OPTIONS_NAME, $defaultOptions);
            $this->options = $defaultOptions;
        }

        return $this->options;
    }

    /**
     * Updates the options.
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->wordpress->updateOption(self::OPTIONS_NAME, $options);
        $this->options = null;
    }
}
