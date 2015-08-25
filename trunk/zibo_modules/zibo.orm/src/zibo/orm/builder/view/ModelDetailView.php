<?php

namespace zibo\orm\builder\view;

use zibo\admin\view\BaseView;

use zibo\library\orm\model\Model;

use zibo\jquery\Module as JQueryModule;

use zibo\orm\builder\form\ModelFieldOrderForm;
use zibo\orm\builder\table\ModelFieldTable;
use zibo\orm\builder\table\SimpleDataFormatTable;
use zibo\orm\builder\table\SimpleModelIndexTable;

class ModelDetailView extends BaseView {

    const TEMPLATE = 'orm/builder/model.detail';

    const SCRIPT_BUILDER = 'web/scripts/orm/builder.js';

    const STYLE_BUILDER = 'web/styles/orm/builder.css';

    /**
     * Constructs a model detail view
     * @param zibo\library\orm\model\Model $model
     * @return null
     */
    public function __construct(Model $model, ModelFieldTable $fieldTable, ModelFieldOrderForm $orderForm = null, $editModelAction = null, $addFieldsAction = null, $editFormatAction = null, $editIndexAction = null) {
        parent::__construct(self::TEMPLATE);

        $meta = $model->getMeta();
        $modelTable = $meta->getModelTable();
        $modelClass = get_class($model);
        $dataClass = $meta->getDataClassName();

        $indexTable = new SimpleModelIndexTable($modelTable);
        $formatTable = new SimpleDataFormatTable($modelTable);

        $this->set('modelTable', $modelTable);
        $this->set('modelClass', $modelClass);
        $this->set('dataClass', $dataClass);
        $this->set('fieldTable', $fieldTable);
        $this->set('formatTable', $formatTable);
        $this->set('indexTable', $indexTable);
        $this->set('orderForm', $orderForm);

        $this->set('editModelAction', $editModelAction);
        $this->set('addFieldsAction', $addFieldsAction);
        $this->set('editFormatAction', $editFormatAction);
        $this->set('editIndexAction', $editIndexAction);

        $this->addJavascript(JQueryModule::SCRIPT_JQUERY_UI);
        $this->addJavascript(self::SCRIPT_TABLE);
        $this->addJavascript(self::SCRIPT_BUILDER);
        $this->addInlineJavascript('ziboOrmInitializeModelDetail();');

        $this->addStyle(self::STYLE_BUILDER);
    }

}