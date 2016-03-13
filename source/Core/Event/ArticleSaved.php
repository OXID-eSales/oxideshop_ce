<?php
namespace OxidEsales\Eshop\Core\Event;

class ArticleSaved extends AbstractEvent
{
    const NAME = 'ArticleSaved';

    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getArticleId()
    {
        return $this->id;
    }
}
