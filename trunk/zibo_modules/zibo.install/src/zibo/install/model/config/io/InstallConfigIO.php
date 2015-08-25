<?php

namespace zibo\install\model\config\io;

use zibo\install\Module;

use zibo\library\config\io\ini\IniConfigIO;

class InstallConfigIO extends IniConfigIO {

    /**
     * Read the configuration values for a section
     * @param string $section name of the section to read
     * @return array Hierarchic array with each configuration token as a key
     * @throws zibo\library\config\exception\ConfigException when the section name is invalid or empty
     */
    public function read($section) {
        $values = parent::read($section);

        switch ($section) {
            case 'smarty':
                if (!array_key_exists('compile', $values)) {
                    $values['compile'] = array();
                }

                $values['compile']['directory'] = Module::getTempDirectory('smarty')->getPath();

                break;
            case 'system':
                if (!array_key_exists('session', $values)) {
                    $values['session'] = array();
                }

                $values['session']['path'] = Module::getTempDirectory('session')->getPath();

                break;
        }

        return $values;
    }

}