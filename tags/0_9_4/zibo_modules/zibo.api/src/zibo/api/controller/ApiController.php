<?php

namespace zibo\api\controller;

use zibo\admin\controller\AbstractController;

use zibo\api\form\SearchForm;
use zibo\api\model\ApiBrowser;
use zibo\api\view\ClassView;
use zibo\api\view\NamespaceView;
use zibo\api\view\SearchView;
use zibo\api\Module;

use zibo\library\html\Breadcrumbs;
use zibo\library\smarty\view\SmartyView;

use \Exception;

/**
 * Controller to browse through the API of this Zibo installation
 */
class ApiController extends AbstractController {

    /**
     * Translation key for the class not found message
     * @var string
     */
    const TRANSLATION_CLASS_NOT_FOUND = 'api.error.class.not.found';

    /**
     * Translation key for the breadcrumbs prefix
     * @var string
     */
    const TRANSLATION_NAVIGATION = 'api.label.navigation.label';

    /**
     * Translation key of the home breadcrumb
     * @var string
     */
    const TRANSLATION_NAVIGATION_HOME = 'api.label.navigation.home';

    /**
     * The API browser
     * @var zibo\api\model\ApiBrowser
     */
    private $apiBrowser;

    /**
     * The base URL to a namespace view
     * @var string
     */
    private $namespaceAction;

    /**
     * The base URL to a class view
     * @var string
     */
    private $classAction;

    /**
     * The URL to a search action
     * @var string
     */
    private $searchAction;

    /**
     * Create a API browser and initialize the URLs to the actions
     * @return null
     */
    public function preAction() {
        $this->apiBrowser = new ApiBrowser();

        $basePath = $this->request->getBasePath();
        $this->namespaceAction = $basePath . '/namespace/';
        $this->classAction = $basePath . '/class/';
        $this->searchAction = $basePath . '/search';
    }

    /**
     * Action to show the main API browser view
     * @return null
     */
    public function indexAction() {
        $form = new SearchForm($this->searchAction);

        $namespaces = $this->apiBrowser->getNamespaces();

        $view = new NamespaceView($form, $namespaces, $this->namespaceAction);
        $view->setPageTitle(Module::TRANSLATION_API, true);
        $this->setBreadcrumbsToView($view);

        $this->response->setView($view);
    }

    /**
     * Action to show the detail of a namespace
     * @return null
     */
    public function namespaceAction() {
        $form = new SearchForm($this->searchAction);

        $namespace = implode(ApiBrowser::NAMESPACE_SEPARATOR, func_get_args());

        $namespaces = $this->apiBrowser->getNamespaces($namespace);
        $classes = $this->apiBrowser->getClassesForNamespace($namespace);

        $view = new NamespaceView($form, $namespaces, $this->namespaceAction, $classes, $this->classAction, $namespace);
        $view->setPageTitle(Module::TRANSLATION_API, true);
        $this->setBreadcrumbsToView($view, $namespace);

        $this->response->setView($view);
    }

    /**
     * Action to show the API of a class
     * @return null
     */
    public function classAction() {
        $args = func_get_args();
        $class = array_pop($args);
        $namespace = implode(ApiBrowser::NAMESPACE_SEPARATOR, $args);

        try {
            $form = new SearchForm($this->searchAction);

            $namespaces = $this->apiBrowser->getNamespaces($namespace);
            $classes = $this->apiBrowser->getClassesForNamespace($namespace);

            $classDefinition = $this->apiBrowser->getClass($namespace, $class);

            $view = new ClassView($form, $namespaces, $this->namespaceAction, $classes, $this->classAction, $namespace, $class, $classDefinition);
            $view->setPageTitle(Module::TRANSLATION_API, true);
            $this->setBreadcrumbsToView($view, $namespace, $class);

            $this->response->setView($view);
        } catch (Exception $e) {
            $namespace = implode('\\', $args);
            $this->addError(self::TRANSLATION_CLASS_NOT_FOUND, array('class' => $namespace . '\\' . $class));
            $this->response->setRedirect($this->request->getBasePath());
        }
    }

    /**
     * Action to perform a class search
     * @param string $searchQuery url encoded search query
     * @return null
     */
    public function searchAction($searchQuery = null) {
        $searchQuery = urldecode($searchQuery);

        $form = new SearchForm($this->searchAction, $searchQuery);

        if ($form->isSubmitted()) {
            $searchQuery = $form->getQuery();
            $this->response->setRedirect($this->searchAction . '/' . urlencode($searchQuery));
            return;
        } elseif (!$searchQuery) {
            $this->response->setRedirect($this->request->getBasePath());
            return;
        }

        $searchResult = $this->apiBrowser->getClassesForNamespace(null, true, $searchQuery);
        if (count($searchResult) == 1) {
            $searchResult = each($searchResult);
            $this->response->setRedirect($this->classAction . $searchResult['key']);
            return;
        }

        $namespaces = $this->apiBrowser->getNamespaces();

        $view = new SearchView($form, $namespaces, $this->namespaceAction, $this->classAction, $searchQuery, $searchResult);
        $view->setPageTitle(Module::TRANSLATION_API, true);
        $this->setBreadcrumbsToView($view);

        $this->response->setView($view);
    }

    /**
     * Set the API breadcrumbs to the view
     * @param zibo\library\smarty\view\SmartyView $view
     * @param string $namespace current namespace
     * @param string $class current class
     * @return null
     */
    private function setBreadcrumbsToView(SmartyView $view, $namespace = null, $class = null) {
        $translator = $this->getTranslator();

        $breadcrumbs = new Breadcrumbs();
        $breadcrumbs->setId('breadcrumbs');
        $breadcrumbs->setLabel($translator->translate(self::TRANSLATION_NAVIGATION));
        $breadcrumbs->addBreadcrumb($this->request->getBasePath(), $translator->translate(self::TRANSLATION_NAVIGATION_HOME));

        $view->set('breadcrumbs', $breadcrumbs);

        if (empty($namespace)) {
            return;
        }

        $tokens = explode(ApiBrowser::NAMESPACE_SEPARATOR, $namespace);

        $namespace = null;
        foreach ($tokens as $token) {
            $namespace = $namespace . $token . ApiBrowser::NAMESPACE_SEPARATOR;
            $breadcrumbs->addBreadcrumb($this->namespaceAction . $namespace, $token);
        }

        if ($class) {
            $breadcrumbs->addBreadcrumb($this->classAction . $namespace . $class, $class);
        }
    }

}