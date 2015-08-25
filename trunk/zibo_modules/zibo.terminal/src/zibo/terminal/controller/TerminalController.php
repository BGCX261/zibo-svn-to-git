<?php

namespace zibo\terminal\controller;

use zibo\admin\controller\AbstractController;

use zibo\core\view\JsonView;

use zibo\library\System;

use zibo\terminal\form\TerminalForm;
use zibo\terminal\view\TerminalView;

use zibo\ZiboException;

use \ErrorException;

/**
 * Controller for the terminal application
 */
class TerminalController extends AbstractController {

    /**
     * Name of the session variable to store the current path
     * @var string
     */
    const SESSION_PATH = 'terminal.path';

    /**
     * The current path
     * @var string
     */
    private $path;

    /**
     * The path of the running system
     * @var string
     */
    private $defaultPath;

    /**
     * Initializes the path before every action
     * @return null
     */
    public function preAction() {
        $this->defaultPath = getcwd();
        $this->path = $this->getSession()->get(self::SESSION_PATH);
    }

    /**
     * Saves the current path in the session and restores the system's path
     * @return null
     */
    public function postAction() {
        $this->getSession()->set(self::SESSION_PATH, $this->path);
    }

    /**
     * Action to show and process the terminal form
     * @return null
     */
    public function indexAction() {
        $action = $this->request->getBasePath();

        $form = new TerminalForm($action);
        if ($form->isSubmitted()) {
            $command = $form->getCommand();

            $output = '';
            $isError = false;

            try {
                if ($this->path) {
                    chdir($this->path);
                }

                $tokens = explode(' ', $command);
                if ($tokens[0] == 'cd') {
                    // handle a change directory command
                    try {
                        if (array_key_exists(1, $tokens) && $tokens[1]) {
                            chdir($tokens[1]);
                        } else {
                            chdir($this->defaultPath);
                        }

                        $this->path = getcwd();
                    } catch (ErrorException $exception) {
                        $output = 'Error: No such file or directory';
                        $isError = true;
                    }
                } else {
                    // any other command
                    $output = System::execute($command);
                    $output = htmlentities($output);
                }
            } catch (ZiboException $exception) {
                $output = 'Error: ' . $exception->getMessage();
                $isError = true;
            }

            if ($this->path) {
                chdir($this->defaultPath);
            }

            $result = array(
                'path' => $this->path ? $this->path : $this->defaultPath,
                'command' => $command,
                'output' => $output,
                'error' => $isError,
            );

            $view = new JsonView($result);
        } else {
            $view = new TerminalView($form, $this->path ? $this->path : $this->defaultPath);
        }

        $this->response->setView($view);
    }

}