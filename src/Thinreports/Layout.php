<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports;

use Thinreports\Exception;
use Thinreports\Item;
use Thinreports\Page\Page;

class Layout
{
    const FILE_EXT_NAME = 'tlf';
    const COMPATIBLE_VERSION_RANGE_START = '>= 0.8.2';
    const COMPATIBLE_VERSION_RANGE_END   = '< 1.0.0';

    /**
     * @param string $filename
     * @return self
     * @throws Exception\StandardException
     */
    static public function loadFile($filename)
    {
        if (pathinfo($filename, PATHINFO_EXTENSION) != self::FILE_EXT_NAME) {
            $filename .= '.' . self::FILE_EXT_NAME;
        }

        if (!file_exists($filename)) {
            throw new Exception\StandardException('Layout File Not Found', $filename);
        }

        return self::loadData(file_get_contents($filename, true));
    }

    /**
     * @param string $data
     * @return self
     */
    static public function loadData($data)
    {
        $schema = self::parse($data);
        $identifier = md5($data);

        return new self($schema, $identifier);
    }

    /**
     * @access private
     *
     * @param string $file_content
     * @return array
     * @throws Exception\IncompatibleLayout
     */
    static public function parse($file_content)
    {
        $schema = json_decode($file_content, true);

        if (!self::isCompatible($schema['version'])) {
            $rules = array(
                self::COMPATIBLE_VERSION_RANGE_START,
                self::COMPATIBLE_VERSION_RANGE_END
            );
            throw new Exception\IncompatibleLayout($schema['version'], $rules);
        }

        return $schema;
    }

    /**
     * @access private
     *
     * @param string $layout_version
     * @return boolean
     */
    static public function isCompatible($layout_version)
    {
        $rules = array(
            self::COMPATIBLE_VERSION_RANGE_START,
            self::COMPATIBLE_VERSION_RANGE_END
        );

        foreach ($rules as $rule) {
            list($operator, $version) = explode(' ', $rule);

            if (!version_compare($layout_version, $version, $operator)) {
                return false;
            }
        }
        return true;
    }

    private $schema;
    private $identifier;
    private $item_schemas;

    /**
     * @param array $schema
     * @param string $identifier
     */
    public function __construct(array $schema, $identifier)
    {
        $this->schema = $schema;
        $this->identifier = $identifier;
        $this->item_schemas = $this->buildItemSchemas($schema['items']);
    }

    /**
     * @return string
     */
    public function getReportTitle()
    {
        return $this->schema['title'];
    }

    /**
     * @return string
     */
    public function getPagePaperType()
    {
        return $this->schema['report']['paper-type'];
    }

    /**
     * @return string[]|null
     */
    public function getPageSize()
    {
        if ($this->isUserPaperType()) {
            return array(
              $this->schema['report']['width'],
              $this->schema['report']['height']
            );
        } else {
            return null;
        }
    }

    /**
     * @return boolean
     */
    public function isPortraitPage()
    {
        return $this->schema['report']['orientation'] === 'portrait';
    }

    /**
     * @return boolean
     */
    public function isUserPaperType()
    {
        return $this->schema['report']['paper-type'] === 'user';
    }

    /**
     * @access private
     *
     * @return string
     */
    public function getLastVersion()
    {
        return $this->schema['version'];
    }

    /**
     * @access private
     *
     * @param array $item_schemas
     * @return array array('with_id' => array, 'without_id' => array)
     */
    public function buildItemSchemas(array $item_schemas)
    {
        $with_id = $without_id = array();

        foreach ($item_schemas as $item_schema) {
            $item_id = $item_schema['id'];

            if ($item_id === '') {
                $without_id[] = $item_schema;
            } else {
                $with_id[$item_id] = $item_schema;
            }
        }

        return array('with_id' => $with_id, 'without_id' => $without_id);
    }

    /**
     * @access private
     *
     * @param string $id
     * @return boolean
     */
    public function hasItemById($id)
    {
        return array_key_exists($id, $this->item_schemas['with_id']);
    }

    /**
     * @access private
     *
     * @param Page $owner
     * @param string $id
     * @return Item\AbstractItem
     * @throws Exception\StandardException
     */
    public function createItem(Page $owner, $id)
    {
        if (!$this->hasItemById($id)) {
            throw new Exception\StandardException('Item Not Found', $id);
        }

        $item_schema = $this->item_schemas['with_id'][$id];

        switch ($item_schema['type']) {
            case 'text-block':
                return new Item\TextBlockItem($owner, $item_schema);
                break;
            case 'image-block':
                return new Item\ImageBlockItem($owner, $item_schema);
                break;
            case 'page-number';
                return new Item\PageNumberItem($owner, $item_schema);
                break;
            default:
                return new Item\BasicItem($owner, $item_schema);
                break;
        }
    }

    /**
     * @access private
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @access private
     *
     * @return array
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @access private
     *
     * @param string $filter all|with_id|without_id
     * @return array
     */
    public function getItemSchemas($filter = 'all')
    {
        switch ($filter) {
            case 'all':
                return $this->schema['items'];
                break;
            case 'with_id':
                return $this->item_schemas['with_id'];
                break;
            case 'without_id':
                return $this->item_schemas['without_id'];
                break;
        }
    }
}
