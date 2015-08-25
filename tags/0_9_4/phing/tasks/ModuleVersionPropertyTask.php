<?php

require_once 'phing/Task.php';

class ModuleVersionPropertyTask extends Task {

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

        $version = $this->extractModuleVersion($this->source);

        $this->project->setUserProperty($this->name, $version);
    }

    private function extractModuleVersion($source) {
        $dom = new DOMDocument('1.0', 'utf-8');
        $dom->load($source);

        $modules = $dom->getElementsByTagName('module');
        foreach ($modules as $module) {
            $version = $module->getAttribute('version');
            return $version;
        }
    }
}
