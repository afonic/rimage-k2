<?php

namespace Reach\Helpers;

use Intervention\Image\ImageManager;

class rImageHelper {

	protected $manager;

	function __construct() {
		$this->manager = new ImageManager();
	}

	
	public function getImage($image, $width, $height, $quality) {

		$path = '/cache/images/img_'.md5($image.$width.$height.$quality).'.jpg';

		if ($this->checkCache($path)) {
			return $path;
		}
		
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


}