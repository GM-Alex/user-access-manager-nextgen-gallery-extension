<?php
namespace UserAccessManagerNextGenGallery\Wrapper;

class NextGenGallery
{
    /**
     * @return array
     */
    public function getOptions()
    {
        global $ngg;
        return (array)$ngg->options;
    }

    /**
     * @param $id
     *
     * @return null|\stdClass
     */
    public function getImage($id)
    {

        return \C_Image_Mapper::get_instance()->find($id, true);
    }

    /**
     * @param $galleryId
     *
     * @return \stdClass[]
     */
    public function getImagesByGallery($galleryId)
    {
        /**
         * @var \C_Image_Mapper $mapper
         */
        $mapper = \C_Image_Mapper::get_instance();

        return (array)$mapper->select()
            ->where(["galleryid = %d", $galleryId])
            ->run_query();
    }

    /**
     * @param $albumId
     *
     * @return \stdClass[]
     */
    public function getImagesByAlbum($albumId)
    {
        /**
         * @var \C_Image_Mapper $mapper
         */
        $mapper = \C_Image_Mapper::get_instance();

        return (array)$mapper->select()
            ->where(["galleryid = %d", $albumId])
            ->run_query();
    }

    /**
     * @param $id
     *
     * @return null|\stdClass
     */
    public function getGallery($id)
    {
        return \C_Gallery_Mapper::get_instance()->find($id, true);
    }

    /**
     * @param $id
     *
     * @return null|\stdClass
     */
    public function getAlbum($id)
    {
        return \C_Album_Mapper::get_instance()->find($id, true);
    }

    /**
     * @return \stdClass[]
     */
    public function getAllAlbums()
    {
        return \C_Album_Mapper::get_instance()->find_all();
    }

    /**
     * @return \C_Gallery_Storage
     */
    public function getStorage()
    {
        return \C_Gallery_Storage::get_instance();
    }
}
