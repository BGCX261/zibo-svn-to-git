<?php

namespace zibo\library\soap\curl;

use zibo\test\BaseTestCase;

class ClientTest extends BaseTestCase {

   public function testSpellChecker() {
        try {
            $client = new Client('http://ws.cdyne.com/SpellChecker/check.asmx?WSDL', array('trace' => TRUE));
            $param = new \stdClass();
            $param->BodyText = 'I dont like SOAP';
            $result = $client->CheckTextBodyV2($param);
        } catch (SoapFault $e) {
            $this->fail('Soap fault: ' . $e->getMessage());
        }

        $this->assertObjectHasAttribute('DocumentSummary', $result);
        $this->assertObjectHasAttribute('MisspelledWord', $result->DocumentSummary);
        $this->assertObjectHasAttribute('word', $result->DocumentSummary->MisspelledWord);
        $this->assertType(\PHPUnit_Framework_Constraint_IsType::TYPE_STRING, $result->DocumentSummary->MisspelledWord->word);
        $this->assertEquals('dont', $result->DocumentSummary->MisspelledWord->word);

        $this->assertObjectHasAttribute('Suggestions', $result->DocumentSummary->MisspelledWord);
        $this->assertType(\PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $result->DocumentSummary->MisspelledWord->Suggestions);
        $this->assertContains("don't", $result->DocumentSummary->MisspelledWord->Suggestions);
    }
}
