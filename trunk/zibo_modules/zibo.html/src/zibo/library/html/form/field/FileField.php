<?php

namespace zibo\library\html\form\field;

use zibo\library\filesystem\File;
use zibo\library\Boolean;
use zibo\library\String;

use zibo\ZiboException;

/**
 * Form field implementation to upload a file
 */
class FileField extends AbstractArrayField {

    /**
     * Default value for an empty value
     * @var string
     */
    const DEFAULT_VALUE = '';

    /**
     * Suffix for the hidden field with the default value of this field
     * @var string
     */
    const SUFFIX_DEFAULT = 'Default';

    /**
     * Path to upload the files to
     * @var zibo\library\filesystem\File
     */
    private $uploadPath;

    /**
     * Flag to see if this field should overwrite existing files
     * @var boolean
     */
    private $willOverwrite;

    /**
     * Initialize this file upload field
     * @return null
     */
    protected function init() {
        $this->setUploadPath(new File('application/data'));
        $this->setWillOverwrite(false);
    }

    /**
     * Sets the default value of this field
     * @param mixed $value
     * @return null
     */
    public function setDefaultValue($value) {
        if (empty($value)) {
            $value = self::DEFAULT_VALUE;
        }

        $this->defaultValue = $value;
    }

    /**
     * Sets whether this field will overwrite existing files
     * @param boolean $flag true to overwrite, false otherwise
     * @return null
     */
    public function setWillOverwrite($flag) {
        $this->willOverwrite = Boolean::getBoolean($flag);
    }

    /**
     * Checks whether this field will overwrite existing files
     * @return boolean true if this field overwrites files, false otherwise
     */
    public function willOverwrite() {
        return $this->willOverwrite;
    }

    /**
     * Sets the upload path of this file upload field
     * @param zibo\library\filesystem\File $uploadPath
     * @return null
     * @throws zibo\ZiboException when the upload path is not writable
     */
    public function setUploadPath(File $uploadPath) {
        $this->uploadPath = $uploadPath;
    }

    /**
     * Gets the upload path of this file upload field
     * @return zibo\library\filesystem\File
     */
    public function getUploadPath() {
        return $this->uploadPath;
    }

    /**
     * Gets the HTML of this file upload field
     * @return string
     */
    public function getHtml() {
        $html =
            '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->getMaxUploadSize() . '" />' .
            '<input type="file"' .
            $this->getIdHtml() .
            $this->getNameHtml() .
            $this->getClassHtml() .
            $this->getAttributesHtml() .
            $this->getIsDisabledHtml() .
            ' />';

        $value = $this->getDisplayValue();
        if (!empty($value)) {
            $html .= '<input type="hidden" name="' . $this->getName() . self::SUFFIX_DEFAULT . '" value="' . $value . '" />';
            $html .= $this->getPreviewHtml($value);
        }

        return $html;
    }

    /**
     * Gets a preview of the provided value
     * @param string $value Path to the current file
     * @return string HTML of the value
     */
    protected function getPreviewHtml($value) {
        return '<span class="file">' . $value . '</span>';
    }

    /**
     * Process the upload and update the value of this field
     * @return null
     */
    public function processRequest() {
        if (!isset($_FILES[$this->name])) {
            $this->setValue($this->getDefaultValue());
            return;
        }

        $this->uploadPath->create();
        if (!$this->uploadPath->isWritable()) {
            throw new ZiboException('Upload path ' . $this->uploadPath->getAbsolutePath() . ' is not writable');
        }

        if (!$this->isMultiple()) {
            $formFile = $this->uploadFile($_FILES[$this->name]);
            $this->setValue($formFile);
            return;
        }

        $values = array();

        $files = $this->getMultipleFiles($_FILES[$this->name]);
        foreach ($files as $file) {
            if ($file['error'] == UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $formFile = $this->uploadFile($file);
            $values[] = $formFile;
        }

        $this->setValue($values);
    }

    /**
     * Upload a file
     * @param array $file Array with file upload details from PHP
     * @return string path to the uploaded file
     */
    private function uploadFile($file) {
        if ($file['error'] == UPLOAD_ERR_NO_FILE) {
            $default = $this->getDefaultValue();

            if (empty($default)) {
                $default = parent::getRequestValue($this->getName() . self::SUFFIX_DEFAULT);
                if (empty($default)) {
                    $default = self::DEFAULT_VALUE;
                }
            }

            return $default;
        }

        $this->isUploadError($file);

        $this->uploadPath->create();

        $uploadFileName = String::safeString($file['name']);

        $uploadFile = new File($this->uploadPath, $uploadFileName);
        if (!$this->willOverwrite()) {
            $uploadFile = $uploadFile->getCopyFile();
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadFile->getPath())) {
            throw new ZiboException('Could not move the uploaded file ' . $file['tmp_name'] . ' to ' . $uploadFile->getPath());
        }
        $uploadFile->setPermissions(0644);

        return $uploadFile->getPath();
    }

    /**
     * Converts the multiple files array
     * @param array $files Array with the files grouped by attribute, as in $_FILES
     * @return array $files Array with the files grouped by file
     */
    private function getMultipleFiles(array $files) {
        $result = array();

        foreach ($files as $paramName => $paramValue) {
            foreach ($paramValue as $file => $param) {
                if (!array_key_exists($file, $result)) {
                    $result[$file] = array();
                }

                $result[$file][$paramName] = $param;
            }
        }

        return $result;
    }

    /**
     * Checks whether a file upload error occured
     * @return null
     * @throws zibo\ZiboException when an upload error occured
     */
    private function isUploadError($file) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            return;
        }

        $message = '';
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'The uploaded file exceeds the maximum upload size';
                break;
            case UPLOAD_ERR_INI_SIZE:
                $message = 'The uploaded file was only partially uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'No temporary directory to upload the file to';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Failed to write file to disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'The upload was stopped by a PHP extension';
                break;
        }

        throw new ZiboException('Could not upload ' . $file['name'] . ': ' . $message);
    }

    /**
     * Gets the maximum upload size
     * @return int Maximum upload size in bytes
     */
    private function getMaxUploadSize() {
        $size = ini_get('upload_max_filesize');

        $size = trim($size);
        $last = strtolower($size[strlen($size) - 1]);

        switch($last) {
            case 'g':
                $size *= 1024;
            case 'm':
                $size *= 1024;
            case 'k':
                $size *= 1024;
        }

        return $size;
    }

}