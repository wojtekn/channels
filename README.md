# CrazyCall service connector  

## Overview

Module integrates Magento with Crazy Call service. It allows synchronizing Magento customer accounts into Crazy Call contacts.

## Technical approach

Customer account synchronization is scheduled to queue on each change. Queue is configured to use database by default,
however it can be adjusted to use RabbitMQ.

By default, Magento processes database queue on CRON basis. CRON job runs once per minute, executes queue messages
and sends scheduled contacts.

To start queue processing manually:

bin/magento queue:consumers:start crazycallCustomerExport --max-messages=1
