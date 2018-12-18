<?php

namespace Reach;

use Reach\rImage;
use Reach\rImageFiles;

// This classes messes with the database records
class rImageDbHelper {

	protected $db;
	protected $id;
	protected $catid;
	protected $files;
	protected $force;

	function __construct($id, $catid, $force = false) {
		$this->db = \JFactory::getDbo();
		$this->id = $id;
		$this->catid = $catid;
		$this->files = new rImageFiles($this->id);
		$this->force = $force;
	}

	// Get all the sets from the plugin's parameters
	public function getSets($type) {

		if ($type === 'item') {
			$type = 0;
		}		
		if ($type === 'gallery') {
			$type = 1;
		}

		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('params')));
		$query->from($this->db->quoteName('#__extensions'));
		$query->where($this->db->quoteName('element') . ' = '. $this->db->quote('rimage'), 'AND');
		$query->where($this->db->quoteName('folder') . ' = '. $this->db->quote('k2'));
		$this->db->setQuery($query);
		$dbData = json_decode($this->db->loadResult(), true);

		$sets = array();		

		foreach ($dbData['image-sets'] as $imgSet) {
			if (($this->checkCategories($imgSet['k2categories'], $imgSet['k2selectsubcategories'])) and ($type == $imgSet['set_type'])) {
				$set = new \stdClass;
				$set->name = $imgSet['set_name'];
				$set->width = $imgSet['width'];
				$set->height = $imgSet['height'];
				$set->quality = $imgSet['quality'];
				$set->ratio = $imgSet['ratio'];
				$sets[$imgSet['set_name']] = $set;			
			}			
		}

		return $sets;		
	}

	// Check if the item's category has a set configured
	public function checkCategories($categories, $children) {
		if (in_array($this->catid, $categories)) {
			return true;
		}
		if ($children) {
			$childs = $this->getChildK2Categories($categories);
			foreach ($childs as $child) {
				if (in_array($child->parent, $categories)) {
					$categories[] = $child->id;
				}
			}
			if (in_array($this->catid, $categories)) {
				return true;
			}
		}

	}
	
	// Get all parent categories from database
	protected function getChildK2Categories($categories) {		
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('id', 'parent')));
		$query->from($this->db->quoteName('#__k2_categories'));
		$query->where($this->db->quoteName('published') . ' = 1');
		$query->where($this->db->quoteName('parent') . ' != 0');
		$this->db->setQuery($query);
		return $this->db->loadObjectList('id');
	}

	// Check the the gallery's files have been changed by comparing hashes
	public function hashChanged() {

		// Check if we will force a regen
		if ($this->force) {
			return true;
		}

		// Get the hashes
		$newHash = $this->getGalleryHash();
		$search = $this->findGalleryHash();
		// If we found the id in the database and the hash is unchanged return false
		if ($search) {
			if ($search->hash == $newHash) {				
				return false;
			}
		}
		// Else return true
		return true;
	}


	// Generate the unique hash (filename + filesize based)
	public function getGalleryHash() {		
		return md5(serialize($this->files->getFiles()).serialize($this->getSets('gallery')));
	}

	// Update the hash in the database
	public function updateGalleryHash() {
		try {
			$gallery = new \stdClass();
			$gallery->itemid = $this->id;
			$gallery->hash = $this->getGalleryHash($this->id);
			$gallery->path = $this->files->getDir();
			if ($this->findGalleryHash()) {
				$result = $this->db->updateObject('#__rimage_gallery_hashes', $gallery, 'itemid');
			}
			else {
				$result = $this->db->insertObject('#__rimage_gallery_hashes', $gallery);
			}
		}
		catch (Exception $e) {
			echo 'Cannot updated gallery hash. Caught exception: ', $e->getMessage(), "\n";
		}
	}

	// Check the database to see if that hash exists
	public function findGalleryHash() {
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('itemid', 'hash')));
		$query->from($this->db->quoteName('#__rimage_gallery_hashes'));
		$query->where($this->db->quoteName('itemid') . ' = '. $this->db->quote($this->id));
		$this->db->setQuery($query);
		$found = $this->db->loadObject();
		if ($found) {
			return $found;
		}
		return false;
	}

	// Delete an old hash from the table
	public function deleteGalleryHash() {
		try {
			$query = $this->db->getQuery(true);
			$query->delete($this->db->quoteName('#__rimage_gallery_hashes'));
			$query->where($this->db->quoteName('itemid') . ' = ' . $this->id);
			$this->db->setQuery($query);
			$result = $this->db->execute();
		}
		catch (Exception $e) {
			echo 'Cannot delete hash. Caught exception: ', $e->getMessage(), "\n";
		}
	}

	// Add the gallery image to the database
	public function addImage(rImage $image, $type) {

		// Create an object and save the image in the database.
		try {
			$img = new \stdClass();
			$img->itemid = $this->id;
			$img->galleryset = $image->set;
			$img->path = str_replace(JPATH_SITE, '', $image->dir.'/'.$image->filename());
			// If the image is just optimized, get the original size.
			if ($image->width == 0) {
				$real = $this->files->getImageInfo($image);
				$image->width = $real->width;
				$image->height = $real->height;
			}
			$img->width = $image->width;
			$img->height = $image->height;
			$img->orig_path = str_replace(JPATH_SITE, '', $image->path);
			$img->timestamp = hash('crc32', date('Y-m-d H:i'));
			$result = $this->db->insertObject('#__rimage_'.$type.'_images', $img, 'itemid');
		}
		catch (Exception $e) {
			echo 'Cannot add image to database. Caught exception: ', $e->getMessage(), "\n";
		}
	}

	// Delete all records for that item
	public function deleteImages($type) {
		try {
			$query = $this->db->getQuery(true);
			$query->delete($this->db->quoteName('#__rimage_'.$type.'_images'));
			$query->where($this->db->quoteName('itemid') . ' = ' . $this->id);
			$this->db->setQuery($query);
			$result = $this->db->execute();
		}
		catch (Exception $e) {
			echo 'Cannot delete image entry. Caught exception: ', $e->getMessage(), "\n";
		}
	}

	// Get the image gallery setting
	public function getLibrary() {
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('params')));
		$query->from($this->db->quoteName('#__extensions'));
		$query->where($this->db->quoteName('element') . ' = '. $this->db->quote('rimage'), 'AND');
		$query->where($this->db->quoteName('folder') . ' = '. $this->db->quote('k2'));
		$this->db->setQuery($query);
		$dbData = json_decode($this->db->loadResult(), true);

		if ($dbData['imagelibrary'] == 1) {
			return 'imagick';
		}

		return 'gd';

	}

}