<?php

namespace zibo\library\excel;

use zibo\library\filesystem\File;

use zibo\test\BaseTestCase;

class WorkbookTest extends BaseTestCase {

    public function testWriteWorkbook() {
    	$file = new File('/tmp/test.xls');
        $workbook = new Workbook();
        $worksheet = $workbook->addWorksheet('My first worksheet');

        $worksheet->write(0, 0, 'Name');
        $worksheet->write(0, 1, 'Age');
        $worksheet->write(1, 0, 'John Smith');
        $worksheet->write(1, 1, 30);
        $worksheet->write(2, 0, 'Johann Schmidt');
        $worksheet->write(2, 1, 31);
        $worksheet->write(3, 0, 'Juan Herrera');
        $worksheet->write(3, 1, 32);

        $workbook->write($file);

        $exists = $file->exists();

        $file->delete();

        $this->assertTrue($exists, 'file was not saved');
    }

}