<?php
/**
 * GDImage class
 *
 * PHP version 5
 *
 * @category GDImage
 * @package  GDImage\GDImage
 * @author   Dmitry Elfimov <elfimov@gmail.com>
 * @license  MIT License
 * @link     https://github.com/delfimov/GDImage/
 */

/**
 * Example:
 *
    // resize with smart crop, add logo and save
    include 'GDImage.php';

    $logo = new GDImage('logo.png');
    $logo->opacity(30)->save('newlogo.png');

    $image = new GDImage('test.jpg');
    $image->resize(300, 200, true)
        ->merge($logo, 'right', 'bottom')
        ->save('new.jpg');
 */

namespace DElfimov\GDImage;

/**
 * Class GDImage
 *
 * @category GDImage
 * @package  DElfimov\GDImage
 * @author   Dmitry Elfimov <elfimov@gmail.com>
 * @license  MIT License
 * @link     https://github.com/delfimov/GDImage/
 */
class GDImage
{

    /**
     * Default memory limit
     *
     * @var int
     */
    public $memoryLimit = 268435456; // 256 Mb

    /**
     * RGB fill color
     *
     * @var array
     */
    public $fillColor = array(0, 0, 0);

    /**
     * JPEG quality for output file, 0-100, where 100 is best quality
     *
     * @var int
     */
    public $jpgQuality = 80;

    /**
     * PNG compression, 0-9, from 0 (no compression) to 9
     *
     * @var int
     */
    public $pngQuality = 0;

    /**
     * An image resource, returned by one of the image creation functions
     *
     * @var null|resource
     */
    public $image = null;

    /**
     * Alphablending
     *
     * @var bool
     */
    protected $alphablending = false;

    /**
     * Save alpha from source
     *
     * @var bool
     */
    protected $savealpha = false;

    /**
     * Errors
     *
     * @var array
     */
    public $error = array();

    /**
     * Source image type
     *
     * @var null|string
     */
    protected $type = null;

    /**
     * Source image width
     *
     * @var null|int
     */
    protected $width = null;

    /**
     * Source image height
     *
     * @var null|int
     */
    protected $height = null;

    /**
     * Source image file name
     *
     * @var null|string
     */
    protected $fileName = null;
    
    /**
     * Constructor.
     *
     * @param string $fileName Source image file name
     */
    public function __construct($fileName)
    {
        if (!function_exists('imagecreatefrompng')) {
            $this->error[] = 'GD is not available';
            return false;
        }

        // get real memory limit
        $memLimit = ini_get('memory_limit');
        if (preg_match('/^(\d+)(.)$/', $memLimit, $matches)) {
            if ($matches[2] == 'G') {
                $memLimit = $matches[1] * 1024 * 1024 * 1024; // nnnG -> nnn GB
            } else if ($matches[2] == 'M') {
                $memLimit = $matches[1] * 1024 * 1024; // nnnM -> nnn MB
            } else if ($matches[2] == 'K') {
                $memLimit = $matches[1] * 1024; // nnnK -> nnn KB
            }
        }
        $memLimit = (int) $memLimit;
        $this->memoryLimit = empty($memLimit) ? $this->memoryLimit : $memLimit;


        $this->getImage($fileName);
    }


    /**
     * Create image from source
     *
     * @param string $fileName Source image file name
     *
     * @return $this|bool
     */
    public function getImage($fileName)
    {
        if (!file_exists($fileName)) {
            $this->error[] = 'Image ' . $fileName . ' is not exists';
            return false;
        }
       
        $info = getimagesize($fileName);

        $channels = empty($info['channels']) ? 3 : $info['channels'];
        $bits = empty($info['bits']) ? 8 : $info['bits'];

        if (($info[0] * $info[1] * ($channels * $bits / 8)) > $this->memoryLimit) {
            $this->error[] = 'image is larger then '
                . $this->memoryLimit . ' (memory limit)';
            return false;
        }

        $this->width = $info[0];
        $this->height = $info[1];
        $this->fileName = $fileName;

        // create new image from fileName
        switch($info[2]) {
        case IMAGETYPE_GIF:
            $this->type = 'gif';
            $this->image = imagecreatefromgif($fileName);
            break;
        case IMAGETYPE_JPEG:
            $this->type = 'jpg';
            $this->image = imagecreatefromjpeg($fileName);
            break;
        case IMAGETYPE_PNG:
            $this->type = 'png';
            $this->image = imagecreatefrompng($fileName);
            break;
        default:
            $this->error[] = 'Supported formats are gif, jpg & png only';
            return false; // gif, jpg & png only
            break;
        }

        return $this;
    }


    /**
     * Crop image
     *
     * @param int $x1 crop start X position
     * @param int $y1 crop start Y position
     * @param int $x2 crop end X position
     * @param int $y2 crop end Y position
     *
     * @return $this|bool
     */
    public function crop($x1, $y1, $x2, $y2)
    {
        if (empty($this->image)) {
            return false;
        }
        
        if ($x1 > $x2) {
            $temp = $x1;
            $x1 = $x2;
            $x2 = $temp;
        }
        
        if ($y1 > $y2) {
            $temp = $y1;
            $y1 = $y2;
            $y2 = $temp;
        }
        
        $width = $x2 - $x1;
        $height = $y2 - $y1;
        
        $image = imagecreatetruecolor($width, $height);
        
        $this->setAlpha($this->image, $this->alphablending, $this->savealpha);
        
        imagecopy($image, $this->image, 0, 0, $x1, $y1, $width, $height);
        
        $this->image = $image;
        $this->width = $width;
        $this->height = $height;
        
        return $this;
    }



    /**
     * Resize image
     *
     * @param int  $width        new width
     * @param int  $height       new height
     * @param bool $crop         if true - crop image, otherwise - cover
     * @param bool $proportional stretch with source aspect ratio or not
     *
     * @return $this|bool
     */
    public function resize($width, $height, $crop = false, $proportional = true)
    {
        if (empty($this->image)) {
            return false;
        }
    
        if ($width == $this->width && $this->height == $height) { 
            // nothing to do
        } else if ($this->width > $width && $this->height == $height && $crop) {
            $x = round(($this->width - $width)/2);
            $this->crop($x, 0, $x + $width, $height);
        } else if ($this->height > $height && $this->width == $width && $crop) {
            $y = round(($this->height - $height)/2);
            $this->crop(0, $y, $width, $height + $y);
        } else if ($width > $this->width && $this->height == $height && !$crop) {
            $x = round(($width - $this->width)/2);
            $newImage = imagecreatetruecolor($width, $height);
            if (!$this->alphablending) {
                imagefill(
                    $newImage,
                    0,
                    0,
                    imagecolorallocate(
                        $newImage,
                        $this->fillColor[0],
                        $this->fillColor[1],
                        $this->fillColor[2]
                    )
                );
            }
            imagecopy(
                $newImage,
                $this->image,
                $x,
                0,
                0,
                0,
                $this->width,
                $this->height
            );
            $this->image = $newImage;
            $this->width = $width;
        } else if ($height > $this->height && $this->width == $width && !$crop) {
            $y = round(($height - $this->height)/2);
            $newImage = imagecreatetruecolor($width, $height);
            if (!$this->alphablending) {
                imagefill(
                    $newImage,
                    0,
                    0,
                    imagecolorallocate(
                        $newImage,
                        $this->fillColor[0],
                        $this->fillColor[1],
                        $this->fillColor[2]
                    )
                );
            }
            imagecopy(
                $newImage,
                $this->image,
                0,
                $y,
                0,
                0,
                $this->width,
                $this->height
            );
            $this->image = $newImage;
            $this->width = $width;
            
        } else {
    
            $srcX = 0;
            $srcY = 0;
            $dstX = 0;
            $dstY = 0;

            if ($width == 0) {
                $width = $this->width;
            }
            
            if ($height == 0) {
                $height = $this->height;
            }
            
            $oldRatio = round($this->width / $this->height, 2);
            $newRatio = round($width / $height, 2);
            
            $srcHeight = $this->height;
            $srcWidth = $this->width;
           
            $dstHeight = $height;
            $dstWidth = $width;
            
            if ($proportional) {
                if ($oldRatio > $newRatio) { // album to book
                    if ($crop) {
                        $hr = $this->height * $newRatio;
                        $srcWidth = round($hr);
                        $srcX = round(($this->width - $hr) / 2);
                    } else {
                        $dstHeight = round($width / $oldRatio);
                        $dstY = round(($height - $dstHeight) / 2);
                    }
                } else if ($oldRatio < $newRatio) { // book to album
                    if ($crop) {
                        $wr = $this->width / $newRatio;
                        $srcY = round(($this->height - $wr) / 2);
                        $srcHeight = round($wr);
                    } else {
                        $dstWidth = round($height * $oldRatio);
                        $dstX = round(($width - $dstWidth) / 2);
                    }
                }
            }

            $newImage = imagecreatetruecolor($width, $height);
            
            $this->setAlpha($newImage, $this->alphablending, $this->savealpha);

            
            if ($this->alphablending) {
                $color = imagecolorallocatealpha(
                    $newImage,
                    0,
                    0,
                    0,
                    127
                );
                imagefill($newImage, 0, 0, $color);
            } else if ($dstY > 0 || $dstX > 0) {
                $color = imagecolorallocate(
                    $newImage,
                    $this->fillColor[0],
                    $this->fillColor[1],
                    $this->fillColor[2]
                );
                imagefill($newImage, 0, 0, $color);
            }

            
            imagecopyresampled(
                $newImage,
                $this->image,
                $dstX,
                $dstY,
                $srcX,
                $srcY,
                $dstWidth,
                $dstHeight,
                $srcWidth,
                $srcHeight
            );
            
            $this->image = $newImage;
            $this->width = $width;
            $this->height = $height;
        }

        return $this;
    
    }


    /**
     * Merge image with overlay
     *
     * @param GDImage|string $overlay image
     * @param int            $posX    position X
     * @param int            $posY    position Y
     *
     * @return $this|bool
     */
    public function merge($overlay, $posX, $posY)
    {
        if (empty($this->image)) {
            return false;
        }
        
        if (is_string($overlay)) {
            $overlay = new GDImage($overlay);
        }
        
        if (!is_int($posX)) {
            if ($posX == 'left') {
                $posX = 0;
            } else if ($posX == 'right') {
                $posX = $this->width - $overlay->width;
            } else if ($posX == 'center') {
                $posX = round(($this->width - $overlay->width)/2);
            }
        }
        
        if (!is_int($posY)) {
            if ($posY == 'top') {
                $posY = 0;
            } else if ($posY == 'bottom') {
                $posY = $this->height - $overlay->height;
            } else if ($posY == 'center') {
                $posY = round(($this->height - $overlay->height)/2);
            }
        }

        $this->setAlpha($this->image, $this->alphablending, $this->savealpha);
        
        imagecopy(
            $this->image,
            $overlay->image,
            $posX,
            $posY,
            0,
            0,
            $overlay->width,
            $overlay->height
        );
        
        return $this;
    }


    /**
     * Set image opactity
     *
     * @param int $opacity from 0 to 100
     *
     * @return $this|bool
     */
    public function opacity($opacity)
    {
        if (empty($this->image)) {
            return false;
        }
        
        // $this->setAlpha($this->image, false, true);
        imagesavealpha($this->image, true); 
        imagealphablending($this->image, false);
        
        $opacity /= 100;
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $color = imagecolorat($this->image, $x, $y);
                $alpha = ($color >> 24) & 0xFF;
                $alpha = 127 + $opacity * ($alpha - 127);
                $colorWithAlpha = imagecolorallocatealpha(
                    $this->image,
                    ($color >> 16) & 0xFF,
                    ($color >> 8) & 0xFF,
                    $color & 0xFF,
                    $alpha
                );
                // get the new color with alpha and set it.
                imagesetpixel(
                    $this->image,
                    $x,
                    $y,
                    $colorWithAlpha
                ); // set new pixel
            }
        }
        return $this;
    }


    /**
     * Set alpha channel properties for image
     *
     * @param bool $alphablending blending
     * @param bool $savealpha     save
     *
     * @return $this
     */
    public function alpha($alphablending = true, $savealpha = true)
    {
        $this->alphablending = $alphablending;
        $this->savealpha = $savealpha;
        return $this;
    }

    /**
     * Set alpha channel properties for image
     *
     * @param resource $image    GD library resource
     * @param bool     $blending blending
     * @param bool     $save     save
     *
     * @return bool
     */
    protected function setAlpha($image, $blending, $save)
    {
        imagealphablending($image, $blending);
        imagesavealpha($image, $save);
        return true;
    }


    /**
     * Save image
     *
     * @param string      $fileName new image file name
     * @param null|string $format   new image format,
     *                              if not set, will be detected from file name
     *
     * @return bool|GDImage
     */
    public function save($fileName, $format = null)
    {

        if (empty($this->image)) {
            return false;
        }
        
        if (empty($format)) {
            $dotPos = strrpos($fileName, '.');
            $format = strtolower($dotPos < 1 ? '' : substr($fileName, $dotPos + 1));
        }

        switch ($format) {
        case 'gif':
            $result = imagegif($this->image, $fileName);
            break;
        case 'png8':
        case 'png24':
        case 'png':
            $result = imagepng($this->image, $fileName, $this->pngQuality);
            break;
        case 'jpeg':
        case 'jpg':
        default:
            $result = imagejpeg($this->image, $fileName, $this->jpgQuality);
            break;
        }
        return empty($result) ? false : $this;
    }


    /**
     * Set background fill color
     *
     * @param array $color RGB array [r,g,b]
     *
     * @return bool
     */
    public function setFillColor(array $color)
    {
        $this->fillColor = $color;
        return true;
    }


}
