<?php

namespace zibo\orm\builder\view;

use zibo\admin\view\BaseView;

use zibo\orm\builder\table\ModelTable;

class BuilderView extends BaseView {

    const STYLE_BUILDER = 'web/styles/orm/builder.css';

    public function __construct(ModelTable $table) {
        parent::__construct('orm/builder/index');
        $this->set('table', $table);

        $this->addJavascript(self::SCRIPT_TABLE);

        $this->addStyle(self::STYLE_BUILDER);
    }

}