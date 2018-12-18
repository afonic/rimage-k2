<?php

namespace Reach;

use Reach\rImage;
use Intervention\Image\ImageManager;

// This class generates the image
class rImageGenerator {

	protected $manager;
	protected $image;

	function __construct(rImage $image, $library) {
		$this->image = $image;
		$this->manager = new ImageManager(array('driver' => $library));
	}


    // Create and save the image
    public function save() {
		
		$img = $this->manager->make($this->image->path);

		if ($this->image->ratio == '1') {
			if ($img->width() >= $img->height()) {
				$img->resize($this->image->width, null, function ($constraint) {
				    $constraint->aspectRatio();
				});
			} else {
				$img->resize(null, $this->image->width, function ($constraint) {
				    $constraint->aspectRatio();
				});
			}
		} else {
			// If we set 0 to width that means we just want to optimize
			if ($this->image->width > 1) {
				$img->fit($this->image->width, $this->image->height, function ($constraint) {
				//$constraint->upsize();
				});
			}
		}

		$img->interlace();

		$img->save($this->image->dir.'/'.$this->image->filename(), $this->image->quality);
    }
}