<?php

namespace joppa\view\backend;

use joppa\form\backend\NodeSettingsForm;
use joppa\form\backend\SiteSelectForm;

use joppa\model\Node;
use joppa\model\NodeSettings;
use joppa\model\Site;

use zibo\library\Structure;

/**
 * Backend view for the advanced node settings
 */
class NodeAdvancedView extends BaseView {

    /**
     * Relative path to the stylesheet for this view
     * @var string
     */
    const STYLE = 'web/styles/joppa/advanced.css';

    /**
     * Construct this view
     * @param joppa\form\backend\SiteSelectForm $siteSelectForm form to select another site
     * @param joppa\form\backend\NodeSettingsForm $nodeSettingsForm form to edit the node settings
     * @param joppa\model\Site $site the current site
     * @param joppa\model\Node $node the current node
     * @return null
     */
	public function __construct(SiteSelectForm $siteSelectForm, NodeSettingsForm $nodeSettingsForm, Site $site, Node $node) {
		parent::__construct($siteSelectForm, $site, $node, 'joppa/backend/node.advanced');

		$nodeSettings = $nodeSettingsForm->getNodeSettings(false);
		$settings = $this->getHtmlFromNodeSettings($nodeSettings);

		$this->set('node', $node);
		$this->set('form', $nodeSettingsForm);
		$this->set('settings', $settings);

		$this->addStyle(self::STYLE);
        $this->addInlineJavascript("joppaInitializeAdvanced();");
	}

	/**
     * Get a html representation of a NodeSettings object
     * @param joppa\model\NodeSettings $nodesettings
     * @return string html representation of the NodeSettings object
	 */
	private function getHtmlFromNodeSettings(NodeSettings $nodeSettings) {
		$inheritedSettings = $nodeSettings->getInheritedNodeSettings();
		if ($inheritedSettings) {
		    $arrayInheritedSettings = $inheritedSettings->getArray(true, true);
		} else {
            $arrayInheritedSettings = array();
		}
		$arraySettings = $nodeSettings->getArray();

        $settings = Structure::merge($arrayInheritedSettings, $arraySettings);
        ksort($settings);

        $html = '';
        foreach ($settings as $key => $setting) {
        	$value = $setting->getIniString(true);
        	if ($setting->inherit) {
        		$value = substr($value, 1);
        	}

            if (isset($arraySettings[$key])) {
            	$html .= '<strong>' . $value . '</strong>';
            } else {
            	$html .= $value;
            }

            $html .= "<br />\n";
        }

        return $html;
	}

}