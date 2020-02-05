# CrazyCall service connector  

## Overview

Module integrates Magento with Crazy Call service. It allows synchronizing Magento customer accounts into Crazy Call contacts.

## Compatibility

- Magento >= 2.3
- Supports both Magento Open Source (Community) and Magento Commerce (Enterprise)

## Installation Instructions

For now extension is in beta state, you can install it from private repository. Add it in composer.json file of your project in repositories section:

    {
        "type": "vcs",
        "url": "git@github.com:wojtekn/crazy-call.git"
    }

For beta state, also change "minimum-stability" to "dev".

Then install using composer by using commands:

1. composer require wojtekn/module-crazycall dev-master
2. composer update wojtekn/module-crazycall
3. bin/magento setup:upgrade
4. bin/magento cache:flush

## Technical approach

Customer account synchronization is scheduled to queue on each account or address change. Queue is configured to use database by default,
however it can be adjusted to use RabbitMQ or any other queue backend supported by Magento.

By default, Magento processes database queue on CRON basis. CRON job runs once per minute, executes queue messages
and sends scheduled contacts to the service.

To start queue processing manually:

bin/magento queue:consumers:start crazycallCustomerExport --max-messages=1

Copyright
---------
Copyright © 2020 Wojciech Naruniec (https://naruniec.me/). All rights reserved.
