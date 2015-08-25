<?php

namespace zibo\core\view;

use zibo\core\View;

use zibo\library\html\meta\Meta;

/**
 * Abstract view for html output
 */
abstract class HtmlView implements View {

    /**
     * Array with Meta objects
     * @var array
     */
    protected $meta = array();

    /**
     * Array with inline javascripts
     * @var array
     */
    protected $inlineScripts = array();

    /**
     * Array with javascript files
     * @var array
     */
    protected $scripts = array();

    /**
     * Array with css files
     * @var unknown_type
     */
    protected $styles = array();

    /**
     * Array with custom code for the head
     * @var array
     */
    protected $custom = array();

    /**
     * Adds a meta element to this view
     * @param zibo\library\html\meta\Meta $meta Meta element
     * @return null
     */
    public function addMeta(Meta $meta) {
        $this->meta[$meta->getName()] = $meta;
    }

    /**
     * Gets the meta elements of this view
     * @return array Array with Meta objects
     */
    public function getMeta() {
        return $this->meta;
    }

    /**
     * Add a inline javascript to this view
     * @param string $script some javascript code
     * @return null
     */
    public function addInlineJavascript($script) {
        $this->inlineScripts[] = $script;
    }

    /**
     * Get all the inline javascripts
     * @return array
     */
    public function getInlineJavascripts() {
        return $this->inlineScripts;
    }

    /**
     * Add a javascript file to this view
     * @param string $file reference to a javascript file, absolute or relative to the base url
     * @return null
     */
    public function addJavascript($file) {
        $this->scripts[$file] = $file;
    }

    /**
     * Get all the javascript files which are added to this view
     * @return array
     */
    public function getJavascripts() {
        return $this->scripts;
    }

    /**
     * Remove a javascript file from this view
     * @param string $file reference to the javascript file
     * @return null
     */
    public function removeJavascript($file) {
        if (array_key_exists($file, $this->scripts)) {
            unset($this->scripts[$file]);
        }
    }

    /**
     * Add a stylesheet file to this view
     * @param string $file reference to a css file, absolute or relative to the base url
     * @return null
     */
    public function addStyle($file) {
        $this->styles[$file] = $file;
    }

    /**
     * Get all the stylesheets which are added to this view
     * @return array
     */
    public function getStyles() {
        return $this->styles;
    }

    /**
     * Remove a stylesheet file from this view
     * @param string $file reference to the css file
     * @return null
     */
    public function removeStyle($file) {
        if (array_key_exists($file, $this->styles)) {
            unset($this->styles[$file]);
        }
    }

    /**
     * Adds a piece of custom code for the head tag
     * @param string $code The piece of custom code to add
     * @return null
     */
    public function addCustomCodeToHead($code) {
        $this->custom[] = $code;
    }

    /**
     * Gets the pieces of custom code for the head tag
     * @return array Array with pieces of custom code
     */
    public function getCustomCodesForHead() {
        return $this->custom;
    }

}