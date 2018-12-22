## RImage for K2 (Joomla)

RImage is a plugin for Joomla that complements K2 by adding an image gallery and greatly improving images handling. Please note that this plugin is made for use in-house and may or may not appeal to you. It is tailor made for our needs and it is focused on a developer's point of view and it's not meant to be used by a simple Joomla / K2 user. It takes the assumption that you have full control of whoever has access to your administrator panel. We will try to improve functionality and fix bugs but there is no guarantee this will be upgraded or that pull requests will be implemented. Please use at your own risk and with consideration of the above in mind.

## What does it do

rImage is a plugin that uses [Intervention Image](http://image.intervention.io/) to greately enhance image manipulation in K2. Using the plugin parameters you can set a number of "sets" of images. These images will be generated inside the media/rimage folder when the item is saved in K2 and their details will be saved in the database. You can then use your own method to pull their info from there, or use the supplied helpers to load them inside your template files or overrides.

The plugin also saves in the database (inside the rimage_gallery_hashes table) a hash of the filenames and filesizes of each gallery plus the set options in order to avoid regenerating new images when the galleries or settings have not changed.

## Why?

K2's image manipulation is pretty basic: you can set 6 different sizes based on width and that's it. You cannot select if you want to crop them or not, neither you can regenerate them if your settings change.

Joomlaworks solution for a gallery, Simple Image Gallery Pro, will generate thumbnails but that's pretty much it. You can't have different sizes for these thumbnails and it doesn't handle the original (full size) images at all. Plus it has no way to manage the order of the items, which this plugin does.

In a modern websites, images need to manipulated in different types of ways, especially if you include extra images for social media metadata tags and more. This plugin is made in order to manage these images in any way we needed, to remove the need for external editors.

## Install

Download the latest [K2 plugin](https://github.com/afonic/rimage-k2/archive/master.zip) and the latest [system plugin](https://github.com/afonic/rimage-system/archive/1.1.zip). Install both through the Joomla extension manager and enable them in Extensions -> Plugins.

## Usage (Backend - K2 Plugin parameters)

The plugin's parameters inside the K2 plugin. 

The options you have for each set are:

* *Set Name* The name of this set. This will be used to call the specific images later on.
* *Set type* If this set refers to K2's item image or at the item's image gallery.
* *Width / Long Edge* The required width. Set this to 0 to just optimize the file and leave the dimensions intact. If keep aspect ratio is set to yes, this is the size that the long dimension of the image will have (long edge)
* *Height* The same for height.
* *Quality* Refers to jpeg save quality.
* *Keep aspect ratio* If set to yes it will resize the image by keeping the aspect ratio. By default, it crops the image to get it to the required size.
* *K2 categories* Select the categories this set will apply to.
* *Also select subcategories* By setting this to yes, the set will apply to items that are in children categories of the ones selected.

The sets that are configured will be generated at each item save and their relevant info will be saved in the database, in the rimage_gallery_images and rimage_item_images tables.

Inside the K2 plugin you can also select between the GD library (default) or Imagemagick for image processing. Generally, GD is usually faster for resizing and creates a smaller file for the same quality setting. Imagemagick must be installed and enabled for your PHP installation, but usually generates images with better colors. You can easily switch between the two and try for yourself!

## Usage (Backend - K2 Item edit form)

This plugin adds some extra buttons in the toolbar of the K2 item edit form:

![Plugin buttons](https://i.imgur.com/RbTeVyJ.png)

*Manage gallery* is the main window of the plugin. Here you can upload / delete images and set the order of appearance.

![Manage gallery](https://i.imgur.com/kBV33qc.png)

*RImage options* is a shortcut for the plugin's options so that you don't have to go all the way to Plugins manager to change a setting. You're welcome.

*Regenerate images* will manually regenerate all images for the item you are currently editing. Usually this is done by saving the item, but in some cases you might need to force a regeneration.

*Regenerate all* will open yet another modal that allows you to regenerate every image in your website. (well obviously K2 item and gallery images as set in the image sets). This is extremely resource intesive and even though it queues each item and send consequent AJAX requests, it may still stress the server a bit too much. Please keep the window open and the tab in focus until it finishes.

## Usage (Frontend)

This plugin is designed with the developer in mind, so there is no handle of the images in the frontend. You need to generate the view in your template overrides.

The system plugin loads the composer autoload file system-wide, so the helpers classes are available using their respective namespaces in every Joomla page. Ideally, you want to use these inside K2 template overrides.

### To get an item image

Call the rImageItemHelper with the id of the item like so:

```php
$helper = new Reach\Helpers\rImageItemHelper($this->item->id);
$image = $helper->getItemSet('name');
```

The $name variable can now be used to get the image info. Using the getItemSet('name') method should return an object with the following properties:

* galleryset: The name of the set.
* path: The path to the image.
* width: The width of the image.
* height: The height of the image.
* timestamp: A crc32 hash of the date and time the image was created. You can use this to prevent browser caching.

So in our example you can display an image like so (where 'name' the name of the set you created): 

```php
<img src=<?php echo $image->path; ?>" />
```

### To get a gallery set

Similary call the helper:

```php
$helper = new Reach\Helpers\rImageItemHelper($this->item->id);
$gallery = $helper->getGallerySet('name');
```

The getGallerySet('name') will return an array of objects containing the requested set of images generated from that image's gallery. Each array item contains an object very similar with the one explained above. To use the images a simple foreach loop should be enough. Example:

```php
<?php foreach($gallery as $photo): ?>
<div>
  <img src="<?php echo $photo->path; ?>" />
</div>
<?php endforeach; ?>
```

## Notice for Simple Image Gallery Pro users

This plugin is a drop-in replacement. That means that if you unistall Sigpro and install RImage your galleries will be preserved and you will see the images right away in RImage image manager.

Of course, this will break the frontend as this plugin does not handle it at all. However it will still generate (or remove if you deleted all files) the needed {gallery}id{/gallery} text in each K2 item's gallery column, so you can implement your own frontend plugin, or even keep using Sigpro in the frontend.

To avoid confusion, this plugin hides K2's Image Gallery tab. You can change that behaviour in the system plugin's settings.

## Other methods

An rImageHelper class also exists that can be used to call Intervention Image directly for some quick and easy resizing. You can use it like so:

```php
$helper = new Reach\Helpers\rImageHelper;
<img src="<?php echo $helper->getImage('/path/relative/toJoomla/dir/image.jpg', 1024, 768, 70); ?>" />
```

The getImage method will take the path of the original image, the requested width, height and quality and will return a string containing the new image path inside the *cache* folder. This method uses the GD library and ignores the relevant K2 plugin setting.

## Issues

* The plugin won't manage the image titles and captions. _This will implemented in the future._
* It also won't delete unused files. So if you delete a file from a gallery, it will remove it from the database (meaning you won't call non existing files) but it will not remove the physical cached file. _This will implemented in the future._
* K2 will still generate its cache images, this plugin will not disable that.
* There are no plans to provide Joomla auto-update support or stable releases. Just download the repository and install using Joomla.
