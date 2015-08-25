<?php

namespace zibo\api\model\doc;

use zibo\ZiboException;

/**
 * Data container for parsed PhpDoc or a code block
 */
class Doc {

    /**
     * Private access level
     * @var string
     */
    const ACCESS_PRIVATE = 'private';

    /**
     * Protected access level
     * @var string
     */
    const ACCESS_PROTECTED = 'protected';

    /**
     * Public access level
     * @var string
     */
    const ACCESS_PUBLIC = 'public';

    /**
     * Short description of the doc
     * @var string
     */
    private $description;

    /**
     * Long description of the doc
     * @var string
     */
    private $longDescription;

    /**
     * Flag to see if the code block is abstract
     * @var boolean
     */
    private $isAbstract;

    /**
     * Access level of the code block
     * @var string
     */
    private $access;

    /**
     * Author of the code block
     * @var string
     */
    private $author;

    /**
     * Copyright notes of the code block
     * @var string
     */
    private $copyright;

    /**
     * Flag to mark the code block as deprecated
     * @var boolean
     */
    private $isDeprecated;

    /**
     * Message for the deprecated flag
     * @var string
     */
    private $deprecated;

    /**
     * Location of an external example
     * @var string
     */
    private $example;

    /**
     * Exceptions thrown by the code block
     * @var array
     */
    private $exceptions = array();

    /**
     * Flag to see if the code block should be ignored in the documentation
     * @var boolean
     */
    private $isIgnore;

    /**
     * An URL
     * @var string
     */
    private $link;

    /**
     * An alias for a variable
     * @var string
     */
    private $name;

    /**
     * The package of the code block
     * @var string
     */
    private $package;

    /**
     * Array containing the parameters of the code block as DocParameters objects
     * @var array
     */
    private $parameters = array();

    /**
     * Doc parameter of the return value
     * @var DocParameter
     */
    private $return;

    /**
     * Association with another class or method
     * @var string
     */
    private $see;

    /**
     * Documents when the code block was added
     * @var string
     */
    private $since;

    /**
     * Flag to see if the code block is static or not
     * @var boolean
     */
    private $isStatic;

    /**
     * The subpackage of the code block
     * @var string
     */
    private $subPackage;

    /**
     * Array with todos for the code block
     * @var array
     */
    private $todos = array();

    /**
     * Type of the variable
     * @var string
     */
    private $var;

    /**
     * Version of the code block
     * @var string
     */
    private $version;

    /**
     * Set the short description of the code block
     * @param string
     * @return null
     */
    public function setDescription($description) {
        $this->description = $description;
    }

    /**
     * Get the short description of the code block
     * @return null
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set the long description of the code block
     * @param string $description
     */
    public function setLongDescription($description) {
        $this->longDescription = $description;
    }

    /**
     * Get the long description of the code block
     * @return string
     */
    public function getLongDescription() {
        return $this->longDescription;
    }

    /**
     * Sets whether the code block is abstract
     * @param boolean $isAbstract
     * @return null
     */
    public function setIsAbstract($isAbstract) {
        $this->isAbstract = $isAbstract;
    }

    /**
     * Gets whether the code block is abstract
     * @return boolean
     */
    public function isAbstract() {
        return $this->isAbstract;
    }

    /**
     * Set the access level of the code block
     * @param string $access private, protected or public
     * @return null
     * @throws zibo\ZiboException when a invalid access level is provided
     */
    public function setAccess($access) {
        if ($access != self::ACCESS_PRIVATE && $access != self::ACCESS_PROTECTED && $access != self::ACCESS_PUBLIC) {
            throw new ZiboException('Provided access level is invalid, try private, protected or public');
        }
        $this->access = $access;
    }

    /**
     * Get the access level of the code block
     * @return string
     */
    public function getAccess() {
        return $this->access;
    }

    /**
     * Set the author of the code block
     * @param string $author
     * @return null
     */
    public function setAuthor($author) {
        $this->author = $author;
    }

    /**
     * Get the author of the code block
     * @return string
     */
    public function getAuthor() {
        return $this->author;
    }

    /**
     * Set the copyrights of the code block
     * @param string $copyright
     * @return null
     */
    public function setCopyright($copyright) {
        $this->copyright = $copyright;
    }

    /**
     * Get the copyrights of the code block
     * @return string
     */
    public function getCopyright() {
        return $this->copyright;
    }

    /**
     * Sets whether the code block is deprecated
     * @param boolean $isDeprecated
     * @return null
     */
    public function setIsDeprecated($isDeprecated) {
        $this->isDeprecated = $isDeprecated;
    }

    /**
     * Gets whether the code block is deprecated
     * @return boolean
     */
    public function isDeprecated() {
        return $this->isDeprecated;
    }

    /**
     * Sets the description of the deprecation of the code block
     * @param string $deprecated
     * @return null
     */
    public function setDeprecatedMessage($deprecated) {
        $this->deprecated = $deprecated;
    }

    /**
     * Gets the description of the deprecation of the code block
     * @return string
     */
    public function getDeprecatedMessage() {
        return $this->deprecated;
    }

    /**
     * Sets a reference to an external example
     * @param string $example
     * @return null
     */
    public function setExample($example) {
        $this->example = $example;
    }

    /**
     * Gets the reference to an external example
     * @return string
     */
    public function getExample() {
        return $this->example;
    }

    /**
     * Add a exception which could be thrown by the code block
     * @param DocParameter $exception
     * @return null
     */
    public function addException(DocParameter $exception) {
        $this->exceptions[] = $exception;
    }

    /**
     * Gets the exceptions which could be thrown by the code block
     * @return array Array with DocParameter objects
     */
    public function getExceptions() {
        return $this->exceptions;
    }

    /**
     * Sets whether the code block should be ignored by the documentation
     * @param boolean $isIgnore
     * @return null
     */
    public function setIsIgnore($isIgnore) {
        $this->isIgnore = $isIgnore;
    }

    /**
     * Gets whether the code block should be ignored by the documentation
     * @return boolean
     */
    public function isIgnore() {
        return $this->isIgnore;
    }

    /**
     * Sets a link to a URL
     * @param string $link
     * @return null
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * Gets a link to a URL
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * Sets an alias for the documented variable
     * @param string $name
     * @return null
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets the alias for the documented variable
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set the package of the code block
     * @param string $package
     * @return null
     */
    public function setPackage($package) {
        $this->package = $package;
    }

    /**
     * Get the package of the code block
     * @return string
     */
    public function getPackage() {
        return $this->package;
    }

    /**
     * Add a parameter of the documented method
     * @param DocParameter $parameter definition of the parameter
     * @return null
     */
    public function addParameter(DocParameter $parameter) {
        $this->parameters[$parameter->getName()] = $parameter;
    }

    /**
     * Get the parameter definition of a specified parameter
     * @param string $name name of the parameter
     * @return DocParameter
     */
    public function getParameter($name) {
        if (isset($this->parameters[$name])) {
            return $this->parameters[$name];
        }

        return new DocParameter();
    }

    /**
     * Get all the parameters of the documented method
     * @return array Array with DocParameter objects
     */
    public function getParameters() {
        return $this->parameters;
    }

    /**
     * Sets the parameter definition of the return value
     * @param DocParameter $return
     * @return null
     */
    public function setReturn(DocParameter $return) {
        $this->return = $return;
    }

    /**
     * Gets the parameter definition of the return value
     * @return DocParameter
     */
    public function getReturn() {
        return $this->return;
    }

    /**
     * Sets a association with another class or method
     * @param string $see
     * @return null
     */
    public function setSee($see) {
        $this->see = $see;
    }

    /**
     * Gets a association with another class or method
     * @return string
     */
    public function getSee() {
        return $this->see;
    }

    /**
     * Sets when the code block was introduced
     * @param string $since
     * @return null
     */
    public function setSince($since) {
        $this->since = $since;
    }

    /**
     * Gets when the code block was introduced
     * @return string
     */
    public function getSince() {
        return $this->since;
    }

    /**
     * Sets whether the code block is static
     * @param boolean $isStatic
     * @return null
     */
    public function setIsStatic($isStatic) {
        $this->isStatic = $isStatic;
    }

    /**
     * Gets whether the code block is static
     * @return boolean
     */
    public function isStatic() {
        return $this->isStatic;
    }

    /**
     * Sets the subpackage of the code
     * @param string $subPackage
     * @return null
     */
    public function setSubPackage($subPackage) {
        $this->subPackage = $subPackage;
    }

    /**
     * Gets the subpackage of the code
     * @return string
     */
    public function getSubPackage() {
        return $this->subPackage;
    }

    /**
     * Add a todo for the code block
     * @param string $todo
     * @return null
     */
    public function addTodo($todo) {
        $this->todos[] = $todo;
    }

    /**
     * Gets the todos for the code block
     * @return array Array with strings as values
     */
    public function getTodos() {
        return $this->todos;
    }

    /**
     * Sets the data type of the documented variable
     * @param string $var
     * @return null
     */
    public function setVar($var) {
        $this->var = $var;
    }

    /**
     * Gets the data type of the documented variable
     * @return string
     */
    public function getVar() {
        return $this->var;
    }

    /**
     * Sets the version of the code block
     * @param string $version
     * @return null
     */
    public function setVersion($version) {
        $this->version = $version;
    }

    /**
     * Gets the version of the code block
     * @return string
     */
    public function getVersion() {
        return $this->version;
    }

}