<?php

/*
 * This file is part of the Thinreports PHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Thinreports\Generator\PDF;

/**
 * @access private
 */
class Graphics
{
    static private $pdf_image_align = array(
        'left'   => 'L',
        'center' => 'C',
        'right'  => 'R'
    );

    static private $pdf_image_valign = array(
        'top'    => 'T',
        'middle' => 'M',
        'bottom' => 'B'
    );

    /**
     * @var \TCPDF
     */
    private $pdf;

    /**
     * @var string[]
     */
    private $image_registry = array();

    /**
     * @param \TCPDF $pdf
     */
    public function __construct(\TCPDF $pdf)
    {
        $this->pdf = $pdf;
    }

    /**
     * @param float|string $x1
     * @param float|string $y1
     * @param float|string $x2
     * @param float|string $y2
     * @param array $attrs {
     *      @option string|null "stroke_width" required
     *      @option string|null "stroke_color" required
     *      @option string "stroke_dash" required
     * }
     * @see http://www.tcpdf.org/doc/code/classTCPDF.html
     */
    public function drawLine($x1, $y1, $x2, $y2, array $attrs = array())
    {
        $style = $this->buildGraphicStyles($attrs);

        $this->pdf->Line($x1, $y1, $x2, $y2, $style['stroke']);
    }

    /**
     * @param float|string $x
     * @param float|string $y
     * @param float|string $width
     * @param float|string $height
     * @param array $attrs {
     *      @option string|null "stroke_width" required
     *      @option string|null "stroke_color" required
     *      @option string "stroke_dash" required
     *      @option string "fill" required
     *      @option float|string "radius" required
     * }
     * @see http://www.tcpdf.org/doc/code/classTCPDF.html
     */
    public function drawRect($x, $y, $width, $height, array $attrs = array())
    {
        $style = $this->buildGraphicStyles($attrs);

        if (empty($attrs['radius'])) {
            $this->pdf->Rect($x, $y, $width, $height,
                null, array('all' => $style['stroke']), $style['fill']);
        } else {
            $this->pdf->RoundedRect($x, $y, $width, $height,
                $attrs['radius'], '1111', null, $style['stroke'], $style['fill']);
        }
    }

    /**
     * @param float|string $cx
     * @param float|string $cy
     * @param float|string $rx
     * @param float|string $ry
     * @param array $attrs {
     *      @option string|null "stroke_width" required
     *      @option string|null "stroke_color" required
     *      @option string "stroke_dash" required
     *      @option string "fill" required
     * }
     * @see http://www.tcpdf.org/doc/code/classTCPDF.html
     */
    public function drawEllipse($cx, $cy, $rx, $ry, array $attrs = array())
    {
        $style = $this->buildGraphicStyles($attrs);

        $this->pdf->Ellipse($cx, $cy, $rx, $ry,
            0, 0, 360, null, $style['stroke'], $style['fill']);
    }

    /**
     * @param string $filename
     * @param float|string $x
     * @param float|string $y
     * @param float|string $width
     * @param float|string $height
     * @param array $attrs {
     *      @option string "align" optional default is "left"
     *      @option string "valign" optional default is "top"
     * }
     * @see http://www.tcpdf.org/doc/code/classTCPDF.html
     */
    public function drawImage($filename, $x, $y, $width, $height, array $attrs = array())
    {
        $position = $this->buildImagePosition($attrs);

        $this->pdf->Image(
            $filename,  // image file
            $x,         // x
            $y,         // y
            $width,     // box width
            $height,    // box height
            null,       // type
            null,       // link
            null,       // align
            true,       // resize
            300,        // dpi
            null,       // palign
            false,      // ismask
            false,      // imgmask
            0,          // border
            $position   // fitbox
        );
    }

    /**
     * @param string $base64_string
     * @param float|string $x
     * @param float|string $y
     * @param float|string $width
     * @param float|string $height
     * @param array $attrs {@see self::drawImage()}
     */
    public function drawBase64Image($base64_string, $x, $y, $width, $height, array $attrs = array())
    {
        $registry_key = md5($base64_string);
        $image_path = $this->getRegisteredImagePath($registry_key);

        if ($image_path === null) {
            $image_path = tempnam(sys_get_temp_dir(), 'thinreports');
            $this->image_registry[$registry_key] = $image_path;
            file_put_contents($image_path, base64_decode($base64_string));
        }

        $this->drawImage($image_path, $x, $y, $width, $height, $attrs);
    }

    public function clearRegisteredImages()
    {
        foreach ($this->image_registry as $image_path) {
            unlink($image_path);
        }
    }

    /**
     * @param array $attrs
     * @return array {@example array("stroke" => array("attr" => "value"), "fill" => "fill_color"))
     */
    public function buildGraphicStyles(array $attrs)
    {
        if (empty($attrs['stroke_width'])) {
            $stroke_style = null;
        } else {
            $stroke_color = ColorParser::parse($attrs['stroke_color']);

            if ($attrs['stroke_dash'] === 'none') {
                $stroke_dash = 0;
            } else {
                $stroke_dash = $attrs['stroke_dash'];
            }

            $stroke_style = array(
                'width' => $attrs['stroke_width'],
                'color' => $stroke_color,
                'dash'  => $stroke_dash
            );
        }

        if (array_key_exists('fill_color', $attrs) && $attrs['fill_color'] !== 'none') {
            $fill_color = ColorParser::parse($attrs['fill_color']);
        } else {
            $fill_color = array();
        }

        return array('stroke' => $stroke_style, 'fill' => $fill_color);
    }

    /**
     * @param string $registry_key
     * @return string|null
     */
    public function getRegisteredImagePath($registry_key)
    {
        if (array_key_exists($registry_key, $this->image_registry)) {
            return $this->image_registry[$registry_key];
        } else {
            return null;
        }
    }

    /**
     * @param array $attrs
     * @return string
     */
    public function buildImagePosition(array $attrs)
    {
        $align  = array_key_exists('align', $attrs)  ? $attrs['align']  : 'left';
        $valign = array_key_exists('valign', $attrs) ? $attrs['valign'] : 'top';

        return self::$pdf_image_align[$align] . self::$pdf_image_valign[$valign];
    }
}
