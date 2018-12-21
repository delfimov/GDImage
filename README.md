[![Latest Stable Version](https://poser.pugx.org/delfimov/gdimage/v/stable)](https://packagist.org/packages/delfimov/gdimage)
[![Build Status](https://travis-ci.org/delfimov/GDImage.svg?branch=master)](https://travis-ci.org/delfimov/GDImage)
[![StyleCI](https://styleci.io/repos/99135056/shield?branch=master)](https://styleci.io/repos/99135056)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a6ab283e-ac26-4ff2-9b71-9aa2f0a45fbc/mini.png)](https://insight.sensiolabs.com/projects/a6ab283e-ac26-4ff2-9b71-9aa2f0a45fbc)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/delfimov/GDImage/blob/master/LICENSE)

# GDImage

Easy to use image manipulation tool based on PHP-GD extension.

## Key features

 * Easy to use.
 * JPEG, PNG, GIF, WEBP support. 
 * Method chaining.
 * JPEG autorotation (ext-exif required) based on EXIF header
 * Easy to resize, crop, rotate, add text, flip, merge, set opactity

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

/* Add text to image 
The first parameter is text to add, 
the second parameter is optional, by default equals to: 
[
    'size' => 20,
    'angle' => 0,
    'x' => 0,
    'y' => 0,
    'color' => [0, 0, 0],
    'font' => '/../fonts/Roboto-Medium.ttf'
] 
*/
$image->addText(
    'Sample text to add',
    [
        'font' => __DIR__ . '/../fonts/Roboto-Medium.ttf',
        'size' => 18,
        'x' => 150,
        'y' => 100,
        'color' => [255, 0, 0]
    ]
);

// Save image
$image->save('path/to/newimage.jpg');
```

## TODO

 * Examples
 * Readme
 * More unit tests
 * Animated gifs support

