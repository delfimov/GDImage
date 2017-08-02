<?php
use PHPUnit\Framework\TestCase;
use delfimov\GDImage\GDImage;

/**
 * @covers delfimov\GDImage\GDImage
 */

class TranslateTest extends TestCase
{

    const TEST_JPEG = 'test.jpg';
    const TEST_PNG = 'test.png';
    const TEST_GIF = 'test.gif';

    public function testCanBeCreated()
    {
        $translate = new GDImage(__DIR__ . DIRECTORY_SEPARATOR . self::TEST_JPEG);
        $this->assertEquals(true, $translate instanceof GDImage);
    }


}