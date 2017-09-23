<?php

namespace Reach;

use Reach\rImage;
use Intervention\Image\ImageManager;

// This class generates the image
class rImageGenerator {

	protected $manager;
	protected $image;

	function __construct(rImage $image) {
		$this->image = $image;
		$this->manager = new ImageManager();
	}


    // Create and save the image
    public function save() {
		
		$img = $this->manager->make($this->image->path);

		// If we set 0 to width that means we just want to optimize
		if ($this->image->width > 1) {
			$img->fit($this->image->width, $this->image->height, function ($constraint) {
			//$constraint->upsize();
			});
		}

		$img->interlace();

		$img->save($this->image->dir.'/'.$this->image->filename(), $this->image->quality);
    }
}