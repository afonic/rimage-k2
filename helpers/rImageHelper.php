<?php

namespace Reach\Helpers;

use Intervention\Image\ImageManager;

class rImageHelper {

	protected $manager;
	protected $folder;

	function __construct() {
		$this->manager = new ImageManager();
		$this->folder = '/cache/images/';
	}

	
	public function getImage($image, $width, $height, $quality) {

		$path = $this->folder.'img_'.md5($image.$width.$height.$quality).'.jpg';

		if ($this->checkCache($path)) {
			return $path;
		}

		$this->generateCacheFolder();
		
		$img = $this->manager->make(JPATH_ROOT.$image);

		if ($width > 0) {
			$img->fit($width, $height, function ($constraint) {
			//$constraint->upsize();
			});
		}

		$img->interlace();

		$img->save(JPATH_ROOT.$path, $quality);

		return $path;
	}

	private function checkCache($path) {		
        return file_exists(JPATH_ROOT.$path);
    }

    // Creates the folder in the cache
    private function generateCacheFolder() {
        if (!file_exists(JPATH_ROOT.$this->folder)) {
            try {
                mkdir(JPATH_ROOT.$this->folder, 0755, true); }
            catch (Exception $e) {
                echo 'Folder cannot be created. Caught exception: ', $e->getMessage(), "\n";
            }
        }
    }


}