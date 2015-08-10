<?php
namespace Thinreports;

use Symfony\Component\Yaml\Yaml;

class TestCase extends \PHPUnit_Framework_TestCase
{
    static protected $_item_formats = array();

    function rootDir()
    {
        return realpath(__DIR__ . '/../');
    }

    function dataDir()
    {
        return $this->rootDir() . '/data';
    }

    function dataItemFormat($item_name, $format_key = 'default')
    {
        $this->dataLoadItemFormat($item_name);
        return static::$_item_formats[$item_name][$format_key];
    }

    function dataItemFormatsFor($item_name)
    {
        $this->dataLoadItemFormat($item_name);

        $formats = array();
        foreach (static::$_item_formats[$item_name] as $key => $format) {
            $formats[$format['id']] = $format;
        }
        return $formats;
    }

    function dataItemFormats(array $item_name_and_keys)
    {
        $formats = array();

        foreach ($item_name_and_keys as $item_name_and_key) {
            list($item_name, $format_key) = $item_name_and_key;

            $format = $this->dataItemFormat($item_name, $format_key);
            $formats[$format['id']] = $format;
        }
        return $formats;
    }

    function dataLayoutFile($name)
    {
        return $this->dataDir() . '/layouts/' . $name;
    }

    private function dataLoadItemFormat($item_name)
    {
        if (!array_key_exists($item_name, static::$_item_formats)) {
            $format_file = $this->dataDir() . '/items/' . $item_name . '.yml';
            $formats = Yaml::parse(file_get_contents($format_file));

            static::$_item_formats[$item_name] = $formats;
        }
    }
}
