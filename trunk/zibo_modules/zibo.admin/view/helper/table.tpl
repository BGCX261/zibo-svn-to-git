{php}
    $table = $this->get_template_vars('table');
    
    if ($table instanceof zibo\library\html\table\HierarchicTable) {
        $template = 'helper/hierarchic.table';
    } elseif ($table instanceof zibo\library\html\table\ExtendedTable) {
        $template = 'helper/extended.table';
    } elseif ($table instanceof zibo\library\html\table\SimpleTable) {
        $template = 'helper/simple.table';
    } else {
        $this->trigger_error('Invalid table provided');
    }
    
    $exportExtensions = null;
    if ($table instanceof zibo\library\html\table\ExportableTable) {
        $exportExtensions = zibo\library\html\table\export\ExportExtensionManager::getInstance()->getExportExtensions();
    }    
    $this->assign('exportExtensions', $exportExtensions);
    
    $this->assign('actionField', zibo\library\html\table\ExtendedTable::FIELD_ACTION);
    $this->assign('idField', zibo\library\html\table\ExtendedTable::FIELD_ID);
    $this->assign('orderField', zibo\library\html\table\ExtendedTable::FIELD_ORDER_METHOD);
    $this->assign('searchQueryField', zibo\library\html\table\ExtendedTable::FIELD_SEARCH_QUERY);
    $this->assign('pageRowsField', zibo\library\html\table\ExtendedTable::FIELD_PAGE_ROWS);
    $this->assign('template', $template);
{/php}

{include file=$template table=$table}