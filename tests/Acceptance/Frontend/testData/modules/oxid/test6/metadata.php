<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Module information
 */
$aModule = array(
    'id'           => 'test6',
    'title'        => 'Test module #6 (in vendor dir)',
    'description'  => 'Adds PayPal logo in partner box, appends "+ test6" to content title"',
    'thumbnail'    => 'module.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales',
    'url'          => 'http://www.oxid-esales.com',
    'email'        => 'info@oxid-esales.com',
    'extend'       => array(
        'content' => 'oxid/test6/view/myinfo6'
    ),
    'blocks'    => array(
        array("template"=>"widget/sidebar/partners.tpl", "block"=>"partner_logos", "file"=>"oepaypalpartnerbox.tpl"),
    ),
);
