<?php

namespace zibo\admin\controller;

use zibo\admin\message\Message;
use zibo\admin\view\DownloadView;
use zibo\admin\view\Error404View;
use zibo\admin\Module;

use zibo\core\controller\AbstractController as CoreAbstractController;
use zibo\core\Dispatcher;
use zibo\core\Request;
use zibo\core\Response;
use zibo\core\View;
use zibo\core\Zibo;

use zibo\library\filesystem\File;
use zibo\library\i18n\I18n;
use zibo\library\security\SecurityManager;
use zibo\library\Session;

use zibo\ZiboException;

/**
 * Abstract implementation of a controller
 */
class AbstractController extends CoreAbstractController {

    /**
     * Translation key for a generic error message
     * @var string
     */
    const TRANSLATION_ERROR = 'error';

    /**
     * The file to clean up after download
     * @var zibo\library\filesystem\File
     */
    protected $downloadFile;

    /**
     * Add a localized information message to the response
     * @param string $translationKey translation key of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    public function addInformation($translationKey, array $vars = null) {
        $this->addMessage($translationKey, Message::TYPE_INFORMATION, $vars);
    }

    /**
     * Add a localized error message to the response
     * @param string $translationKey translation key of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    public function addError($translationKey, array $vars = null) {
        $this->addMessage($translationKey, Message::TYPE_ERROR, $vars);
    }

    /**
     * Add a localized warning message to the response
     * @param string $translationKey translation key of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    public function addWarning($translationKey, array $vars = null) {
        $this->addMessage($translationKey, Message::TYPE_WARNING, $vars);
    }

    /**
     * Add a localized message to the response
     * @param string $translationKey translation key of the message
     * @param string $type type of the message
     * @param array $vars array with variables for the translator
     * @return null
     */
    private function addMessage($translationKey, $type, $vars) {
        $message = new Message($translationKey, $type, $vars);
        $this->response->addMessage($message);
    }

    /**
     * Parses an array of values into a key value array. Usefull to parse the arguments of an action
     * eg. array('key1', 'value1', 'key2', 'value2') will return array('key1' => 'value1', 'key2' => 'value2')
     * @param array $arguments Arguments array
     * @return array Parsed arguments array
     * @throws zibo\ZiboException when the number of elements in the argument array is not even
     */
    protected function parseArguments(array $arguments) {
        if (count($arguments) % 2 != 0) {
            throw new ZiboException('Provided arguments array should have an even number of arguments');
        }

        $parsedArguments = array();

        $argumentName = null;
        foreach ($arguments as $argument) {
            if ($argumentName === null) {
                $argumentName = $argument;
            } else {
                $parsedArguments[$argumentName] = urldecode($argument);
                $argumentName = null;
            }
        }

        return $parsedArguments;
    }

    /**
     * Get a new Request for request chaining
     * @param string $controllerClass name of the controller for the new request
     * @param string $action action in the controller, if not specified the * action will be used for auto lookup
     * @param boolean|int|array $parameters provide an array as parameters for the new request. if a boolean is provided, the parameters will be taken from the request. if the boolean is set to true, the first parameter will be taken of the parameter array and added to the base path. You can also provide the number of parameters to be taken of the parameter array and added to the base path.
     * @param string $basePath the basePath for your new request. if none specified, the base path will be taken from the current request
     * @return zibo\core\Request
     */
    protected function forward($controllerClass, $action = null, $parameters = true, $basePath = null) {
        $baseUrl = $this->request->getBaseUrl();
        if (!$basePath) {
            $basePath = $this->request->getBasePath();
        }

        if (!is_array($parameters)) {
            $requestParameters = $this->request->getParameters();

            if (is_bool($parameters) && $parameters) {
                $parameters = 1;
            }

            if (is_numeric($parameters) && $parameters > 0) {
                for ($i = 0; $i < $parameters; $i++) {
                    $basePathSuffix = array_shift($requestParameters);
                    $basePath .= Request::QUERY_SEPARATOR . $basePathSuffix;
                }
            }

            $parameters = $requestParameters;
        }

        if ($action === null) {
            $action = Dispatcher::ACTION_ASTERIX;
        }

        return new Request($baseUrl, $basePath, $controllerClass, $action, $parameters);
    }

    /**
     * Gets the environment of Zibo
     * @return zibo\core\environment\Environment
     */
    public function getEnvironment() {
        return Zibo::getInstance()->getEnvironment();
    }

    /**
     * Easy access to the translator of a locale
     * @param string|zibo\library\i18n\locale\Locale $locale the locale or locale code, if not specified the current locale is assumed
     * @return zibo\library\i18n\translation\Translator
     */
    public function getTranslator($locale = null) {
        return I18n::getInstance()->getTranslator($locale);
    }

    /**
     * Easy access to the session
     * @return zibo\library\Session
     */
    public function getSession() {
        return Session::getInstance();
    }

    /**
     * Gets the current user
     * @return zibo\library\security\model\User|null
     */
    public function getUser() {
        return SecurityManager::getInstance()->getUser();
    }

    /**
     * Checks if a permission is allowed by the current user
     * @param string $permission Code of the permission
     * @return boolean True if the permission is allowed, false otherwise
     */
    public function isPermissionAllowed($permission) {
        return SecurityManager::getInstance()->isPermissionAllowed($permission);
    }

    /**
     * Gets the referer of the last page displayed
     * @param string $default Default referer to return when there is no referer set
     * @return string URL to the last page displayed
     */
    public  function getReferer($default = null) {
        $referer = Session::getInstance()->get(Module::SESSION_REFERER, $default);

        if ($referer) {
            return $referer;
        }

        return $this->request->getBaseUrl();
    }

    /**
     * Set an Error404View to the response
     * @param zibo\core\View $view View for the 404 response
     * @return null
     */
    protected function setError404(View $view = null) {
        if (!$view) {
            $view = new Error404View();
        }

        $this->response->setStatusCode(Response::STATUS_CODE_NOT_FOUND);
        $this->response->setView($view);
    }

    /**
     * Sets the download view of the provided file to the response and registers an event to clean up the file.
     * @param zibo\library\filesystem\File $file File which needs to be offered for download
     * @param string $name Name for the download
     * @return null
     */
    protected function setDownloadView(File $file, $name = null) {
        $this->downloadFile = $file;

        $view = new DownloadView($file, $name);

        $this->response->setView($view);

        Zibo::getInstance()->registerEventListener(Zibo::EVENT_POST_RESPONSE, array($this, 'cleanUpDownload'));
    }

    /**
     * Cleans up the download file
     * @return null
     */
    public function cleanUpDownload() {
        if ($this->downloadFile) {
            $this->downloadFile->delete();
        }
    }

}