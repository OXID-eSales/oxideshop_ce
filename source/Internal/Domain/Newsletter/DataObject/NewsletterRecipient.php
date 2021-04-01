<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Newsletter\DataObject;

class NewsletterRecipient
{
    /**
     * @var string
     */
    private $salutation;

    /**
     * @var string
     */
    private $fistName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $otpInState;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $userGroups;

    public const SALUTATION = 'Salutation';
    public const FIRST_NAME = 'Firstname';
    public const LAST_NAME = 'LastName';
    public const EMAIL = 'Email';
    public const OPT_IN_STATE = 'Opt-In state';
    public const COUNTRY = 'Country';
    public const ASSIGNED_USER_GROUPS = 'Assigned user groups';

    private const OPT_IN_STATE_SUBSCRIBED = 'subscribed';
    private const OPT_IN_STATE_NOT_CONFIRMED = 'not confirmed';
    private const OPT_IN_STATE_NOT_SUBSCRIBED = 'not subscribed';

    private $otpInStateList = [
        '0' => self::OPT_IN_STATE_NOT_SUBSCRIBED,
        '1' => self::OPT_IN_STATE_SUBSCRIBED,
        '2' => self::OPT_IN_STATE_NOT_CONFIRMED
    ];

    /**
     * @return string
     */
    public function getSalutation(): string
    {
        return $this->salutation;
    }

    /**
     * @param string $salutation
     *
     * @return NewsletterRecipient
     */
    public function setSalutation(string $salutation): NewsletterRecipient
    {
        $this->salutation = $salutation;

        return $this;
    }

    /**
     * @return string
     */
    public function getFistName(): string
    {
        return $this->fistName;
    }

    /**
     * @param string $fistName
     *
     * @return NewsletterRecipient
     */
    public function setFistName(string $fistName): NewsletterRecipient
    {
        $this->fistName = $fistName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return NewsletterRecipient
     */
    public function setLastName(string $lastName): NewsletterRecipient
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return NewsletterRecipient
     */
    public function setEmail(string $email): NewsletterRecipient
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getOtpInState(): string
    {
        return $this->otpInState;
    }

    /**
     * @param string $otpInState
     *
     * @return NewsletterRecipient
     */
    public function setOtpInState(string $otpInState): NewsletterRecipient
    {
        $this->otpInState = self::OPT_IN_STATE_NOT_SUBSCRIBED;
        if (array_key_exists($otpInState, $this->otpInStateList)) {
            $this->otpInState = $this->otpInStateList[$otpInState];
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOtpInStateAsNumber(): string
    {
        return array_search($this->otpInState, $this->otpInStateList);
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return NewsletterRecipient
     */
    public function setCountry(string $country): NewsletterRecipient
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserGroups(): string
    {
        return $this->userGroups;
    }

    /**
     * @param string $userGroups
     *
     * @return NewsletterRecipient
     */
    public function setUserGroups(string $userGroups): NewsletterRecipient
    {
        $this->userGroups = $userGroups;

        return $this;
    }
}
