# CrazyCall service connector  

## Overview

Module integrates Magento with Crazy Call service. It allows synchronizing Magento customer accounts into Crazy Call contacts.

## Compatibility

- Magento >= 2.3
- Supports both Magento Open Source (Community) and Magento Commerce (Enterprise)

## Installation Instructions

Install extension using composer by using commands:

    composer require wojtekn/module-crazycall
    composer update wojtekn/module-crazycall
    bin/magento setup:upgrade
    bin/magento cache:flush

## Configuration

To integrate module with the service:

1. Log in to Magento 2 backend
2. Navigate to Stores -> Configuration
3. Navigate to Services -> Crazy Call tab
4. Enable integration, fill API details
5. Save

API account and API key can be found in Crazy Call backend in "For developers" tab.

## Scheduling existing customers

### CLI

Module provides a command to schedule existing customers for export. It can be used when module is integrated in store which already have customers. To do so, run a command:

    bin/magento crazycall:customer:export

### Admin interface

It's also possible to scheudule multiple customers for Crazy Call export directly in the admin interface.

In order to do this, navigate to customer grid, select customers and use "Export to Crazy Call" mass action.

Note that recommended way is CLI, especially if store contains significant number of customers.

## Technical approach

Customer account synchronization is scheduled to queue on each account or address change. Queue is configured to use database by default,
however it can be adjusted to use RabbitMQ or any other queue backend supported by Magento.

By default, Magento processes database queue on CRON basis. CRON job runs once per minute, executes queue messages
and sends scheduled contacts to the service.

To start queue processing manually:

    bin/magento queue:consumers:start crazycallCustomerExport --max-messages=1

Copyright
---------
Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
