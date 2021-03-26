<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Newsletter\Dao;

use Doctrine\DBAL\Exception;
use OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject\NewsletterRecipient;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class NewsletterRecipientsDao implements NewsletterRecipientsDaoInterface
{
    /**
     * @var QueryBuilderFactoryInterface
     */
    private $queryBuilderFactory;

    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @param int $shopId
     *
     * @return NewsletterRecipient[]
     * @throws Exception
     */
    public function get(int $shopId): array
    {
        $recipientList = [];

        $subscribersList = $this->getSubscribersList($shopId);

        foreach ($subscribersList as $row) {
            $recipient = new NewsletterRecipient();
            $recipient->setSalutation($row['Salutation']);
            $recipient->setFistName($row['Firstname']);
            $recipient->setLastName($row['Lastname']);
            $recipient->setEmail($row['Email']);
            $recipient->setOtpInState($this->getOptInstate($row['OptInState']));
            $recipient->setCountry($row['Country']);
            $groups = implode(",", $this->getUserGroups($row['UserId']));
            $recipient->setUserGroups($groups);
            $recipientList[] = $recipient;
        }

        return $recipientList;
    }

    /**
     * @param int $shopId
     *
     * @return array
     * @throws Exception
     */
    private function getSubscribersList(int $shopId): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select([
                'n.OXSAL as Salutation',
                'n.OXFNAME as Firstname',
                'n.OXLNAME as Lastname',
                'u.OXUSERNAME as Email',
                'n.OXDBOPTIN AS OptInState',
                'c.OXTITLE as Country',
                'u.oxid as UserId'
            ])
            ->from('oxnewssubscribed', 'n')
            ->join('n', 'oxuser', 'u', 'u.oxid=n.oxuserid')
            ->join('u', 'oxcountry', 'c', 'u.OXCOUNTRYID=c.OXID')
            ->where('n.oxshopid = :shopId')
            ->setParameters(["shopId" => $shopId])
            ->orderBy('u.oxcreate', 'DESC');

        return $queryBuilder->execute()->fetchAllAssociative();
    }

    /**
     * @param string $userId
     *
     * @return array
     * @throws Exception
     */
    private function getUserGroups(string $userId): array
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select([
                'g.oxtitle'
            ])
            ->from('oxgroups', 'g')
            ->leftJoin('g', 'oxobject2group', 'objg', 'g.oxid=objg.oxgroupsid')
            ->where('objg.oxobjectid = :userId')
            ->orderBy('g.oxid')
            ->setParameters(['userId' => $userId]);

        return $queryBuilder->execute()->fetchFirstColumn();
    }

    private function getOptInstate(string $state): string
    {
        switch ($state) {
            case "1":
                return "subscribed";
            case "2":
                return "not confirmed";
            default:
                return "not subscribed";
        }
    }
}
