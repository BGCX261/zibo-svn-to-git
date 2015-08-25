<?php

namespace zibo\library\html\form\field;

use zibo\core\Zibo;

use zibo\library\i18n\I18n;
use zibo\library\String;

use zibo\ZiboException;

use \ReflectionClass;
use \ReflectionException;

/**
 * Factory to create form fields dynamically
 */
class FieldFactory {

    /**
     * Default class name for a boolean field
     * @var string
     */
    const CLASS_BOOLEAN = 'zibo\\library\\html\\form\\field\\BooleanField';

    /**
     * Default class name for a date field
     * @var string
     */
    const CLASS_DATE = 'zibo\\library\\html\\form\\field\\DateField';

    /**
     * Default class name for a email field
     * @var string
     */
    const CLASS_EMAIL = 'zibo\\library\\html\\form\\field\\EmailField';

    /**
     * Default class name for a file upload field
     * @var string
     */
    const CLASS_FILE = 'zibo\\library\\html\\form\\field\\FileField';

    /**
     * Default class name for a hidden field
     * @var string
     */
    const CLASS_HIDDEN = 'zibo\\library\\html\\form\\field\\HiddenField';

    /**
     * Default class name for a list field
     * @var string
     */
    const CLASS_LIST = 'zibo\\library\\html\\form\\field\\ListField';

    /**
     * Default class name for a locale field
     * @var string
     */
    const CLASS_LOCALE = 'zibo\\library\\html\\form\\field\\LocaleField';

    /**
     * Default class name for a option field
     * @var string
     */
    const CLASS_OPTION = 'zibo\\library\\html\\form\\field\\OptionField';

    /**
     * Default class name for a password field
     * @var string
     */
    const CLASS_PASSWORD = 'zibo\\library\\html\\form\\field\\PasswordField';

    /**
     * Default class name for a string field
     * @var string
     */
    const CLASS_STRING = 'zibo\\library\\html\\form\\field\\StringField';

    /**
     * Default class name for a submit button
     * @var string
     */
    const CLASS_SUBMIT = 'zibo\\library\\html\\form\\field\\SubmitField';

    /**
     * Default class name for a text field
     * @var string
     */
    const CLASS_TEXT = 'zibo\\library\\html\\form\\field\\TextField';

    /**
     * Default class name for a website field
     * @var string
     */
    const CLASS_WEBSITE = 'zibo\\library\\html\\form\\field\\WebsiteField';

    /**
     * Configuration key for to register fields through the configuration
     * @var string
     */
    const CONFIG_FIELD_TYPES = 'form.field';

    /**
     * Interface of a field class
     * @var string
     */
    const INTERFACE_FIELD = 'zibo\\library\\html\\form\\field\\Field';

    /**
     * Type name of the boolean field
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * Type name of the date field
     * @var string
     */
    const TYPE_DATE = 'date';

    /**
     * Type name of the email field
     * @var string
     */
    const TYPE_EMAIL = 'email';

    /**
     * Type name of the file upload field
     * @var string
     */
    const TYPE_FILE = 'file';

    /**
     * Type name of the hidden field
     * @var string
     */
    const TYPE_HIDDEN = 'hidden';

    /**
     * Type name of the image field
     * @var string
     */
    const TYPE_IMAGE = 'image';

    /**
     * Type name of the list field
     * @var string
     */
    const TYPE_LIST = 'list';

    /**
     * Type name of the locale field
     * @var string
     */
    const TYPE_LOCALE = 'locale';

    /**
     * Type name of the option field
     * @var string
     */
    const TYPE_OPTION = 'option';

    /**
     * Type name of the password field
     * @var string
     */
    const TYPE_PASSWORD = 'password';

    /**
     * Type name of the string field
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * Type name of the submit button
     * @var string
     */
    const TYPE_SUBMIT = 'submit';

    /**
     * Type name of the text field
     * @var string
     */
    const TYPE_TEXT = 'text';

    /**
     * Type name of the website field
     * @var string
     */
    const TYPE_WEBSITE = 'website';

    /**
     * Type name of the wysiwyg field
     * @var string
     */
    const TYPE_WYSIWYG = 'wysiwyg';

    /**
     * Instance of the factory
     * @var FieldFactory
     */
    private static $instance;

    /**
     * Array with the field type as key and the field class name as value
     * @var array
     */
    private $fields;

    /**
     * Translator instance
     * @var zibo\library\i18n\translation\Translator
     */
    private $translator;

    /**
     * Construct a new factory, register the default fields and the fields defined in the configuration.
     * @return null
     */
    private function __construct() {
        $this->fields = array(
            self::TYPE_BOOLEAN => self::CLASS_BOOLEAN,
            self::TYPE_DATE => self::CLASS_DATE,
            self::TYPE_EMAIL => self::CLASS_EMAIL,
            self::TYPE_FILE => self::CLASS_FILE,
            self::TYPE_HIDDEN => self::CLASS_HIDDEN,
            self::TYPE_IMAGE => self::CLASS_FILE,
            self::TYPE_LIST => self::CLASS_LIST,
            self::TYPE_LOCALE => self::CLASS_LOCALE,
            self::TYPE_OPTION => self::CLASS_OPTION,
            self::TYPE_PASSWORD => self::CLASS_PASSWORD,
            self::TYPE_STRING => self::CLASS_STRING,
            self::TYPE_SUBMIT => self::CLASS_SUBMIT,
            self::TYPE_TEXT => self::CLASS_TEXT,
            self::TYPE_WEBSITE => self::CLASS_WEBSITE,
            self::TYPE_WYSIWYG => self::CLASS_TEXT,
        );

        $types = Zibo::getInstance()->getConfigValue(self::CONFIG_FIELD_TYPES, array());
        foreach ($types as $type => $className) {
            $this->registerField($type, $className);
        }
    }

    /**
     * Get the instance of the factory
     * @return FieldFactory
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a new field
     * @param string $type name of the field
     * @param string $className class name of the field
     * @return null
     */
    public function registerField($type, $className) {
        if (String::isEmpty($type)) {
            throw new ZiboException('Provided type is empty');
        }
        if (String::isEmpty($className)) {
            throw new ZiboException('Provided class is empty');
        }

        try {
            $reflection = new ReflectionClass($className);
        } catch (ReflectionException $e) {
            throw new ZiboException('Provided class does not exist');
        }
        if (!$reflection->implementsInterface(self::INTERFACE_FIELD)) {
            throw new ZiboException('Provided class does not implement the interface ' . self::INTERFACE_FIELD);
        }

        $this->fields[$type] = $className;
    }

    /**
     * Create a new field
     * @param string $type type of the field
     * @param string $name name for the new field
     * @param mixed $defaultValue default value for the new field
     * @param boolean $isDisabled disabled flag for the new field
     * @return Field new instance of the requested field
     * @throws zibo\ZiboException when the type is not available in this factory
     */
    public function createField($type, $name, $defaultValue = null, $isDisabled = false) {
        if (!isset($this->fields[$type])) {
            throw new ZiboException('Unsupported field: ' . $type);
        }

        $className = $this->fields[$type];
        return new $className($name, $defaultValue, $isDisabled);
    }

    /**
     * Create a new submit field
     * @param string $name name for the submit field
     * @param string $translationKey translation key for the value of the submit field
     * @param boolean $isDisabled disabled flag for the submit field
     * @param Field new instance of a submit field
     */
    public function createSubmitField($name, $translationKey, $isDisabled = false) {
        $translator = $this->getTranslator();
        return $this->createField(self::TYPE_SUBMIT, $name, $translator->translate($translationKey), $isDisabled);
    }

    /**
     * Get the translator
     * @return zibo\library\i18n\translation\Translator
     */
    private function getTranslator() {
        if ($this->translator == null) {
            $this->translator = I18n::getInstance()->getTranslator();
        }
        return $this->translator;
    }

}