<?php
/**
 * Used by PhpStorm to map factory methods to classes for code completion, source code analysis, etc.
 *
 * The code is not ever actually executed and it only needed during development when coding with PhpStorm.
 *
 * @see http://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
 * @see http://blog.jetbrains.com/webide/2013/04/phpstorm-6-0-1-eap-build-129-177/
 */

namespace PHPSTORM_META {

    /** @noinspection PhpUnusedLocalVariableInspection */
    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    $STATIC_METHOD_TYPES = array(
        oxNew('') => array(
            'oxConfig' instanceof \oxConfig,
            'oxSession' instanceof \oxSession,
            'oxArticle' instanceof \oxArticle,
            'oxCategory' instanceof \oxCategory,
            'oxOrder' instanceof \oxOrder,
            'oxBasket' instanceof \oxBasket,
            'oxDb' instanceof \oxDb,
        ),
        \oxRegistry::get('') => array(
            'oxCacheBackend' instanceof \oxCacheBackend,
            'oxReverseProxyBackend' instanceof \oxReverseProxyBackend,
            'oxInputValidator' instanceof \oxInputValidator,
            'oxUtilsServer' instanceof \oxUtilsServer,
            'oxUtilsDate' instanceof \oxUtilsDate,
            'oxConfigFile' instanceof \oxConfigFile,
        )
    );
}
