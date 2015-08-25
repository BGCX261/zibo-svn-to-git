<?php

namespace zibo\library\tcpdf\view;

use zibo\core\View;

use zibo\library\tcpdf\Pdf;

/**
 * View for a PDF document of the TCPDF library
 */
class PdfView implements View {

    /**
     * Inline mode for this view
     * @var string
     */
    const MODE_INLINE = 'I';

    /**
     * Download mode for this view
     * @var string
     */
    const MODE_DOWNLOAD = 'D';

    /**
     * The PDF to generate in the view
     * @var zibo\library\tcpdf\TcPdf
     */
    protected $pdf;

    /**
     * The name of the download file
     * @var string
     */
    protected $name;

    /**
     * Constructs a new PDF view
     * @param zibo\library\tcpdf\TcPdf $pdf The pdf to render
     * @param string $name The name of the download file
     * @return null
     */
    public function __construct(Pdf $pdf, $name = null, $mode = null) {
        if (!$name) {
            $name = 'document.pdf';
        }

        if (!$mode) {
            $mode = self::MODE_INLINE;
        }

        $this->pdf = $pdf;
        $this->name = $name;
        $this->mode = $mode;
    }

    /**
     * Render the view
     * @param boolean $return true to return the rendered view, false to send it to the client
     * @return mixed null when provided $return is set to true; the rendered output when the provided $return is set to false
     */
    public function render($return = true) {
        $this->pdf->Output($this->name, $this->mode);
    }

}