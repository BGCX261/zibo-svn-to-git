<?php

namespace zibo\orm\scaffold\view;

use zibo\admin\controller\LocalizeController;

use zibo\library\i18n\I18n;

use zibo\library\orm\model\meta\ModelMeta;
use zibo\library\orm\ModelManager;
use zibo\library\smarty\view\SmartyView;

/**
 * View to display the localized data in other locales
 */
class LocalizeView extends SmartyView {

    /**
     * Path to the template of this view
     * @var string
     */
    const TEMPLATE = 'orm/scaffold/localize';

    /**
     * Constructs a new localize view
     * @param $meta
     * @param $id
     * @param $action
     * @param $field
     * @return null
     */
    public function __construct(ModelMeta $meta, $data, $action = null) {
        parent::__construct(self::TEMPLATE);

        $localizedModel = $meta->getLocalizedModel();
        $localizedFields = $meta->getLocalizedFields();

        $currentLocale = LocalizeController::getLocale();
        $allLocales = I18n::getInstance()->getAllLocales();
        unset($allLocales[$currentLocale]);

        $locales = array();
        foreach ($allLocales as $locale) {
            $locale = $locale->getCode();

            $localizedId = $localizedModel->getLocalizedId($data->id, $locale);
            if ($localizedId === null) {
                $locales[$locale] = null;
                continue;
            }

            $localesData = clone($data);
            $localizedData = $localizedModel->findById($localizedId);
            foreach ($localizedFields as $fieldName => $localizedField) {
                $localesData->$fieldName = $localizedData->$fieldName;
            }

            $localesData->localizedLabel = $meta->formatData($localesData);

            $locales[$locale] = $localesData;
        }

        $this->set('locales', $locales);
        $this->set('action', $action);
    }

}