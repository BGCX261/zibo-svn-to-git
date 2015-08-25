<?php

namespace zibo\library\message;

use zibo\test\BaseTestCase;

class MessageContainerTest extends BaseTestCase {

    private $response;

    public function setUp() {
        $this->messageContainer = new MessageContainer();
    }

    public function testMergeAddsAllMessagesFromOtherMessageContainer() {
        $this->messageContainer->add(new Message('message', 'type'));

        $otherMessageContainer = new MessageContainer();
        $otherMessageContainer->add(new Message('somemessage', 'sometype'));
        $otherMessageContainer->add(new Message('someothermessage', 'someothertype'));

        $this->messageContainer->merge($otherMessageContainer);

        $this->assertEquals(3, count($this->messageContainer));

        $i = 0;
        foreach ($this->messageContainer as $message) {
            switch($i) {
                case 0:
                    {
                        $this->assertEquals('message', $message->getMessage());
                        $this->assertEquals('type', $message->getType());
                    } break;
                case 1:
                    {
                        $this->assertEquals('somemessage', $message->getMessage());
                        $this->assertEquals('sometype', $message->getType());
                    } break;
                case 2:
                    {
                        $this->assertEquals('someothermessage', $message->getMessage());
                        $this->assertEquals('someothertype', $message->getType());
                    } break;
            }

            $i++;
        }
    }

    /**
     * @dataProvider providerHasTypeReturnsIfListContainsAMessageOfASpecificType
     * @param Message $message
     * @param string $typeToCheck
     * @param bool $expectedResult
     */
    public function testHasTypeReturnsIfListContainsAMessageOfASpecificType(Message $message, $typeToCheck, $expectedResult) {
        $this->messageContainer->add($message);

        $this->assertEquals($expectedResult, $this->messageContainer->hasType($typeToCheck));
    }

    public function providerHasTypeReturnsIfListContainsAMessageOfASpecificType() {
        return array(
            array(new Message('somemessage', 'sometype'), 'sometype', true),
            array(new Message('somemessage', 'sometype'), 'someothertype', false),
        );
    }

}