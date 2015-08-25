<?php

namespace zibo\library\smarty\view;

use zibo\core\view\HtmlView;
use zibo\core\View;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\smarty\resource\ResourceHandler;
use zibo\library\Structure;

use zibo\ZiboException;

use \Smarty;

/**
 * View implementation for a Smarty rendered page
 */
class SmartyView extends HtmlView {

    /**
     * Configuration key for the compile directory of Smarty
     * @var string
     */
    const CONFIG_COMPILE_DIRECTORY = 'smarty.compile.directory';

    /**
     * Configuration key for the plugins of Smarty
     * @var string
     */
    const CONFIG_PLUGINS = 'smarty.plugin';

    /**
     * Default compile directory of Smarty
     * @var unknown_type
     */
    const DEFAULT_COMPILE_DIRECTORY = 'application/data/cache/smarty/compile';

    /**
     * Smarty engine
     * @var Smarty
     */
    protected $smarty;

    /**
     * Array with subviews for this view
     * @var array
     */
    protected $subviews;

    /**
     * Path to the template for this view
     * @var string
     */
    protected $template;

    /**
     * Resource handler for the Smarty engine
     * @var zibo\library\smarty\ResourceHandler
     */
    protected $resourceHandler;

    /**
     * Default resource handler for the Smarty engine
     * @var zibo\library\smarty\ResourceHandler
     */
    static protected $defaultResourceHandler;

    /**
     * Construct a smarty view
     * @param string $template relative path of the template file without extension
     * @return null
     */
    public function __construct($template) {
        $this->smarty = new Smarty();
        $this->subviews = array();
        $this->template = $template;

        $this->initSmarty();
    }

    /**
     * Initialize the smarty engine
     * @return null
     */
    private function initSmarty() {
        $zibo = Zibo::getInstance();

        $this->parseConfiguration($zibo);
        $this->setResourceHandler();

        $request = $zibo->getRequest();
        if ($request === null) {
            return;
        }

        $this->set('_baseUrl', $request->getBaseUrl());
        $this->set('_basePath', $request->getBasePath());
        $this->set('_route', $request->getRoute());
    }

    /**
     * Assign the smarty configuration to the engine
     * @param zibo\core\Zibo $zibo instance of Zibo to get the configuration from
     * @return null
     */
    private function parseConfiguration(Zibo $zibo) {
        $this->smarty->caching = false;
        $this->smarty->compile_dir = $zibo->getConfigValue(self::CONFIG_COMPILE_DIRECTORY, self::DEFAULT_COMPILE_DIRECTORY);
        $directory = new File($this->smarty->compile_dir);
        $directory->create();

        $smartyPluginDirectories = $zibo->getConfigValue(self::CONFIG_PLUGINS, array());
        if (!is_array($smartyPluginDirectories)) {
            $smartyPluginDirectories = array($smartyPluginDirectories);
        }
        foreach ($smartyPluginDirectories as $directory) {
            $this->smarty->plugins_dir[] = $directory;
        }
    }

    /**
     * Override the resource handler of this view
     * @param zibo\library\smarty\resource\ResourceHandler $resourceHandler instance of a resource handler or null for the default one
     * @return null
     */
    public function setResourceHandler(ResourceHandler $resourceHandler = null) {
        if ($resourceHandler === null) {
            if (!self::$defaultResourceHandler) {
                self::$defaultResourceHandler = new ResourceHandler();
            }
            $resourceHandler = self::$defaultResourceHandler;
        }

        $this->resourceHandler = $resourceHandler;

        $this->smarty->default_resource_type = 'zibo';
        $this->smarty->register_resource(
            'zibo',
            array(
                $resourceHandler,
                'getSource',
                'getTimestamp',
                'isSecure',
                'isTrusted',
            )
        );
    }

    /**
     * Get the smarty instance of this view
     * @return Smarty
     */
    public function getEngine() {
        return $this->smarty;
    }

    /**
     * Set a subview to this view
     * @param string $name name of the view
     * @param zibo\core\View $view the subview
     * @return null
     */
    public function setSubview($name, View $view) {
        $this->subviews[$name] = $view;
    }

    /**
     * Get a subview from this view
     * @param string $name name of the view
     * @return zibo\core\View the subview with the provided name
     *
     * @throws zibo\ZiboException when no subview set for the provided name
     */
    public function getSubview($name) {
        if (!array_key_exists($name, $this->subviews)) {
            throw new ZiboException('No subview set for ' . $name);
        }

        return $this->subviews[$name];
    }

    /**
     * Get all the subviews from this view
     * @return array Array with View instances
     */
    public function getSubviews() {
        return $this->subviews;
    }

    /**
     * Assign a value to this view
     * @param string $key name of the value
     * @param mixed $value the value to set to this view
     * @return null
     */
    public function set($key, $value = null) {
        if (is_array($key)) {
            $this->smarty->assign($key);
        } else {
            $this->smarty->assign($key, $value);
        }
    }

    /**
     * Get a previously assigned value from this view
     * @param string $key name of the value
     * @param mixed $default the default value for when the key is not set
     * @return mixed the value for the provided key, or the provided default value when the key is not set
     */
    public function get($key, $default = null) {
        $vars = $this->smarty->get_template_vars();
        $result = $default;

        if (isset($vars[$key])) {
            $result = $vars[$key];
        }

        return $result;
    }

    /**
     * Render the view
     * @param boolean $return true to return the rendered view, false to send it to the client
     * @return mixed null when provided $return is set to true, the rendered output when the provided $return is set to false
     */
    public function render($return = true) {
        $this->preRenderSubviews();

        $views = array();
        foreach ($this->subviews as $name => $view) {
            $viewContainer = array(
                'view' => $view,
                'html' => $this->renderView($view),
            );
            $views[$name] = $viewContainer;
        }

        $this->preRender();

        $this->set('_meta', $this->meta);
        $this->set('_inlineScripts', $this->inlineScripts);
        $this->set('_scripts', $this->scripts);
        $this->set('_styles', $this->styles);
        $this->set('_custom', $this->custom);
        $this->set('_views', $views);

        $output = $this->smarty->fetch($this->template);

        $output = $this->postRender($output);

        if ($return) {
            return $output;
        }

        echo $output;

        return;
    }

    /**
     * Render a subview for this view
     *
     * All the styles and scripts of the subview will be added to the main view
     *
     * @param zibo\core\View $view the view to render
     * @return string The output of the view
     */
    protected function renderView(View $view) {
        $renderedView = $view->render(true);

        if (!($view instanceof HtmlView)) {
            return $renderedView;
        }

        $this->meta = Structure::merge($this->meta, $view->getMeta());
        $this->styles = Structure::merge($this->styles, $view->getStyles());
        $this->scripts = Structure::merge($this->scripts, $view->getJavascripts());
        $this->inlineScripts = Structure::merge($this->inlineScripts, $view->getInlineJavascripts());

        return $renderedView;
    }

    /**
     * Hook to process this view before any rendering has started
     * @return null
     */
    protected function preRenderSubviews() {

    }

    /**
     * Hook to process this view after the subviews are rendered and before the main view is rendered
     * @return null
     */
    protected function preRender() {

    }

    /**
     * Hook to process the output of this view after it has been rendered
     * @param string $rendered this view rendered
     * @return string this view rendered and processed
     */
    protected function postRender($rendered) {
        return $rendered;
    }

}