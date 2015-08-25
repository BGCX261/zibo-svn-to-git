<?php

namespace zibo\library\tcpdf;

use zibo\library\filesystem\File;
use zibo\library\i18n\I18n;

use zibo\ZiboException;

use \TCPDF;

require_once __DIR__ . '/config.php';

/**
 * Wrapper around the TCPDF library for better integration with Zibo
 */
class Pdf extends TCPDF {

    /**
     * Array with the mapping of the locales to the TCPDF languages
     * @var array
     */
    protected $locales = array(
        'de' => 'ger',
        'en' => 'eng',
        'fr' => 'fra',
        'nl' => 'nld',
    );

    /**
     * Constructs a new instance of the TCPDF library
     * It allows to set up the page format, the orientation and the measure unit used in all the methods (except for the font sizes).
     * @param string $orientation page orientation. Possible values are (case insensitive):<ul><li>P or Portrait (default)</li><li>L or Landscape</li><li>'' (empty string) for automatic orientation</li></ul>
     * @param string $unit User measure unit. Possible values are:<ul><li>pt: point</li><li>mm: millimeter (default)</li><li>cm: centimeter</li><li>in: inch</li></ul><br />A point equals 1/72 of inch, that is to say about 0.35 mm (an inch being 2.54 cm). This is a very common unit in typography; font sizes are expressed in that unit.
     * @param mixed $format The format used for pages. It can be either: one of the string values specified at getPageSizeFromFormat() or an array of parameters specified at setPageFormat().
     * @param boolean $unicode TRUE means that the input text is unicode (default = true)
     * @param boolean $diskcache if TRUE reduce the RAM memory usage by caching temporary data on filesystem (slower).
     * @param string $encoding charset encoding; default is UTF-8
     * @return null
     */
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);

        $this->setLocale();
    }

    /**
     * Saves the PDF on the filesystem
     * @param zibo\library\filesystem\File $file
     * @return null
     */
    public function save(File $file) {
        $this->Output($file->getPath(), 'F');
    }

    /**
     * This method is automatically called in case of fatal error; it simply outputs the message and halts the execution. An inherited class may override it to customize the error handling but should always halt the script, or the resulting document would probably be invalid.
     * @param $msg (string) The error message
     */
    public function Error($msg) {
        $this->_destroy(true);

        throw new ZiboException('PDF error: ' . $msg);
    }

    /**
     * Sets the locale of this PDF document
     * @param string $locale The locale to use for this document. If not provided, the current locale of Zibo will be used
     * @return null
     */
    public function setLocale($locale = null) {
        if (!$locale) {
            $locale = I18n::getInstance()->getLocale();
            $locale = $locale->getCode();
        }

        if (!array_key_exists($locale, $this->locales)) {
            throw new ZiboException('Could not set the locale for this PDF document: ' . $locale . ' is not mapped');
        }

        require_once(K_PATH_MAIN . 'config/lang/' . $this->locales[$locale] . '.php');

        global $l;

        $this->setLanguageArray($l);
    }

    /**
     * Writes a table in your document
     * @param array $header Array with the values for the table header
     * @param array $data Array with arrays for each row
     * @param array $width Array with the widths for each column (optional)
     * @return null
     */
    public function writeTable(array $header, array $data, array $width = null) {
        $lineWidth = $this->GetLineWidth();

        $this->SetDrawColor(159, 159, 159);
        $this->SetLineWidth(0.3);

        // count the headers and the widths
        $numHeaders = count($header);

        if (!$width) {
            $w = 180 / $numHeaders;
            for ($i = 0; $i < $numHeaders; $i++) {
                $width[] = $w;
            }
        }

        // writes the initial table header
        $this->writeTableHeader($header, $numHeaders, $width);

        $dimensions = $this->getPageDimensions();

        // writes the table data
        $fill = 0;
        foreach($data as $row) {
            // calculate the maximum height of the cells in the current row
            $maxLines = 0;
            for ($column = 0; $column < $numHeaders; $column++) {
                $data = $row[$column];
                if (!$data) {
                    $data = '';
                }

                $maxLines = max($maxLines, $this->getNumLines($data, $width[$column]));
            }

            $height = $maxLines * 6;

            // when the end of the page is reached, add a new page and rewrite the table header before writing the current row
            $currentY = $this->GetY();
            if ($currentY + $height + $dimensions['bm'] >= $dimensions['hk'] - 3) {
                $this->writeTableFooter($width);

                $this->addPage();

                $this->writeTableHeader($header, $numHeaders, $width);
            }

            // write the current row
            $cells = array();
            for ($column = 0; $column < $numHeaders; $column++) {
                $border = 'L';
                if ($column == ($numHeaders - 1)) {
                    $border .= 'R';
                }

                $this->MultiCell($width[$column], $height, $row[$column], $border, 'L', $fill, 0);
            }

            // go to the next line
            $this->Ln();

            // invert the zebra setting
            $fill = !$fill;
        }

        $this->writeTableFooter($width);

        $this->SetLineWidth($lineWidth);
    }

    /**
     * Writes the header of a table
     * @param array $header Array with the values for the table header
     * @param integer $numHeaders Number of values in the $header array
     * @param array $width Array with the widths for each column
     * @return null
     */
    private function writeTableHeader(array $header, $numHeaders, array $width) {
        $this->SetFillColor(223, 223, 223);
        $this->SetTextColor(32);
        $this->SetFont('', 'B');

        for ($column = 0; $column < $numHeaders; $column++) {
            $this->Cell($width[$column], 0, $header[$column], 1, 0, 'C', 1, '', 1);
        }

        $this->Ln();

        $this->SetFillColor(245, 245, 245);
        $this->SetTextColor(0);
        $this->SetFont('');
    }

    /**
     * Writes the footer of a table, just the bottom border
     * @param array $width Array with the widths for each column
     * @return null
     */
    private function writeTableFooter(array $width) {
        $this->Cell(array_sum($width), 0, '', 'T');
    }

}