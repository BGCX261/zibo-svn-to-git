<?php

namespace joppa\model;

use zibo\library\orm\model\SimpleModel;
use zibo\library\widget\model\WidgetModel as ZiboWidgetModel;

use zibo\ZiboException;

/**
 * Model to manage the widget
 */
class WidgetModel extends SimpleModel {

	/**
	 * Name of this model
	 * @var string
	 */
	const NAME = 'Widget';

    /**
     * Get the instance of a widget
     * @param int $id id of the widget
     * @return zibo\library\widget\controller\Widget
     * @throws zibo\ZiboException when no widget is found for the provided id
     */
	public function getWidget($id) {
		$widget = $this->findById($id, 0);
		if (!$widget) {
			throw new ZiboException('Could not find widget with id ' . $id);
		}

		$widget = ZiboWidgetModel::getInstance()->getWidget($widget->namespace, $widget->name);
		$widget->setIdentifier($id);

		return $widget;
	}

	/**
     * Get an array of widget id's for a widget
     * @param string $namespace namespace of the widget
     * @param string $name name of the widget
     * @return array Array with the widget id as key and value
	 */
	public function getWidgetIds($namespace, $name) {
		$query = $this->createQuery(0);
		$query->addCondition('{namespace} = %1% AND {name} = %2%', $namespace, $name);

		$widgets = $query->query();

		$widgetIds = array();
		foreach ($widgets as $widget) {
			$widgetIds[$widget->id] = $widget->id;
		}

		return $widgetIds;
	}

	/**
     * Add a new widget to the model
     * @param string $namespace namespace of the widget
     * @param string $name name of the widget
     * @return mixed widget with a new id
	 */
	public function addWidget($namespace, $name) {
		ZiboWidgetModel::getInstance()->getWidget($namespace, $name);

		$widget = $this->createData();
		$widget->namespace = $namespace;
		$widget->name = $name;

		$this->save($widget);

		return $widget;
	}

}