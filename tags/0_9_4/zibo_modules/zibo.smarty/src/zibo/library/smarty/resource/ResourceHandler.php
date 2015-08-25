<?php

namespace zibo\library\smarty\resource;

use zibo\core\Zibo;

use zibo\library\filesystem\File;

/**
 * Default resource handler for Smarty according to the Zibo standards
 */
class ResourceHandler {

    /**
     * Extension suffix to be added to the name of the source
     * @var string
     */
    const EXTENSION = '.tpl';

    /**
     * Get the source file of a template
     * @param string $name relative path of the template to the view folder without the extension
     * @param string $source content of the template source file
     * @param Smarty $smarty smarty instance requesting the template
     * @return boolean true if template is found, false otherwise
     */
    public function getSource($name, &$source, &$smarty) {
        $templateFile = $this->getFile($name, $smarty);
        if ($templateFile != null) {
            $source = $templateFile->read();
            return true;
        }

        return false;
    }

    /**
     * Get the modification date of a template
     * @param string $name relative path of the template to the view folder without the extension
     * @param int $timestamp timestamp of the last modification date
     * @param Smarty $smarty smarty instance requesting the template
     * @return boolean true if template is found, false otherwise
     */
    public function getTimestamp($name, &$timestamp, &$smarty) {
        $templateFile = $this->getFile($name, $smarty);
        if ($templateFile != null) {
            $timestamp = $templateFile->getModificationTime();
            return true;
        }

        return false;
    }

    /**
     * Check whether a given template is secure
     * @param string $name relative path of the template to the view folder without the extension
     * @param Smarty $smarty smarty instance requesting the template
     * @return boolean true if template is secure, false otherwise
     */
    public function isSecure($name, &$smarty) {
        return true;
    }

    /**
     * Check whether a given template is trusted
     * @param string $name relative path of the template to the view folder without the extension
     * @param Smarty $smarty smarty instance requesting the template
     * @return boolean true if template is trusted, false otherwise
     */
    public function isTrusted($name, &$smarty) {
        return true;
    }

    /**
     * Get the source file of a template
     * @param string $name relative path of the template to the view folder without the extension
     * @return zibo\library\filesystem\File instance of a File if the source is found, null otherwise
     */
    protected function getFile($name, $smarty) {
        return Zibo::getInstance()->getFile(Zibo::DIRECTORY_VIEW . File::DIRECTORY_SEPARATOR . $name . self::EXTENSION);
    }

}