<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Exception;

class IncompatibleLayout extends StandardException
{
    /**
     * @param string $layout_version
     * @param array[] $required_rules
     */
    public function __construct($layout_version, array $required_rules)
    {
        $message = 'The layout file that created/modified with ' . $layout_version .
                   ' is incompatible with this version.' .
                   ' It is compatible with ' . implode(' and ', $required_rules) . '.';

        parent::__construct($message);
    }
}
