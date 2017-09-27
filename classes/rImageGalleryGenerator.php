<?php

namespace Reach;

use Reach\rImage;
use Reach\rImageFiles;
use Reach\rImageDbHelper;
use Reach\rImageGenerator;

// This class generates the image gallery set

class rImageGalleryGenerator {

	protected $id;
	protected $files;
	protected $db;
	protected $sets;
	protected $library;

	function __construct($id, $catid) {
		$this->id = $id;
		$this->files = new rImageFiles($id);
		$this->db = new rImageDbHelper($id, $catid);	
		$this->sets = $this->db->getSets('gallery');
		$this->library = $this->db->getLibrary();
	}

	// The function to call all subfunctions
	public function generate() {
		if ($this->noSets()) {
			return;
		}
		$this->deleteDbRecords('gallery');
		try {
			$this->files->generateCacheFolder('galleries');
			$this->generateImages();
		}
		catch (Exception $e) {
            echo 'Images cannot be generated. Caught exception: ', $e->getMessage(), "\n";
        }
		$this->db->updateGalleryHash();

	}

	// Go through the images and the sets wanted and call the generator
	protected function generateImages() {
		foreach ($this->sets as $set) {
			foreach ($this->files->getFiles() as $image) {
				$img = new rImage($image->path,
					$this->files->getCacheFolder('galleries'),
					$set->name,
					$set->width,
					$set->height,
					$set->quality
				);
				$this->generateImage($img, 'gallery');
			}
		}
	}

	// Creates the images in the cache
	protected function generateImage($image) {
		try {
			$generate = new rImageGenerator($image, $this->library);
			$generate->save();
			$this->db->addImage($image, 'gallery');
		}
	    catch (Exception $e) {
            echo 'Image cannot be saved. Caught exception: ', $e->getMessage(), "\n";
        }
	}


	// Calls the function to delete thes images from the database
	protected function deleteDbRecords($type) {
		$this->db->deleteImages($type);
	}

	// Check to see if there are sets for this item
	protected function noSets() {
		if (count($this->sets) == 0) {
			return true;
		}
		return false;
	}



}