<?php

namespace Reach;

use Reach\rImage;
use Reach\rImageFiles;
use Reach\rImageDbHelper;
use Reach\rImageGenerator;
use Reach\rImageGalleryGenerator;

// This class generates the image gallery set

class rImageItemGenerator extends rImageGalleryGenerator {

	protected $sets;

	function __construct($id, $catid) {
		parent::__construct($id, $catid);	
		$this->sets = $this->db->getSets('item');	
	}

	// The function to call all subfunctions
	public function generate() {
		if ($this->noSets()) {
			return;
		}
		$this->deleteDbRecords('item');
		try {
			$this->files->generateCacheFolder('items');
			$this->generateImages();
		}
		catch (Exception $e) {
            echo 'Image cannot be generated. Caught exception: ', $e->getMessage(), "\n";
        }

	}

	// Go through the the sets wanted and call the generator
	protected function generateImages() {
		foreach ($this->sets as $set) {	
			$img = new rImage(JPATH_ROOT.$this->files->getImage(),
				$this->files->getCacheFolder('items'),
				$set->name,
				$set->width,
				$set->height,
				$set->quality
			);
			$this->generateImage($img);
		}		
	}

	// Creates the images in the cache
	protected function generateImage($image) {
		try {
			$generate = new rImageGenerator($image);
			$generate->save();
			$this->db->addImage($image, 'item');
		}
	    catch (Exception $e) {
            echo 'Image cannot be saved. Caught exception: ', $e->getMessage(), "\n";
        }
	}


}