<?php

namespace zibo\api\view;

use zibo\admin\view\BaseView as AdminBaseView;

use zibo\api\form\SearchForm;

/**
 * Base view for the API views
 */
class BaseView extends AdminBaseView {

    /**
     * Path to the style of this view
     * @var string
     */
    const STYLE_API = 'web/styles/api/api.css';

    /**
     * Construct the base API view
     * @param string $template template for the engine
     * @param zibo\api\form\SearchForm $form the search form
     * @param array $namespaces array with the current namespaces
     * @param string $namespaceAction URL to the detail of a namespace
     * @param array $classes array with the current classes
     * @param string $classAction URL to the detail of a class
     * @param string $currentNamespace name of the current namespace
     * @param string $currentClass name of the current class
     * @return null
     */
    public function __construct($template, SearchForm $searchForm, array $namespaces, $namespaceAction, array $classes = null, $classAction = null, $currentNamespace = null, $currentClass = null) {
        parent::__construct($template);

        $sidebarView = new SidebarView($searchForm, $namespaces, $namespaceAction, $classes, $classAction, $currentNamespace, $currentClass);

        $this->sidebar->addPanel($sidebarView);

        $this->set('namespaces', $namespaces);
        $this->set('namespaceAction', $namespaceAction);
        $this->set('currentNamespace', $currentNamespace);

        $this->set('classes', $classes);
        $this->set('classAction', $classAction);
        $this->set('currentClass', $currentClass);

        $this->addStyle(self::STYLE_API);
    }

}