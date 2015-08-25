<?php

namespace zibo\library\html\form\field;

use zibo\library\filesystem\File;
use zibo\library\html\Image as HtmlImage;
use zibo\library\image\thumbnail\ThumbnailFactory;
use zibo\library\image\Image as CoreImage;
use zibo\library\validation\exception\ValidationException;
use zibo\library\validation\validator\ImageValidator;

use zibo\ZiboException;

use Exception;

/**
 * Form field implementation to upload a image
 */
class ImageField extends FileField {

    /**
     * The minimum width for the image
     * @var integer
     */
    private $minWidth = 0;

    /**
     * The minimum height for the image
     * @var integer
     */
    private $minHeight = 0;

    /**
     * The maximum width for the image
     * @var integer
     */
    private $maxWidth = 1500;

    /**
     * The maximum height for the image
     * @var integer
     */
    private $maxHeight = 1500;

    /**
     * The preview width for the image
     * @var integer
     */
    private $previewWidth = 100;

    /**
     * The preview height for the image
     * @var integer
     */
    private $previewHeight = 100;

    /**
     * Sets the minimum width and height for the image
     * @param integer $width
     * @param integer $height
     * @return null
     */
    public function setMinimumDimension($width, $height) {
        $this->minWidth = $width;
        $this->minHeight = $height;
    }

    /**
     * Sets the maximum width and height for the image
     * @param integer $width
     * @param integer $height
     * @return null
     */
    public function setMaximumDimension($width, $height) {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
    }

    /**
     * Sets the maximum width and height for the image
     * @param integer $width
     * @param integer $height
     * @return null
     */
    public function setPreviewDimension($width, $height) {
        $this->previewWidth = $width;
        $this->previewHeight = $height;
    }

    /**
     * Perform the validation on this field through the added validators
     * @param zibo\library\validation\exception\ValidationException $exception
     * @return zibo\library\validation\exception\ValidationException
     */
    public function validate(ValidationException $exception = null) {
        $exception = parent::validate($exception);

        $options = array();
        if ($this->minWidth) {
            $options[ImageValidator::OPTION_WIDTH_MIN] = $this->minWidth;
        }
        if ($this->minHeight) {
            $options[ImageValidator::OPTION_HEIGHT_MIN] = $this->minHeight;
        }
        if ($this->maxWidth) {
            $options[ImageValidator::OPTION_WIDTH_MAX] = $this->maxWidth;
        }
        if ($this->maxHeight) {
            $options[ImageValidator::OPTION_HEIGHT_MAX] = $this->maxHeight;
        }

        $validator = new ImageValidator($options);
        if (!$validator->isValid($this->getValue())) {
            $file = new File($this->getValue());
            if ($file->exists()) {
                $file->delete();
            }

            $this->setValue(null);
            $this->appendToClass(Field::CLASS_VALIDATION_ERROR);

            $exception->addErrors($this->getName(), $validator->getErrors());
        }

        return $exception;
    }

    /**
     * Gets a preview of the provided value
     * @param string $value Path to the current file
     * @return string HTML of the value
     */
    protected function getPreviewHtml($value) {
        try {
            $file = new File($value);
            $image = new CoreImage($file);
        } catch (Exception $e) {
            return;
        }

        $image = new HtmlImage($value);
        $image->setThumbnailer(ThumbnailFactory::CROP, $this->previewWidth, $this->previewHeight);

        return '<span class="image">' . $image->getHtml() . '</span>';
    }

}