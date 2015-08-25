<?php

namespace joppa\forum\view\manager;

use joppa\forum\controller\manager\ForumManager;
use joppa\forum\form\OrderForm;

use zibo\admin\view\BaseView;

use zibo\jquery\Module as JQuery;

use zibo\library\html\table\ExtendedTable;
use zibo\library\smarty\view\SmartyView;

/**
 * View for the manager of the categories
 */
class CategoriesView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/manager/categories';

	/**
	 * Constructs a new view
	 * @param zibo\library\html\table\ExtendedTable $table
	 * @param string $urlAdd
	 * @return null
	 */
	public function __construct(ExtendedTable $categoriesTable, OrderForm $orderForm = null, $addAction = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('categoriesTable', $categoriesTable);
		$this->set('orderForm', $orderForm);
		$this->set('addAction', $addAction);

		$this->addJavascript(BaseView::SCRIPT_TABLE);
		$this->addJavascript(JQuery::SCRIPT_JQUERY_UI);
		$this->addJavascript(ForumManager::SCRIPT_MANAGER);
		$this->addInlineJavascript('joppaForumInitializeOrder("' . $categoriesTable->getId() . '");');
	}

}