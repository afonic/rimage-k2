<?php

namespace Reach;

// This class messes with the filesystem
class rImageFiles {

	protected $files;
	protected $dir;
	protected $id;

	function __construct($id) {
		$this->files = $this->getGalleryFiles($id);
		$this->dir = $this->getGalleryDir($id);
		$this->id = $id;
	}

	// Get the files array
	public function getFiles() {
		return $this->files;
	}

	// Get the gallery's directory
	public function getDir() {
		return $this->dir;
	}

	// Check if the item has a main image
	public function hasImage() {
		return file_exists(JPATH_ROOT.$this->getImage());
	}

	// Location of the main K2 image
    public function getImage() {
        return "/media/k2/items/src/".md5("Image".$this->id).".jpg";
    }

	// Generate the array containing the filepath and filesize of each image of the gallery
	private function getGalleryFiles($id) {

		$dir = $this->getGalleryDir($id);

		if (is_dir($dir)) {
			$files = array();
			try {
                $images = array_merge(
                    glob($dir.'*.[jJ][pP][gG]'), 
                    glob($dir.'*.[jJ][pP][eE][gG]'),
                    glob($dir.'*.[pP][nN][gG]')
                );
				foreach ($images as $image) {
					$img = new \stdClass;
					$img->path = $image;
					$img->size = filesize($image);
					$files[] = $img;
				}
			}
			catch (Exception $e) {
				echo 'Cannot list files. Caught exception: ', $e->getMessage(), "\n";
			}
		}

		return $files;
		
	}

	// Get the gallery's directory path
	public function getGalleryDir($id) {
		return JPATH_ROOT.'/media/k2/galleries/'.$id.'/';
	}

	// Creates the folder in the cache
    public function generateCacheFolder($folder) {
        if (!file_exists($this->getCacheFolder($folder))) {
            try {
                mkdir($this->getCacheFolder($folder), 0755, true); }
            catch (Exception $e) {
                echo 'Folder cannot be created. Caught exception: ', $e->getMessage(), "\n";
            }
        }
    }

    // Get the cache folder
    public function getCacheFolder($folder) {
    	return JPATH_SITE.'/media/rimage/'.$folder.'/'.$this->id;
    }

    // Get image information
    public function getImageInfo(rImage $image) {
    	$img = getimagesize($image->path);
    	$info = new \stdClass;
    	$info->width = $img[0];
    	$info->height = $img[1];
    	return $info;
    }
}