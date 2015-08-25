<?php

namespace zibo\library\excel\format;

use zibo\test\BaseTestCase;

use zibo\ZiboException;

class FormatTest extends BaseTestCase {

	private $format;

	public function setUp() {
		$this->format = new Format();
	}

	public function testSetAlign() {
		$this->format->setAlign(Format::ALIGN_CENTER);
		$align = $this->format->getAlign();
		$this->assertEquals($align, Format::ALIGN_CENTER);
	}

	public function testSetAlignThrowsExceptionWhenInvalidAlignProvided() {
		try {
            $this->format->setAlign('test');
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	public function testSetBorderType() {
		$this->format->setBorderType(Format::BORDER_DOTTED);
		$borderType = $this->format->getBorderType();
		$this->assertEquals($borderType, Format::BORDER_DOTTED);
		$this->format->setBorderType(Format::BORDER_NONE);
		$borderType = $this->format->getBorderType();
		$this->assertEquals($borderType, Format::BORDER_NONE);
	}

	public function testSetBorderTypeThrowsExceptionWhenInvalidBorderTypeProvided() {
		try {
            $this->format->setBorderType('test');
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	public function testSetBorderColor() {
		$color = '#336699';
		$this->format->setBorderColor($color);
		$borderColor = $this->format->getBorderColor();
		$this->assertEquals($color, $borderColor);
	}

	public function testSetBorderColorThrowsExceptionWhenInvalidBorderColorProvided() {
		try {
            $this->format->setBorderColor('test');
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	public function testSetFont() {
		$font = 'arial';
		$this->format->setFont($font);
		$formatFont = $this->format->getFont();
		$this->assertEquals($font, $formatFont);
	}

	public function testSetFontThrowsExceptionWhenEmptyValueProvided() {
		try {
            $this->format->setFont('');
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	public function testSetTextColor() {
		$color = '#336699';
		$this->format->setTextColor($color);
		$borderColor = $this->format->getTextColor();
		$this->assertEquals($color, $borderColor);
	}

	public function testSetTextColorThrowsExceptionWhenInvalidTextColorProvided() {
		try {
            $this->format->setTextColor('test');
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	public function testSetTextSize() {
		$size = '15';
		$this->format->setTextSize($size);
		$textSize = $this->format->getTextSize();
		$this->assertEquals($size, $textSize);
	}

	/**
     * @dataProvider providerSetTextSizeThrowsExceptionWhenInvalidTextSizeProvided
	 */
	public function testSetTextSizeThrowsExceptionWhenInvalidTextSizeProvided($value) {
		try {
            $this->format->setTextSize($value);
		} catch (ZiboException $e) {
			return;
		}
		$this->fail();
	}

	public function providerSetTextSizeThrowsExceptionWhenInvalidTextSizeProvided() {
		return array(
            array('test'),
            array(-50),
		);
	}

	public function testSetTextWeight() {
        $this->format->setTextWeight(Format::WEIGHT_BOLD);
        $weight = $this->format->getTextWeight();
        $this->assertEquals($weight, Format::WEIGHT_BOLD);
        $this->format->setTextWeight(Format::WEIGHT_NORMAL);
        $weight = $this->format->getTextWeight();
        $this->assertEquals($weight, Format::WEIGHT_NORMAL);
    }

    public function testSetTextWeightThrowsExceptionWhenInvalidTextWeightProvided() {
        try {
            $this->format->setTextWeight('test');
        } catch (ZiboException $e) {
            return;
        }
        $this->fail();
    }

}