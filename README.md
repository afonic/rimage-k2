## rImage for K2 and Simple Image Gallery Pro (Joomla)

This is a pair of plugins for Joomla that complement K2 and Simple Image Gallery Pro by greatly improving images handling. Please note that this plugin is made for use in-house and may or may not appeal to you. It is tailor made for our needs and it is focused on a developer's point of view and it's not meant to be used by a simple Joomla / K2 user. It takes the assumption that you have full control of whoever has access to your administrator panel. We will try to improve functionality and fix bugs but there is no guarantee this will be upgraded or that pull requests will be implemented. Please use at your own risk and with consideration of the above in mind.

## What does it do

rImage is a plugin that uses [Intervention Image](http://image.intervention.io/) to greately enhance image manipulation in K2 and Simple Image Gallery Pro. Using the plugin parameters you can set a number of "sets" of images. These images will be generated inside the media/rimage folder when the item is saved in K2 and their details will be saved in the database. You can then use your own method to pull their info from there, or use the supplied helpers to load them inside your template files or overrides.

The plugin also saves in the database (inside the rimage_gallery_hashes table) a hash of the filenames and filesizes of each gallery plus the set options in order to avoid regenerating new images when the galleries or settings have not changed.

## Why?

K2's image manipulation is pretty basic: you can set 6 different sizes based on width and that's it. You cannot select if you want to crop them or not, neither you can regenerate them if your settings change.

Simple Image Gallery Pro will generate thumbnails but that's pretty much it. You can't have different sizes for these thumbnails and it doesn't handle the original (full size) images at all.

In a modern websites, images need to manipulated in different types of ways, especially if you include extra images for social media metadata tags and more. This plugin is made in order to manage these images in any way we needed, to remove the need for external editors.

## Usage (Backend)

First you need to download this plugin as well as the [System Plugin](https://github.com/afonic/rimage-system), install them using Joomla and enable them. Then set "sets" you want to use inside the K2 plugin's parameters. 

The options you have for each set are:

* *Set Name* The name of this set. This will be used to call the specific images later on.
* *Set type* If this set refers to K2's item image or at the item's image gallery.
* *Width* The required width. Set this to 0 to just optimize the file and leave the dimensions intact.
* *Height* The same for height.
* *Quality* Refers to jpeg save quality.
* *K2 categories* Select the categories this set will apply to.

The sets that are configured will be generated at each item save and their relevant info will be saved in the database, in the rimage_gallery_images and rimage_item_images tables.

Inside the K2 plugin you can also select between the GD library (default) or Imagemagick for image processing. Generally, GD is usually faster for resizing and creates a smaller file for the same quality setting. Imagemagick must be installed and enabled for your PHP installation, but usually generates images with better colors. You can easily switch between the two and try for yourself!

There is a way to generate all images at once. Simply add

> rimage=regen

to your administrator URL (at any page) and the plugin will regenerate every single image. This will *force* regeneration even if settings or hashes are unchanged and will need a lot of resources, especialy if you have a lot of images. You should set a PHP execution limit of 2 minutes or more. Use at your own risk.

## Usage (Frontend)

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

## Other methods

An rImageHelper class also exists that can be used to call Intervention Image directly for some quick and easy resizing. You can use it like so:

```php
$helper = new Reach\Helpers\rImageHelper;
<img src="<?php echo $helper->getImage('/path/relative/toJoomla/dir/image.jpg', 1024, 768, 70); ?>" />
```

The getImage method will take the path of the original image, the requested width, height and quality and will return a string containing the new image path inside the *cache* folder. This method uses the GD library and ignores the relevant K2 plugin setting.

## Issues

* The global regeneration is too slow to be reliably used in a production enviroment.
* The plugin won't manage the image titles and captions.
* It assumes you upload valid jpg images. It won't work with png, gif etc.  _This will implemented in the future._
* It also won't delete unused files. So if you delete a file from a gallery, it will remove it from the database (meaning you won't call non existing files) but it will not remove the physical cached file. _This will implemented in the future._
* K2 will still generate its cache images and Simple Image Gallery Pro its thumbnails, this plugin will not disable that.
* There are no plans to provide Joomla auto-update support or stable releases. Just download the repository and install using Joomla.