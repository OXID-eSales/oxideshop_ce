<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 18.07.17
 * Time: 10:06
 */

namespace OxidEsales\EshopCommunity\Internal\Dao;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\OxidLegacyServiceInterface;

class BaseDao implements BaseDaoInterface
{

    /** @var string $tablename */
    protected $tablename;

    /** @var  Connection $connection */
    private $connection;

    /** @var ContextInterface $context */
    protected $context;
    /** @var OxidLegacyServiceInterface $legacyService */
    protected $legacyService;

    public function __construct($tablename,
                                Connection $connection,
                                ContextInterface $context,
                                OxidLegacyServiceInterface $legacyService)
    {
        $this->tablename = $tablename;
        $this->context = $context;
        $this->connection = $connection;
        $this->legacyService = $legacyService;
    }

    /**
     * This cuts through all the convolute view generation code
     * where on several layers of code parameters could be changed,
     * so in the end nobody knows what the process result might be.
     *
     * @return string
     */
    public function getViewName($forceTableUsage)
    {

        return $this->getViewNameForTable($this->tablename, $forceTableUsage);
    }

    public function getViewNameForTable($tablename, $forceTableUsage)
    {

        if ($forceTableUsage) {
            return $tablename;
        }

        return $this->getViewPrefix() . $tablename . $this->getViewSuffix();
    }

    /**
     * Currently the view prefix is just a constant string.
     *
     * @return string
     */
    private function getViewPrefix()
    {

        return "oxv_";
    }

    /**
     * This method returns a view suffix for the CE edition. So no shop id
     * comes into consideration. And it expects the table to be a multi
     * linugal table name. So this needs to be changed if there are
     * non multi langual tables that inherit from this base class.
     *
     * @return string
     */
    private function getViewSuffix()
    {

        return '_' . $this->context->getCurrentLanguageAbbrevitation();
    }

    public function getActiveTimeRangeSnippetForTable($tableName)
    {

        $currentTime = $this->legacyService->getCurrentTimeDBFormatted();

        return " ( $tableName.oxactivefrom < '$currentTime' and $tableName.oxactiveto > '$currentTime' ) ";
    }

    /**
     * @param QueryBuilder $query
     * @param              $tablename
     *
     * @return CompositeExpression|string
     */
    protected function getActiveExpressionForTable($query, $tablename)
    {

        $isActiveExpression = $query->expr()->eq("$tablename.oxactive", '1');

        if ($this->context->useTimeCheck()) {
            return $query->expr()->orX(
                $isActiveExpression,
                $this->getActiveTimeRangeExpressionForTable($query, $tablename)
            );
        } else {
            return $isActiveExpression;
        }
    }

    /**
     * @param QueryBuilder $query
     * @param              $tablename
     *
     * @return CompositeExpression
     */
    protected function getActiveTimeRangeExpressionForTable($query, $tablename)
    {

        $query->setParameter(':currenttime', $this->legacyService->getCurrentTimeDBFormatted());

        return $query->expr()->andX(
            $query->expr()->lt("$tablename.oxactivefrom", ':currenttime'),
            $query->expr()->gt("$tablename.oxactiveto", ':currenttime')
        );
    }

    protected function createQueryBuilder()
    {
        $this->connection->setFetchMode(\PDO::FETCH_ASSOC);

        return $this->connection->createQueryBuilder();
    }

}
