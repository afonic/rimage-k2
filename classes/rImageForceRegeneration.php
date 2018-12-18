<?php

namespace Reach;

use Reach\rImageFiles;
use Reach\rImageDbHelper;
use Reach\rImageGalleryGenerator;
use Reach\rImageItemGenerator;

class rImageForceRegeneration {

	protected $db;

	function __construct() {
		$this->db = \JFactory::getDbo();			
	}

	// Get all K2 items
	public function getK2Items() {
		$query = $this->db->getQuery(true);
		$query->select($this->db->quoteName(array('id', 'catid', 'gallery')));
		$query->from($this->db->quoteName('#__k2_items'));
		$query->where($this->db->quoteName('published') . ' = '. $this->db->quote(1));
		$query->where($this->db->quoteName('trash') . ' = '. $this->db->quote(0));
		$this->db->setQuery($query);
		return $this->db->loadObjectList();

	}

	public function regenerate() {
		$items = $this->getK2Items();
		foreach ($items as $item) {
			$this->generateItemSets($item->id, $item->catid);
			if ($item->gallery) {
				$this->generateGallerySets($item->id, $item->catid);
			}
		}
	}	

	public function regenerateSingle($id, $catid, $gallery = null) {
		$this->generateItemSets($id, $catid);
		if ($gallery) {
			$this->generateGallerySets($id, $catid);
		}
	}

	protected function generateItemSets($id, $catid) {

		$files = new rImageFiles($id);
		$itemGenerator = new rImageItemGenerator($id, $catid);

		if ($files->hasImage()) {
			$itemGenerator->generate();
		}

	}

	protected function generateGallerySets($id, $catid) {

		$helper = new rImageDbHelper($id, $catid, true);
		$generator = new rImageGalleryGenerator($id, $catid);	

		if ($helper->hashChanged()) {
			$generator->generate();
		}
		
	}

}