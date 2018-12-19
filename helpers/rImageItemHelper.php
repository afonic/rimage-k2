<?php

namespace Reach\Helpers;

class rImageItemHelper {

	public $id;
	protected $db;

	function __construct($id) {
		$this->id = $id;
		$this->db = \JFactory::getDbo();
	}

	// Get a gallery set
	public function getGallerySet($set) {
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('galleryset', 'path', 'orig_path', 'width', 'height', 'timestamp')));
		$query->from($this->db->quoteName('#__rimage_gallery_images'));
		$query->where($this->db->quoteName('itemid') . ' = '. $this->db->quote($this->id));
		$query->where($this->db->quoteName('galleryset') . ' = '. $this->db->quote($set));
		$this->db->setQuery($query);
		return $this->orderByFile($this->db->loadObjectList());
	}	

	// Get a main image set
	public function getItemSet($set) {
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('galleryset', 'path', 'width', 'height', 'timestamp')));
		$query->from($this->db->quoteName('#__rimage_item_images'));
		$query->where($this->db->quoteName('itemid') . ' = '. $this->db->quote($this->id));
		$query->where($this->db->quoteName('galleryset') . ' = '. $this->db->quote($set));
		$this->db->setQuery($query);
		return $this->db->loadObject();
	}
	
	// Gets the order.json file for the gallery if it exists
	public function getOrderFile() {
		$helper = new \Reach\rImageFiles($this->id);
       	$file = $helper->getDir().'order.json';
        if (file_exists($file)) {
            return json_decode(file_get_contents($file));
        }
        return false;
	}
	
	// Orders the array by the paths in the file
	public function orderByFile($array) {
		$orderArray = $this->getOrderFile();
		if (! $orderArray) {
			return $array;
		}
		$orderedArray = array();
		foreach ($orderArray as $order) {
			foreach ($array as $i => $image) {
				if ($image->orig_path === $order) {
					$orderedArray[] = $image;
					unset($array[$i]);
				}
			}
		}
		return $orderedArray;
	}

}