<?php

namespace zibo\library\validation\validator;

use zibo\library\filesystem\File;
use zibo\library\image\Image;
use zibo\library\validation\ValidationError;
use zibo\library\Number;

use zibo\ZiboException;

use \Exception;

/**
 * Validator to check for a valid images
 */
class ImageValidator extends AbstractValidator {

    /**
     * Code for the error when the value is not a valid PHP class
     * @var string
     */
    const CODE = 'error.validation.image';

    /**
     * Code for the error when the height of the image is less then the minimum
     * @var string
     */
    const CODE_HEIGHT_MIN = 'error.validation.image.height.min';

    /**
     * Code for the error when the width of the image is less then the minimum
     * @var string
     */
    const CODE_WIDTH_MIN = 'error.validation.image.width.min';

    /**
     * Code for the error when the height of the image exceeds the maximum
     * @var string
     */
    const CODE_HEIGHT_MAX = 'error.validation.image.height.max';

    /**
     * Code for the error when the width of the image exceeds the maximum
     * @var string
     */
    const CODE_WIDTH_MAX = 'error.validation.image.width.max';

    /**
     * Message for the error when the value is not a valid PHP class
     * @var string
     */
    const MESSAGE = 'Invalid image provided: %message%';

    /**
     * Message for the error when the height of the image exceeds the maximum
     * @var string
     */
    const MESSAGE_HEIGHT_MIN = 'The height of the image is less then %height%px';

    /**
     * Message for the error when the width of the image exceeds the maximum
     * @var string
     */
    const MESSAGE_WIDTH_MIN = 'The width of the image is less then %width%px';

    /**
     * Message for the error when the height of the image exceeds the maximum
     * @var string
     */
    const MESSAGE_HEIGHT_MAX = 'The height of the image exceeds %height%px';

    /**
     * Message for the error when the width of the image exceeds the maximum
     * @var string
     */
    const MESSAGE_WIDTH_MAX = 'The width of the image exceeds %width%px';

    /**
     * Option key to see if a value is required
     * @var string
     */
    const OPTION_REQUIRED = 'required';

    /**
     * Option key for the minimum height
     * @var string
     */
    const OPTION_HEIGHT_MIN = 'height.min';

    /**
     * Option key for the minimum width
     * @var string
     */
    const OPTION_WIDTH_MIN = 'width.min';

    /**
     * Option key for the maximum height
     * @var string
     */
    const OPTION_HEIGHT_MAX = 'height.max';

    /**
     * Option key for the maximum width
     * @var string
     */
    const OPTION_WIDTH_MAX = 'width.max';

    /**
     * Flag to see if a value is required
     * @var boolean
     */
    protected $isRequired;

    /**
     * Minimum height for the image
     * @var integer
     */
    protected $minHeight;

    /**
     * Minimum width for the image
     * @var integer
     */
    protected $minWidth;

    /**
     * Maximum height for the image
     * @var integer
     */
    protected $maxHeight;

    /**
     * Maximum width for the image
     * @var integer
     */
    protected $maxWidth;

    /**
     * Construct a new image validator
     * @param array $options options for this validator
     * @return null
     */
    public function __construct(array $options = array()) {
        parent::__construct($options);

        $this->isRequired = false;
        if (array_key_exists(self::OPTION_REQUIRED, $options)) {
            $this->isRequired = $options[self::OPTION_REQUIRED];
        }

        $this->minHeight = null;
        if (array_key_exists(self::OPTION_HEIGHT_MIN, $options)) {
            $this->minHeight = $options[self::OPTION_HEIGHT_MIN];
            if ($this->minHeight != '0' && Number::isNegative($this->minHeight)) {
                throw new ZiboException('Provided minimum height is invalid: ' . $this->minHeight);
            }
        }

        $this->minWidth = null;
        if (array_key_exists(self::OPTION_WIDTH_MIN, $options)) {
            $this->minWidth = $options[self::OPTION_WIDTH_MIN];
            if ($this->minWidth != '0' && Number::isNegative($this->minWidth)) {
                throw new ZiboException('Provided maximum width is invalid: ' . $this->minWidth);
            }
        }

        $this->maxHeight = null;
        if (array_key_exists(self::OPTION_HEIGHT_MAX, $options)) {
            $this->maxHeight = $options[self::OPTION_HEIGHT_MAX];
            if ($this->maxHeight != '0' && Number::isNegative($this->maxHeight)) {
                throw new ZiboException('Provided maximum height is invalid: ' . $this->maxHeight);
            }
        }

        $this->maxWidth = null;
        if (array_key_exists(self::OPTION_WIDTH_MAX, $options)) {
            $this->maxWidth = $options[self::OPTION_WIDTH_MAX];
            if ($this->maxWidth != '0' && Number::isNegative($this->maxWidth)) {
                throw new ZiboException('Provided maximum width is invalid: ' . $this->maxWidth);
            }
        }
    }

    /**
     * Checks whether a value is a valid image
     * @param string $value Path to the image
     * @return boolean true if the value is a valid image, false otherwise
     */
    public function isValid($value) {
        $this->resetErrors();

        if (!$this->isRequired && empty($value)) {
            return true;
        }

        $parameters = array(
           'value' => $value,
        );

        try {
            $file = new File($value);
            $image = new Image($file);
        } catch (Exception $exception) {
            $parameters['message'] = $exception->getMessage();

            $error = new ValidationError(self::CODE, self::MESSAGE, $parameters);
            $this->addError($error);

            return false;
        }

        if ($this->minWidth && $image->getWidth() < $this->minWidth) {
            $parameters['width'] = $this->minWidth;

            $error = new ValidationError(self::CODE_WIDTH_MIN, self::MESSAGE_WIDTH_MIN, $parameters);
            $this->addError($error);
        }

        if ($this->minHeight && $image->getHeight() < $this->minHeight) {
            $parameters['height'] = $this->minHeight;

            $error = new ValidationError(self::CODE_HEIGHT_MIN, self::MESSAGE_HEIGHT_MIN, $parameters);
            $this->addError($error);
        }

        if ($this->maxWidth && $image->getWidth() > $this->maxWidth) {
            $parameters['width'] = $this->maxWidth;

            $error = new ValidationError(self::CODE_WIDTH_MAX, self::MESSAGE_WIDTH_MAX, $parameters);
            $this->addError($error);
        }

        if ($this->maxHeight && $image->getHeight() > $this->maxHeight) {
            $parameters['height'] = $this->maxHeight;

            $error = new ValidationError(self::CODE_HEIGHT_MAX, self::MESSAGE_HEIGHT_MAX, $parameters);
            $this->addError($error);
        }

        if ($this->errors) {
            return false;
        }

        return true;
    }

}