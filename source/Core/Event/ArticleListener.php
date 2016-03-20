<?php
namespace OxidEsales\Eshop\Core\Event;

use OxidEsales\Eshop\Application\Model\Article\ListArticleInterface;

class ArticleListener
{
    public function onArticleSaved(ArticleSaved $event)
    {
        $id = $event->getArticleId();

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
}
