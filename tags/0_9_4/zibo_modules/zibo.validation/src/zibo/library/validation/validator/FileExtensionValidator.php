<?php

namespace zibo\library\validation\validator;

use zibo\library\filesystem\File;
use zibo\library\validation\ValidationError;

use zibo\ZiboException;

/**
 * Validator to check if a filename has a certain extension
 */
class FileExtensionValidator extends AbstractValidator {

    /**
     * Code of the error when the filename has not a valid extension
     * @var string
     */
    const CODE = 'error.validation.file.extension';

    /**
     * Message of the error when the filename has not a valid extension
     * @var sting
     */
    const MESSAGE = '%value% should have one of the following extensions: %extensions%';

    /**
     * Option key for the extensions
     * @var string
     */
    const OPTION_EXTENSIONS = 'extensions';

    /**
     * Option key to see if a value is required
     * @var string
     */
    const OPTION_REQUIRED = 'required';

    /**
     * Extensions to check
     * @var array
     */
    private $extensions;

    /**
     * Flag to see if a value is required
     * @var boolean
     */
    private $isRequired;

    /**
     * Construct a new file extension validator
     * @param array $options options for this validator
     * @return null
     */
    public function __construct(array $options = array()) {
        parent::__construct($options);

        $this->extensions = array();
        if (isset($options[self::OPTION_EXTENSIONS])) {
            $extensions = explode(',', $options[self::OPTION_EXTENSIONS]);
            foreach ($extensions as $extension) {
                $extension = trim($extension);
                $this->extensions[$extension] = $extension;
            }
        }

        $this->isRequired = true;
        if (isset($options[self::OPTION_REQUIRED])) {
            $this->isRequired = $options[self::OPTION_REQUIRED];
        }
    }

    /**
     * Check whether the value has a valid extension
     * @param mixed $value
     * @return boolean true when the value has a valid extension, false otherwise
     */
    public function isValid($value) {
        $isEmpty = empty($value);
        if (!$this->isRequired && $isEmpty) {
            return true;
        } elseif ($isEmpty) {
            $this->addValidationError(RequiredValidator::CODE, RequiredValidator::MESSAGE, array());
            return false;
        }

        $file = new File($value);
        $extension = $file->getExtension();

        if (!$extension || (!empty($this->extensions) && !isset($this->extensions[$extension]))) {
            $parameters = array(
                'value' => $value,
                'extensions' => implode(',', $this->extensions),
            );
            $this->addValidationError(self::CODE, self::MESSAGE, $parameters);
            return false;
        }

        return true;
    }

}