<?php

namespace zibo\api\view;

use zibo\api\form\SearchForm;
use zibo\api\model\ReflectionClass;

/**
 * View for the API of a class
 */
class ClassView extends BaseView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'api/class';

    /**
     * Construct the class view
     * @param zibo\api\form\SearchForm $form the search form
     * @param array $namespaces array with the current namespaces
     * @param string $namespaceAction URL to the detail of a namespace
     * @param array $classes array with the current classes
     * @param string $classAction URL to the detail of a class
     * @param string $currentNamespace name of the current namespace
     * @param string $currentClass name of the current class
     * @param zibo\api\model\ReflectionClass $class class to show the API of
     * @return null
     */
    public function __construct(SearchForm $searchForm, array $namespaces, $namespaceAction, array $classes, $classAction, $currentNamespace, $currentClass, ReflectionClass $class) {
        parent::__construct(self::TEMPLATE, $searchForm, $namespaces, $namespaceAction, $classes, $classAction, $currentNamespace, $currentClass);

        $type = $class->getTypeString();
        $namespace = $class->getNamespaceName();
        $name = $class->getName();
        $shortName = $class->getShortName();
        $inheritance = $class->getInheritance();
        $interfaces = $class->getInterfaceNames();
        $properties = $class->getProperties();
        $constants = $class->getConstants();

        $this->set('class', $class);
        $this->set('type', $type);
        $this->set('namespace', $namespace);
        $this->set('shortName', $shortName);
        $this->set('name', $name);

        $this->set('inheritance', $inheritance);
        $this->set('interfaces', $interfaces);
        $this->set('properties', $properties);
        $this->set('constants', $constants);
    }

}