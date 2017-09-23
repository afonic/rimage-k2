<?php

namespace Reach\Helpers;

use Intervention\Image\ImageManager;

class rImageHelper {

	protected $manager;

	function __construct() {
		$this->manager = new ImageManager();
	}

	
	public function getImage($image, $width, $height, $quality) {

		$path = JPATH_ROOT.'/cache/images/img_'.md5($image.$width.$height.$quality).'.jpg';

		$img = $this->manager->make(JPATH_ROOT.$image);

		$img->fit($width, $height, function ($constraint) {
			//$constraint->upsize();
		});

		$img->interlace();

		$img->save($path, $quality);

		return $path;
	}


}