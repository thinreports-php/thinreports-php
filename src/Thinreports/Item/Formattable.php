<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Item;

trait Formattable
{
    /**
     * @access private
     *
     * @return mixed
     */
    public function getFormattedValue()
    {
        $format = $this->format['format'];
        $value  = $this->getValue();

        if (is_null($value) || $value === '') {
            return $value;
        }

        if (!empty($format['type'])) {
            switch ($format['type']) {
                case 'number':
                    $value = $this->applyNumberFormat($value);
                    break;
                case 'datetime':
                    $value = $this->applyDateTimeFormat($value);
                    break;
                case 'padding':
                    $value = $this->applyPaddingFormat($value);
                    break;
            }
        }

        if (!empty($format['base'])) {
            $value = $this->applyBaseFormat($value);
        }
        return $value;
    }

    /**
     * @access private
     *
     * @param mixed $value
     * @return mixed
     */
    private function applyNumberFormat($value)
    {
        if (!is_numeric($value)) {
            return $value;
        }
        $number_format = $this->format['format']['number'];

        $precision = $number_format['precision'] ?: 0;
        $delimiter = $number_format['delimiter'];

        return number_format($value, $precision, '.', $delimiter);
    }

    /**
     * @access private
     *
     * @param mixed $value
     * @return mixed
     */
    private function applyDateTimeFormat($value)
    {
        $datetime_format = $this->format['format']['datetime'];

        if (empty($datetime_format['format'])) {
            return $value;
        } else {
            $datetime = date_create($value);

            if ($datetime) {
                return strftime($datetime_format['format'], date_timestamp_get($datetime));
            } else {
                return $value;
            }
        }
    }

    /**
     * @access private
     *
     * @param mixed $value
     * @return mixed
     */
    private function applyPaddingFormat($value)
    {
        $padding_format = $this->format['format']['padding'];

        $character = $padding_format['char'];
        $direction = $padding_format['direction'];
        $length = intval($padding_format['length']);

        if (is_null($character) || $character === '' || $length === 0) {
            return $value;
        }
        if (mb_strlen($value) >= $length) {
            return $value;
        }

        return $this->padChars($direction, $value, $character, $length);
    }

    /**
     * @access private
     *
     * @param mixed $value
     * @return mixed
     */
    private function applyBaseFormat($value)
    {
        $base_format = $this->format['format']['base'];
        $pattern = '/\{value\}/';

        if (preg_match($pattern, $base_format)) {
            return preg_replace($pattern, $value, $base_format);
        } else {
            return $value;
        }
    }

    /**
     * @access private
     *
     * @param string $direction Possible types are "L" or "R"
     * @param string $string
     * @param string $padstr
     * @param string|integer $length
     * @return string
     */
    private function padChars($direction, $string, $padstr, $length)
    {
        while (mb_strlen($string) < $length) {
            if ($direction == 'L') {
                $string = $padstr . $string;
            } else {
                $string .= $padstr;
            }
        }

        $string_length = mb_strlen($string);

        if ($string_length > $length) {
            if ($direction == 'L') {
                $string = mb_substr($string, $string_length - $length);
            } else {
                $string = mb_substr($string, 0, $length);
            }
        }

        return $string;
    }
}
