<?php

namespace zibo\library\excel\view;

use zibo\library\excel\format\FormatFactory;
use zibo\library\excel\Workbook;
use zibo\library\excel\Worksheet;
use zibo\library\html\table\export\AbstractExportView;

use zibo\ZiboException;

/**
 * Table Excel export view
 */
class ExcelExportView extends AbstractExportView {

    /**
     * Default title for worksheet and filename
     * @var string
     */
    const DEFAULT_TITLE = 'Export';

    /**
     * Renders the excel, this will will always offer a file download
     * @param boolean $return Not implemented
     * @return null
     */
    public function render($return = true) {
        $workbook = new Workbook();
        $formatFactory = new FormatFactory();
        $rowNumber = 1;

        if ($this->title) {
            $titleFormat = $formatFactory->createTitleFormat();

            $filename = $this->title . '.xls';
            $worksheet = $workbook->addWorksheet(substr($this->title, 0, 31));
            $worksheet->write($rowNumber, 0, $this->title, $titleFormat);

            $rowNumber += 2;
        } else {
            $filename = self::DEFAULT_TITLE . '.xls';
            $worksheet = $workbook->addWorksheet(self::DEFAULT_TITLE);
        }

        $subTitleFormat = $formatFactory->createSubtitleFormat();
        $subTitleFormat->setBackgroundColor('#E3E3E3');
        foreach ($this->headers as $header) {
            $cells = $header->getCells();
            $colNumber = 0;
            foreach ($cells as $cell) {
                $worksheet->write($rowNumber, $colNumber++, $cell->getValue(), $subTitleFormat);
            }
            $rowNumber++;
        }

        $defaultFormat = $formatFactory->createDefaultFormat();
        $evenFormat = clone $defaultFormat;
        $evenFormat->setBackgroundColor('#F3F3F3');
        $groupFormat = $formatFactory->createBoldFormat();
        $groupFormat->setBackgroundColor('#EBEBEB');

        $zebraIndex = 0;
        foreach ($this->rows as $index => $row) {
            $format = $defaultFormat;
            if (in_array($index, $this->groupRows)) {
                $format = $groupFormat;
            } else {
                if ($zebraIndex) {
                    $format = $evenFormat;
                    $zebraIndex = 0;
                } else {
                    $zebraIndex = 1;
                }
            }

            $cells = $row->getCells();
            $colNumber = 0;
            foreach ($cells as $cell) {
                try {
                    $worksheet->write($rowNumber, $colNumber++, strip_tags($cell->getValue()), $format);
                } catch (ZiboException $e) {
                    $worksheet->write($rowNumber, $colNumber, '###', $format);
                }
            }
            $rowNumber++;
        }

        $worksheet->calculateColumnWidths();

        $this->processWorksheet($worksheet);

        $workbook->send($filename);
    }

    /**
     * Hook for some extra processing on the worksheet
     * @param zibo\library\excel\Worksheet $worksheet
     * @return null
     */
    protected function processWorksheet(Worksheet $worksheet) {

    }

}