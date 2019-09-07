<?php

namespace Onnov\DetectEncoding\Tests;

use InvalidArgumentException;
use Onnov\DetectEncoding\EncodingDetector;
use PHPUnit\Framework\TestCase;

class EncodingDetectorTest extends TestCase
{
    public function testInstance()
    {
        $encodingDetector = new EncodingDetector();
        $this->assertInstanceOf(EncodingDetector::class, $encodingDetector);
    }

    /**
     * @dataProvider textDataProvider
     * @param string $text
     */
    public function testGetEncoding($text)
    {
        /** correct detecting */
        $encodingDetector = new EncodingDetector();
        $this->assertEquals(EncodingDetector::UTF_8, $encodingDetector->getEncoding($text));
        $textWindows1251 = iconv(EncodingDetector::UTF_8, EncodingDetector::WINDOWS_1251, $text);
        $this->assertEquals(EncodingDetector::WINDOWS_1251, $encodingDetector->getEncoding($textWindows1251));
        $textISO88595 = iconv(EncodingDetector::UTF_8, EncodingDetector::ISO_8859_5, $text);
        $this->assertEquals(EncodingDetector::ISO_8859_5, $encodingDetector->getEncoding($textISO88595));
        $textKOI8R = iconv(EncodingDetector::UTF_8, EncodingDetector::KOI8_R, $text);

        /** detects as windows-1251 */
        $textIBM866 = iconv(EncodingDetector::UTF_8, EncodingDetector::IBM866, $text);
        // $this->assertEquals(EncodingDetector::IBM866, $encodingDetector->getEncoding($textIBM866));
        $this->assertEquals(EncodingDetector::KOI8_R, $encodingDetector->getEncoding($textKOI8R));
        $textMACCYRILLIC = iconv(EncodingDetector::UTF_8, EncodingDetector::MAC_CYRILLIC, $text);
        // $this->assertEquals(EncodingDetector::MAC_CYRILLIC, $encodingDetector->getEncoding($textMACCYRILLIC));
    }

    public function testAddEncoding()
    {
        $encodingDetector = new EncodingDetector();
        $encodingDetector->addEncoding(['upper' => '1-50,200-250,253', 'lower' => '55-100,120-180,199']);
        // how to check if that was successful?
        $this->assertTrue(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('range config must be an array');
        $this->expectExceptionCode(0);
        $encodingDetector->addEncoding(null);
    }

    /**
     * @dataProvider encodingDataProvider
     * @param string|null $encoding
     */
    public function testDisableEncoding($encoding)
    {
        $encodingDetector = new EncodingDetector();
        $encodingDetector->disableEncoding([$encoding]);
        // how to check if that was successful?
        $this->assertTrue(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Encoding List must be an array');
        $this->expectExceptionCode(0);
        $encodingDetector->disableEncoding(1);
    }

    /**
     * @dataProvider encodingDataProvider
     * @param string|null $encoding
     */
    public function testEnableEncoding($encoding)
    {
        $encodingDetector = new EncodingDetector();
        $encodingDetector->enableEncoding([$encoding]);
        // how to check if that was successful?
        $this->assertTrue(true);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Encoding List must be an array');
        $this->expectExceptionCode(0);
        $encodingDetector->enableEncoding(1);
    }

    /**
     * @dataProvider textDataProvider
     * @param string $text
     */
    public function testIconvXtoEncoding($text)
    {
        $encodingDetector = new EncodingDetector();
        $this->assertSame($text, $encodingDetector->iconvXtoEncoding($text));
        $textKOI8R = iconv(EncodingDetector::UTF_8, EncodingDetector::KOI8_R, $text);
        $this->assertSame($textKOI8R, $encodingDetector->iconvXtoEncoding($text, '//IGNORE', EncodingDetector::KOI8_R));
        // need to write test correctly and with other arguments combinations
    }

    public function textDataProvider()
    {
        yield ['Проверяемый текст'];
        yield ['Длинный проверяемый текст. Длинный проверяемый текст. Длинный проверяемый текст.'];
    }

    public function encodingDataProvider()
    {
        yield [EncodingDetector::UTF_8];
        yield [EncodingDetector::WINDOWS_1251];
        yield [EncodingDetector::ISO_8859_5];
        yield [EncodingDetector::KOI8_R];
        yield [EncodingDetector::MAC_CYRILLIC];
        yield [EncodingDetector::IBM866];
        yield ['unknown'];
        yield [null];
    }
}