<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\Channels\Controller\Adminhtml\Customer;

use Magento\Customer\Controller\Adminhtml\Index\Edit as OriginalCustomerEdit;

/**
 * Class extends original controller to allow access without secret key validation.
 *
 * It's needed to allow linking to customer profiles from external Channels application.
 */
class Edit extends OriginalCustomerEdit
{
    protected $_publicActions = ['edit'];
}
