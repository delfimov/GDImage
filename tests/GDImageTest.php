<?php
use PHPUnit\Framework\TestCase;
use delfimov\GDImage\GDImage;

/**
 * @covers delfimov\GDImage\GDImage
 */

class TranslateTest extends TestCase
{

    const TEST_IMAGES = [
        'jpg' => 'test.jpg',
        'png' => 'test.png',
        'gif' => 'test.gif',
    ];

    const TEST_IMAGES_GIF = [
        [
            'isAnimated' => false,
            'src' => 'test.png'
        ],
        [
            'isAnimated' => false,
            'src' => 'test.gif'
        ],
        [
            'isAnimated' => true,
            'src' => 'ani.gif'
        ],
    ];

    const WATERMARK = 'water.png';
    const LOGO = 'logo.png';

    protected function setUp()
    {
        $image = imagecreate(500, 500);
        try {
            imagejpeg($image, __DIR__ . '/testImage.jpg');
        } catch (\Exception $e) {
            throw new \Exception('You have no permission to create files in this directory:' . $e);
        }

        try {
            unlink(__DIR__ . '/testImage.jpg');
        } catch (\Exception $e) {
            throw new \Exception('You have no permission to delete files in this directory:' . $e);
        }
    }


    /**
     * @dataProvider imageProvider
     */
    public function testCanBeCreated($format, GDImage $image)
    {
        $this->assertEquals(true, $image instanceof GDImage);
        $this->assertEquals(true, $image->getImageType() == $format);
    }

    /**
     * @dataProvider imageProvider
     */
    public function testCanBeSavedToFile($format, GDImage $image)
    {
        $filename = __DIR__ .'/saveTest.' . $format;
        $image->save($filename, $format);
        $this->assertFileExists($filename);
        unlink($filename);
    }

    /**
     * @dataProvider animatedProvider
     */
    public function testIsAnimated($isAnimated, GDImage $image)
    {
        $this->assertEquals(true, $image instanceof GDImage);
        $this->assertEquals(true, $image->isAnimated() == $isAnimated);
    }

    /**
     * @dataProvider heavytestProvider
     */
    public function testWatermarks($src, $dst)
    {
        $width = 1920;
        $height = 1080;
        $dstRatio = $width / $height;

        $image = new GDImage($src);
        $srcRatio = $image->width / $image->height;

        $keepFullImage = rand(0, 100) > 50;
        if ($keepFullImage) {
            $image->setFillColor([255, 0, 0]);
        }

        $image->resize($width, $height, !$keepFullImage);

        $watermark = new GDImage(__DIR__ . DIRECTORY_SEPARATOR . self::WATERMARK);
        $image->merge($watermark->alpha(), 'center', 'center');
        $this->assertEquals(true, $watermark instanceof GDImage);

        $logo = new GDImage(__DIR__ . DIRECTORY_SEPARATOR . self::LOGO);

        $posX = $width - $logo->width;
        $posY = $height - $logo->height;
        if ($keepFullImage) {
            if ($srcRatio > $dstRatio) {
                $posY = $posY - ($height - $width/$srcRatio)/2;
            } else {
                $posX = $posX - ($width - $height*$srcRatio)/2;
            }
        }
        $image->merge($logo->alpha(), $posX, $posY);
        $this->assertEquals(true, $logo instanceof GDImage);

        $image->save($dst);

        $this->assertEquals(true, $image instanceof GDImage);
        $this->assertFileExists($dst);
//        unlink($dst);
    }



    public function imageProvider()
    {
        $images = [];
        foreach (self::TEST_IMAGES as $format => $image) {
            $images[] = [
                $format,
                new GDImage(__DIR__ . DIRECTORY_SEPARATOR . $image)
            ];
        }
        return $images;
    }


    public function animatedProvider()
    {
        $images = [];
        foreach (self::TEST_IMAGES_GIF as $image) {
            $images[] = [
                $image['isAnimated'],
                new GDImage(__DIR__ . DIRECTORY_SEPARATOR . $image['src'])
            ];
        }
        return $images;
    }


    public function heavytestProvider()
    {
        $images = [];
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(__DIR__ . '/in', \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $fileinfo) {
            $filename = $fileinfo->getFilename();
            if ($filename[0] != '.') {
                if ($fileinfo->isDir()) {
                } else {
                    $images[] = [
                        $fileinfo->getRealPath(),
                        strtr($fileinfo->getRealPath(), ['in' => 'out'])
                    ];
                }
            }
        }
        return $images;
    }
}