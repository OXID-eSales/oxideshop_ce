<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File: insert.oxid_content.php
 * Type: string, html
 * Name: oxid_content
 * Purpose: Output content snippet
 * add [{insert name="oxid_content" ident="..."}] where you want to display content
 * -------------------------------------------------------------
 *
 * @param array $params params
 * @param Smarty &$smarty clever simulation of a method
 *
 * @deprecated will be moved to the separate smarty component
 *
 * @return string
 */
function smarty_function_oxcontent($params, &$smarty)
{
    $text = null;
    $id = $params['oxid'] ?? null;
    $loadId = $params['ident'] ?? null;

    if (!Registry::getConfig()->getActiveShop()->isProductiveMode()) {
        $text = sprintf('<b>content not found ! check ident(%s) !</b>', $loadId ?? 'undefined');
    }
    $smarty->oxidcache = new Field($text, Field::T_RAW);

    if ($id || $loadId) {
        $content = oxNew('oxcontent');
        $contentFound = $id ? $content->load($id) : $content->loadbyIdent($loadId);
        if ($contentFound && $content->isActive()) {
            $field = $params['field'] ?? 'oxcontent';
            $property = "oxcontents__{$field}";
            if (Registry::getConfig()->getConfigParam('deactivateSmartyForCmsContent')) {
                $text = $content->$property->value;
            } else {
                $smarty->oxidcache = clone $content->$property;
                $smarty->compile_check = true;
                $resourceName = sprintf(
                    'ox:%s%s%s%s%s',
                    (string)$loadId,
                    (string)$id,
                    $field,
                    Registry::getLang()->getBaseLanguage(),
                    Registry::getConfig()->getShopId()
                );
                try {
                    $text = $smarty->fetch($resourceName);
                } catch (\Throwable $error) {
                    Registry::getLogger()->error($error->getMessage(), [$error]);
                    $text = '';
                }
                $smarty->compile_check = Registry::getConfig()->getConfigParam('blCheckTemplates');
            }
        }
    }
    // if we write '[{oxcontent ident="oxemailfooterplain" assign="fs_text"}]' the content wont be outputted.
    // instead of this the content will be assigned to variable.
    if (isset($params['assign']) && $params['assign']) {
        $smarty->assign($params['assign'], $text);
    } else {
        return $text;
    }
}
