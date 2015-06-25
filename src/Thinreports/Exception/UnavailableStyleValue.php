<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Exception;

class UnavailableStyleValue extends StandardException
{
    /**
     * @param string $style_name
     * @param mixed $value
     * @param mixed[] $available_values
     */
    public function __construct($style_name, $value, array $available_values)
    {
        $message = $value . ' is not available for ' . $style_name . ' style. ' .
                   'Available values are ' . implode(', ', $available_values) . '.';

        parent::__construct($message);
    }
}
