<?php

namespace Reach;

// What do we need to generate an image
class rImage {

	public $path;
	public $dir;
	public $set;
	public $width;
	public $height;
	public $quality;
	public $position;

	function __construct($path, $dir, $set, $width, $height, $quality) {
		$this->path = $path;
		$this->dir = $dir;
		$this->set = $set;
		$this->width = $width;
		$this->height = $height;
		$this->quality = $quality;	
	}

	public function setPosition($position) {
		$this->position = $position;
	}

	public function filename() {
		return $this->set.'_'.md5(str_replace(JPATH_SITE, '', $this->path).$this->width.$this->height.$this->quality).'.jpg';
	}
}