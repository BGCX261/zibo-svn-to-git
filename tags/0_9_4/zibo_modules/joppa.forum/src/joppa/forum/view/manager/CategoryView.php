<?php

namespace joppa\forum\view\manager;

use joppa\forum\controller\manager\ForumManager;
use joppa\forum\form\ForumCategoryForm;
use joppa\forum\form\OrderForm;

use zibo\admin\view\BaseView;

use zibo\jquery\Module as JQuery;

use zibo\library\html\table\ExtendedTable;
use zibo\library\smarty\view\SmartyView;

/**
 * View for the manager of a forum category
 */
class CategoryView extends SmartyView {

	/**
	 * Path to the template of this view
	 * @var string
	 */
	const TEMPLATE = 'joppa/forum/manager/category';

	/**
	 * Constructs a new view
	 * @param zibo\library\html\table\ExtendedTable $table
	 * @param string $urlAdd
	 * @return null
	 */
	public function __construct(ForumCategoryForm $categoryForm, ExtendedTable $boardTable = null, OrderForm $orderForm = null, $addAction = null) {
		parent::__construct(self::TEMPLATE);

		$this->set('categoryForm', $categoryForm);
		$this->set('boardTable', $boardTable);
		$this->set('orderForm', $orderForm);
		$this->set('addAction', $addAction);

		if ($boardTable) {
	        $this->addJavascript(BaseView::SCRIPT_TABLE);
	        $this->addJavascript(JQuery::SCRIPT_JQUERY_UI);
	        $this->addJavascript(ForumManager::SCRIPT_MANAGER);
	        $this->addInlineJavascript('joppaForumInitializeOrder("' . $boardTable->getId() . '");');
		}
	}

}