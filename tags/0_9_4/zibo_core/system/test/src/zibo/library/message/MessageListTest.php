<?php

namespace zibo\library\message;

use zibo\test\BaseTestCase;

class MessageListTest extends BaseTestCase {

    private $response;

    public function setUp() {
        $this->messageList = new MessageList();
    }

    public function testMergeAddsAllMessagesFromOtherMessageList() {
        $this->messageList->add(new Message('message', 'type'));

        $otherMessageList = new MessageList();
        $otherMessageList->add(new Message('somemessage', 'sometype'));
        $otherMessageList->add(new Message('someothermessage', 'someothertype'));

        $this->messageList->merge($otherMessageList);

        $this->assertEquals(3, count($this->messageList));

        $i = 0;
        foreach ($this->messageList as $message) {
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

    public function providerHasTypeReturnsIfListContainsAMessageOfASpecificType() {
        return array(
            array(new Message('somemessage', 'sometype'), 'sometype', true),
            array(new Message('somemessage', 'sometype'), 'someothertype', false),
        );
    }

    /**
     * @dataProvider providerHasTypeReturnsIfListContainsAMessageOfASpecificType
     * @param Message $message
     * @param string $typeToCheck
     * @param bool $expectedResult
     */
    public function testHasTypeReturnsIfListContainsAMessageOfASpecificType(Message $message, $typeToCheck, $expectedResult) {
        $this->messageList->add($message);

        $this->assertEquals($expectedResult, $this->messageList->hasType($typeToCheck));
    }

}