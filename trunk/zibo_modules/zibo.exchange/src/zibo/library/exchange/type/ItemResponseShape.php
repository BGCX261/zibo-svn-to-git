<?php

namespace zibo\library\exchange\type;

class ItemResponseShape {

    public $BaseShape;

    public $IncludeMimeContent;

    public $BodyType;

    public function __construct($baseShape, $includeMimeContent = null, $bodyType = null, $additionalProperties = null) {
        if (!DefaultShapeNames::isValidShape($baseShape)) {
            throw new InvalidArgumentException('Provided base shape is invalid');
        }

        if ($bodyType === null) {
            $bodyType = BodyType::TYPE_BEST;
        } elseif (!BodyType::isValidBodyType($bodyType)) {
            throw new InvalidArgumentException('Provided body type is invalid');
        }

        $this->BaseShape = $baseShape;
        $this->IncludeMimeContent = Boolean::getBoolean($includeMimeContent);
        $this->BodyType = $bodyType;
    }

}