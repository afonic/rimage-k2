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
		$query->select($this->db->quoteName(array('galleryset', 'path', 'width', 'height', 'timestamp')));
		$query->from($this->db->quoteName('#__rimage_gallery_images'));
		$query->where($this->db->quoteName('itemid') . ' = '. $this->db->quote($this->id));
		$query->where($this->db->quoteName('galleryset') . ' = '. $this->db->quote($set));
		$this->db->setQuery($query);
		return $this->db->loadObjectList();
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

}