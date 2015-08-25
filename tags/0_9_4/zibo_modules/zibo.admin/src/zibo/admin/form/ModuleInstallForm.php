<?php

namespace zibo\admin\form;

use zibo\library\filesystem\File;
use zibo\library\html\form\field\FieldFactory;
use zibo\library\html\form\Form;
use zibo\library\validation\validator\RequiredValidator;

/**
 * Form to install a module
 */
class ModuleInstallForm extends Form {

    /**
     * Name of this form
     * @var string
     */
    const NAME = 'formInstallModule';

    /**
     * Name of the module field
     * @var string
     */
    const FIELD_MODULE = 'module';

    /**
     * Name of the install button
     * @var string
     */
    const FIELD_INSTALL = 'install';

    /**
     * Translation of the install button
     * @var string
     */
    const TRANSLATION_INSTALL = 'modules.button.install';

    /**
     * Constructs a new module installation form
     * @param string $action URL where this form will point to
     * @return null
     */
    public function __construct($action) {
        parent::__construct($action, self::NAME);

        $factory = FieldFactory::getInstance();

        $this->addField($factory->createField(FieldFactory::TYPE_FILE, self::FIELD_MODULE));
        $this->addField($factory->createSubmitField(self::FIELD_INSTALL, self::TRANSLATION_INSTALL));

        $this->addValidator(self::FIELD_MODULE, new RequiredValidator());
    }

    /**
     * Gets the file of the uploaded module
     * @return zibo\library\filesystem\File
     */
    public function getModuleFile() {
        $path = $this->getValue(self::FIELD_MODULE);
        return new File($path);
    }

}