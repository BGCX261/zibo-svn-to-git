<?php

function smarty_block_form($params, $content, &$smarty, &$repeat) {
    if (empty($params['form'])) {
        throw new Exception('No form parameter provided');
    }

    if ($repeat) {
        $smarty->assign('block_form', $params['form']);
        return;
    }

    $smarty->clear_assign('block_form');

    $form = $params['form'];

    $html = '<form' . $form->getHtml() . '>' . "\n";
    $html .= $form->getField()->getHtml() . "\n";
    $html .= $content;
    $html .= '</form>';
    return $html;
}