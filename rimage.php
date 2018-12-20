<?php
/**
 * @version		2.2
 * @package		Example K2 Plugin (K2 plugin)
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

// include composer autoload
require 'vendor/autoload.php';

use Reach\rImageFiles;
use Reach\rImageDbHelper;
use Reach\rImageGalleryGenerator;
use Reach\rImageItemGenerator;

// Load the K2 Plugin API
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php');

// Initiate class to hold plugin events
class plgK2rImage extends K2Plugin
{

	// Some params
	var $pluginName = 'rimage';
	var $pluginNameHumanReadable = 'K2 images reimagined.';


	function __construct(&$subject, $params)
	{
		parent::__construct($subject, $params);
	}

	function onAfterK2Save(&$item)

	{

		$files = new rImageFiles($item->id);
		$itemGenerator = new rImageItemGenerator($item->id, $item->catid);

		if ($files->hasImage()) {
			$itemGenerator->generate();
		}

		if ($files->hasGallery()) {
			$helper = new rImageDbHelper($item->id, $item->catid);
			$generator = new rImageGalleryGenerator($item->id, $item->catid);

			if ($helper->hashChanged()) {
				$generator->generate();
			}

		}
		
	}

} // END CLASS