[![StyleCI](https://styleci.io/repos/99135056/shield?branch=master)](https://styleci.io/repos/99135056)
[![Build Status](https://travis-ci.org/delfimov/GDImage.svg?branch=master)](https://travis-ci.org/delfimov/GDImage)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/delfimov/GDImage/blob/master/LICENSE)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a6ab283e-ac26-4ff2-9b71-9aa2f0a45fbc/mini.png)](https://insight.sensiolabs.com/projects/a6ab283e-ac26-4ff2-9b71-9aa2f0a45fbc)

# GDImage

Easy to use image manipulation tool based on PHP-GD extension.

## Key features

 * Easy to use.
 * JPEG, PNG, GIF support. 
 * Method chaining.
 * JPEG autorotation (ext-exif required) based on EXIF header
 * Easy to resize, crop, rotate, flip, merge, set opactity

## Requirements

 * [PHP >= 5.4](http://www.php.net/)
 * [PHP GD](http://php.net/manual/image.installation.php)

## How to install

```sh
composer require delfimov/gdimage
```

or add this line to your composer.json file:

```json
"delfimov/gdimage": "~1.0"
```


Alternatively, copy the contents of the gdimage folder into one of 
your project's directories and `require 'src/GDImage.php';`. 

## A Simple Example

```php
// initialize GDImage
$image = new GDImage('path/to/image.jpg');
 
// set fill color for empty image areas
$image->setFillColor([255, 0, 0]);

// Resize image. By default images are resized proportional and are not cropped,  
// with empty areas filled with color specified in setFillColor() method
$image->resize(1280, 720);

// Save image
$image->save('path/to/newimage.jpg');
```

## TODO

 * Examples
 * Readme
 * More unit tests
 * Support animated gifs

