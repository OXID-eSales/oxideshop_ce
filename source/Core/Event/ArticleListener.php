<?php
namespace OxidEsales\Eshop\Core\Event;

class ArticleListener
{
    public function onArticleSaved(ArticleSaved $event)
    {
        $id = $event->getArticleId();
    }
}
