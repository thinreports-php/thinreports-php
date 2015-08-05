<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Exception;

class StandardException extends \Exception
{
    /**
     * @var string|null The subject of Exception
     */
    protected $subject = null;

    /**
     * @param string $messages,...
     */
    public function __construct($messages)
    {
        $messages = func_get_args();

        if (count($messages) > 1) {
            $this->subject = $messages[0];
        }
        parent::__construct(implode(' - ', $messages));
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
