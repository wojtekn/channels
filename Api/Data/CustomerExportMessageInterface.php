<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Api\Data;

/**
 * Customer Export message interface
 */
interface CustomerExportMessageInterface
{
    /**
     * Constants for queue message field names.
     */
    const FIELD_ID = 'id';
    const FIELD_COMPANY = 'company';
    const FIELD_CUSTOMER_ID = 'customer_id';
    const FIELD_EMAIL = 'email';
    const FIELD_FIRST_NAME = 'first_name';
    const FIELD_LAST_NAME = 'last_name';
    const FIELD_PHONE = 'phone';
    const FIELD_WEBSITE_ID = 'website_id';

    /**
     * Return the company of the customer
     *
     * @return string
     */
    public function getCompany(): string;

    /**
     * Return the id of the customer
     *
     * @return int
     */
    public function getCustomerId(): int;

    /**
     * Return the email of the customer
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Return the first name of the customer
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Return the last name of the customer
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Return the phone of the customer
     *
     * @return string
     */
    public function getPhone(): string;

    /**
     * Return the id of the website
     *
     * @return int
     */
    public function getWebsiteId(): int;

    /**
     * Set the company of the customer
     *
     * @param string $company
     * @return $this
     */
    public function setCompany(string $company);

    /**
     * Set the id of the customer
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId(int $customerId);

    /**
     * Set the email of the customer
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email);

    /**
     * Set the first name of the customer
     *
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName);

    /**
     * Set the last name of the customer
     *
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName);

    /**
     * Set the phone of the customer
     *
     * @param string $phone
     * @return $this
     */
    public function setPhone(string $phone);

    /**
     * Set the id of the website
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId(int $websiteId);
}
