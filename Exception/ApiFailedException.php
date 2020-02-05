<?php
/**
 * Copyright © 2020 Wojciech Naruniec (https://naruniec.me/).
 */
declare(strict_types=1);

namespace Wojtekn\CrazyCall\Exception;

use Magento\Framework\Exception\LocalizedException;

/**
 * Represents an error when API call failed permanently
 */
class ApiFailedException extends LocalizedException
{

}
