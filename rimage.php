<?php
// no direct access
defined('_JEXEC') or die ;

// include composer autoload
require 'vendor/autoload.php';

use Reach\rImageFiles;
use Reach\rImageDbHelper;
use Reach\rImageGalleryGenerator;
use Reach\rImageItemGenerator;

// Load the K2 Plugin API
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php');

class plgK2rImage extends K2Plugin
{

    // Some params
    public $pluginName = 'rimage';
    public $pluginNameHumanReadable = 'K2 images reimagined.';


    public function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);
    }

    /**
     * After a K2 item is saved, we need to proccess the images
     * 
     * @param Object $item The K2 item
     * 
     * @return null
     */
    public function onAfterK2Save(&$item)
    {
        $files = new rImageFiles($item->id);
        $itemGenerator = new rImageItemGenerator($item->id, $item->catid);

        if ($files->hasImage()) {
            $itemGenerator->generate();
        }

        if ($files->hasGallery()) {
            $helper = new rImageDbHelper($item->id, $item->catid);
            $generator = new rImageGalleryGenerator($item->id, $item->catid);

            if ($helper->hashChanged()) {
                $generator->generate();
            }
        }
    }

    /**
     * If the item has a gallery we need to set the gallery to the correct value
     *
     * @param Object $item       The K2 item
     * @param array  $params     The parameters of the plugin
     * @param int    $limitstart Don't know what this does
     *
     * @return null
     */
    public function onK2PrepareContent(&$item, &$params, $limitstart)
    {
        $files = new rImageFiles($item->id);
        if ($files->hasGallery()) {
            $item->gallery = '{gallery}'.$item->id.'{/gallery}';
        }
    }
    
}
