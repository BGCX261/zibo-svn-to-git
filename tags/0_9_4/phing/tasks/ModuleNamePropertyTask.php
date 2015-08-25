<?php

require_once 'phing/Task.php';

class ModuleNamePropertyTask extends Task {

    private $message = ""; // required
    private $name; // required
    private $source; // required

    public function setName($name) {
        $this->name = $name;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function setMessage ($message) {
        $this->message = $message;
    }

    public function addText($msg) {
        $this->message .= $this->project->replaceProperties($msg);
    }

    public function main() {

        if ($this->name === null) {
            throw new BuildException("You must specify a value for the name attribute.");
        }

        if ($this->source === null) {
            throw new BuildException("You must specify a value for the source attribute.");
        }

        $name = $this->extractModuleName($this->source);

        $this->project->setUserProperty($this->name, $name);
    }

    private function extractModuleName($source) {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->load($source);

        $modules = $dom->getElementsByTagName('module');
        foreach ($modules as $module) {
            $namespace = $module->getAttribute('namespace');
            $name = $module->getAttribute('name');
            return $namespace . '.' . $name;
        }
    }

}