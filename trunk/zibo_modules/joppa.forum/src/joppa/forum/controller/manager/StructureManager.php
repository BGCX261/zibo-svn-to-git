<?php

namespace joppa\forum\controller\manager;

use joppa\forum\form\ForumBoardForm;
use joppa\forum\form\ForumCategoryForm;
use joppa\forum\form\OrderForm;
use joppa\forum\model\ForumBoardModel;
use joppa\forum\model\ForumCategoryModel;
use joppa\forum\table\decorator\OrderDecorator;
use joppa\forum\view\manager\BoardView;
use joppa\forum\view\manager\CategoriesView;
use joppa\forum\view\manager\CategoryView;

use zibo\admin\controller\AbstractController;

use zibo\library\html\table\decorator\ZebraDecorator;
use zibo\library\validation\exception\ValidationException;

use zibo\orm\scaffold\table\decorator\DataDecorator;
use zibo\orm\scaffold\table\ModelTable;

/**
 * Controller to manage the structure of the forum
 */
class StructureManager extends AbstractController {

	/**
	 * Action to the detail of a board
	 * @var string
	 */
	const ACTION_BOARD = 'board';

	/**
	 * Action to the detail of a category
	 * @var string
	 */
	const ACTION_CATEGORY = 'category';

	/**
	 * Translation key for the name of this manager
	 * @var string
	 */
	const TRANSLATION_NAME = 'joppa.forum.title.manager.structure';

	/**
	 * Translation key for the delete button
	 * @var string
	 */
	const TRANSLATION_DELETE = 'button.delete';

    /**
     * Translation key for the delete confirmation message
     * @var string
     */
    const TRANSLATION_DELETE_CONFIRM = 'table.label.delete.confirm';

	/**
	 * Hook with the ORM module
	 * @var array
	 */
	public $useModels = array(ForumCategoryModel::NAME, ForumBoardModel::NAME);

	/**
	 * Action to show an overview of the categories
	 * @return null
	 */
	public function indexAction() {
        $basePath = $this->request->getBasePath();
		$urlCategory = $basePath . '/' . self::ACTION_CATEGORY;

		$orderForm = new OrderForm($basePath);
		if ($orderForm->isSubmitted()) {
			$categories = $orderForm->getOrder();

			$this->models[ForumCategoryModel::NAME]->orderCategories($categories);

            $this->response->setRedirect($basePath);
            return;
		}

		$categoryTable = $this->createCategoryTable($basePath, $urlCategory . '/');
        $categoryTable->processForm();

        if ($this->response->willRedirect()) {
        	return;
        }

		$view = new CategoriesView($categoryTable, $orderForm, $urlCategory);

		$this->response->setView($view);
	}

	/**
	 * Action to add or edit a category
	 * @param integer $id Id of the category to edit
	 * @return null
	 */
	public function categoryAction($id = null) {
		$basePath = $this->request->getBasePath();
		$categoryAction = $basePath . '/' . self::ACTION_CATEGORY;

        $category = null;

        if ($id) {
        	$category = $this->models[ForumCategoryModel::NAME]->findById($id, 0);
        }

        $categoryForm = new ForumCategoryForm($categoryAction, $category);
        if ($categoryForm->isSubmitted()) {
        	if ($categoryForm->isCancelled()) {
        		$this->response->setRedirect($this->request->getBasePath());
        		return;
        	}

        	try {
        		$categoryForm->validate();

        		$category = $categoryForm->getCategory();

        		$this->models[ForumCategoryModel::NAME]->save($category);

        		$this->response->setRedirect($this->request->getBasePath());
                return;
        	} catch (ValidationException $validationException) {
        		$categoryForm->setValidationException($validationException);
            }
        }

        $boardTable = null;
        $boardAction = $basePath . '/' . self::ACTION_BOARD . '/';
        $orderForm = null;
        if ($category) {
        	$formAction = $categoryAction . '/' . $category->id;

            $boardTable = $this->createBoardTable($category->id, $formAction, $boardAction);
            $boardTable->processForm();

	        $orderForm = new OrderForm($formAction);
	        if ($orderForm->isSubmitted()) {
	            $boards = $orderForm->getOrder();

	            $this->models[ForumBoardModel::NAME]->orderBoards($category->id, $boards);

	            $this->response->setRedirect($formAction);
	        }

            if ($this->response->willRedirect()) {
            	return;
            }

            $boardAction .= '0/' . $category->id;
        }

        $view = new CategoryView($categoryForm, $boardTable, $orderForm, $boardAction);

        $this->response->setView($view);
	}

	public function boardAction($idBoard = null, $idCategory = null) {
		$board = null;

		if ($idBoard) {
			$board = $this->models[ForumBoardModel::NAME]->findById($idBoard, 1);
		} elseif ($idCategory) {
			$board = $this->models[ForumBoardModel::NAME]->createData();
			$board->category = $idCategory;
		}

        $form = new ForumBoardForm($this->request->getBasePath() . '/' . self::ACTION_BOARD, $board);
        if ($form->isSubmitted()) {
            if ($form->isCancelled()) {
            	if ($board && $board->category) {
                    $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_CATEGORY . '/' . $board->category);
            	} else {
                    $this->response->setRedirect($this->request->getBasePath());
            	}
                return;
            }

            try {
                $form->validate();

                $board = $form->getBoard();

                $this->models[ForumBoardModel::NAME]->save($board);

                $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_CATEGORY . '/' . $board->category);
                return;
            } catch (ValidationException $validationException) {
                $form->setValidationException($validationException);
            }
        }

        $view = new BoardView($form);

        $this->response->setView($view);
	}

    /**
     * Deletes a category or an array of categories
     * @param integer|array $category Id of the category or an array of category ids
     * @return null
     */
    public function deleteCategory($category) {
        $this->models[ForumCategoryModel::NAME]->delete($category);

        $this->response->setRedirect($this->request->getBasePath());
    }

    /**
     * Deletes a board or an array of boards
     * @param integer|array $board Id of the board or an array of board ids
     * @return null
     */
    public function deleteBoard($board) {
        $board = $this->models[ForumBoardModel::NAME]->delete($board);

        if (!$board) {
	        $this->response->setRedirect($this->request->getBasePath());
        }

        if (is_array($board)) {
        	$board = array_pop($board);
        }

        $categoryId = $board->category;

        $this->response->setRedirect($this->request->getBasePath() . '/' . self::ACTION_CATEGORY . '/' . $categoryId);
    }

	/**
	 * Creates a new category table
	 * @return zibo\orm\scaffold\table\ModelTable
	 */
	private function createCategoryTable($tableAction, $categoryAction = null) {
		$translator = $this->getTranslator();

		$model = $this->models[ForumCategoryModel::NAME];

		$table = new ModelTable($model, $tableAction);
		$table->addDecorator(new ZebraDecorator(new OrderDecorator()));
		$table->addDecorator(new DataDecorator($model->getMeta(), $categoryAction));

		$table->addAction(
            $translator->translate(self::TRANSLATION_DELETE),
            array($this, 'deleteCategory'),
            $translator->translate(self::TRANSLATION_DELETE_CONFIRM)
		);
		$table->setId('tableForumCategory');

		$query = $table->getModelQuery();
		$query->addOrderBy('{orderIndex} ASC');

		return $table;
	}

	/**
	 * Creates a new board table
	 * @return zibo\orm\scaffold\table\ModelTable
	 */
	private function createBoardTable($categoryId, $tableAction, $boardAction = null) {
		$translator = $this->getTranslator();

		$model = $this->models[ForumBoardModel::NAME];

		$table = new ModelTable($model, $tableAction);
		$table->addDecorator(new ZebraDecorator(new OrderDecorator()));
		$table->addDecorator(new DataDecorator($model->getMeta(), $boardAction));

		$table->addAction(
            $translator->translate(self::TRANSLATION_DELETE),
            array($this, 'deleteBoard'),
            $translator->translate(self::TRANSLATION_DELETE_CONFIRM)
		);
		$table->setId('tableForumBoard');

		$query = $table->getModelQuery();
		$query->addCondition('{category} = %1%', $categoryId);
		$query->addOrderBy('{orderIndex} ASC');

		return $table;
	}

}