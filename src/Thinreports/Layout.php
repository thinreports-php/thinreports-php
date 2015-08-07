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

        return self::parse(file_get_contents($filename, true));
    }

    /**
     * @access private
     *
     * @param string $file_content
     * @return self
     * @throws Exception\IncompatibleLayout
     */
    static public function parse($file_content)
    {
        $format = json_decode($file_content, true);

        if (!self::isCompatible($format['version'])) {
            $rules = array(
                self::COMPATIBLE_VERSION_RANGE_START,
                self::COMPATIBLE_VERSION_RANGE_END
            );
            throw new Exception\IncompatibleLayout($format['version'], $rules);
        }

        $item_formats = self::extractItemFormats($format['svg']);
        self::cleanFormat($format);

        return new self($format, $item_formats);
    }

    /**
     * @access private
     *
     * @param string $layout_format
     * @return array
     */
    static public function extractItemFormats($layout_format)
    {
        preg_match_all('/<!--SHAPE(.*?)SHAPE-->/',
            $layout_format, $matched_items, PREG_SET_ORDER);

        $item_formats = array();

        foreach ($matched_items as $matched_item) {
            $item_format_json = $matched_item[1];
            $item_format = json_decode($item_format_json, true);

            if ($item_format['type'] === 's-list') {
                continue;
            }
            if ($item_format['type'] === Item\PageNumberItem::TYPE_NAME) {
                self::setPageNumberUniqueId($item_format);
            }

            $item_formats[$item_format['id']] = $item_format;
        }

        return $item_formats;
    }

    /**
     * @access private
     *
     * @param array $format
     */
    static public function cleanFormat(&$format)
    {
        $format['svg'] = preg_replace('/<!\-\-.*?\-\->/', '', $format['svg']);
        unset($format['state']);
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

    /**
     * @access private
     *
     * @param array $item_format
     */
    static public function setPageNumberUniqueId(array &$item_format)
    {
        if (empty($item_format['id'])) {
            $item_format['id'] = Item\PageNumberItem::generateUniqueId();
        }
    }

    private $format;
    private $item_foramts = array();
    private $identifier;

    /**
     * @param array $format
     * @param array $item_foramts
     */
    public function __construct(array $format, array $item_formats)
    {
        $this->format = $format;
        $this->item_formats = $item_formats;
        $this->identifier = md5($format['svg']);
    }

    /**
     * @access private
     *
     * @return string
     */
    public function getLastVersion()
    {
        return $this->format['version'];
    }

    /**
     * @access private
     *
     * @return string
     */
    public function getReportTitle()
    {
        return $this->format['config']['title'];
    }

    /**
     * @access private
     *
     * @return string
     */
    public function getPagePaperType()
    {
        return $this->format['config']['page']['paper-type'];
    }

    /**
     * @access private
     *
     * @return string[]|null
     */
    public function getPageSize()
    {
        if ($this->isUserPaperType()) {
            $page = $this->format['config']['page'];
            return array($page['width'], $page['height']);
        } else {
            return null;
        }
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isPortraitPage()
    {
        return $this->format['config']['page']['orientation'] === 'portrait';
    }

    /**
     * @access private
     *
     * @return boolean
     */
    public function isUserPaperType()
    {
        return $this->format['config']['page']['paper-type'] === 'user';
    }

    /**
     * @access private
     *
     * @return string
     */
    public function getSVG()
    {
        return $this->format['svg'];
    }

    /**
     * @access private
     *
     * @param string $id
     * @return boolean
     */
    public function hasItem($id)
    {
        return array_key_exists($id, $this->item_formats);
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
        if (!$this->hasItem($id)) {
            throw new Exception\StandardException('Item Not Found', $id);
        }

        $item_format = $this->item_formats[$id];

        switch ($item_format['type']) {
            case 's-tblock':
                return new Item\TextBlockItem($owner, $item_format);
                break;
            case 's-iblock':
                return new Item\ImageBlockItem($owner, $item_format);
                break;
            case 's-pageno';
                return new Item\PageNumberItem($owner, $item_format);
                break;
            default:
                return new Item\BasicItem($owner, $item_format);
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
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @access private
     *
     * @return array
     */
    public function getItemFormats()
    {
        return $this->item_formats;
    }
}
