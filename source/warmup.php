<?php

use OxidEsales\Eshop\Application\Model\Article\ListArticleInterface;

require_once dirname(__FILE__) . "/bootstrap.php";

$ids = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC)->getAll('SELECT oxid FROM oxarticles');

foreach(array_column($ids, 'oxid') as $id) {
    $article = new \oxArticle();
    $article->load($id);

    $reflection = new \ReflectionClass(ListArticleInterface::class);
    $publicMethods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

    $data = [];

    foreach ($publicMethods as $method) {
        $methodName = $method->getName();
        $data[$methodName] = $article->$methodName();
    }

    // pre-calculate
    // store as JSON
    $json = json_encode($data);

    \oxDb::getDb()->execute(
        sprintf(
            'REPLACE INTO list_article (`id`, `data`) values (%s, %s)',
            \oxDb::getDb()->quote($id),
            \oxDb::getDb()->quote($json)
        )
    );
}
