<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Exception;

use Magento\Framework\Exception\LocalizedException;

/**
 * Represents an error when API call failed but job can be retried
 */
class ApiFailedButRetryException extends LocalizedException
{

}
