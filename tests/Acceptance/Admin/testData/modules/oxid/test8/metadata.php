<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test8',
    'title'        => 'Test module #8 (in vendor dir, does not extend any class)',
    'description'  => 'Adds PayPal logo in partner box',
    'thumbnail'    => 'module.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'extend'       => array(
    ),
    'blocks'    => array(
        array("template"=>"widget/sidebar/partners.tpl", "block"=>"partner_logos", "file"=>"oepaypalpartnerbox.tpl"),
    ),
);
