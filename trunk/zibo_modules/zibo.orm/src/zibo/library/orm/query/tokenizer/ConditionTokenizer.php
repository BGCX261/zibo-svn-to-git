<?php

namespace zibo\library\orm\query\tokenizer;

use zibo\library\database\manipulation\condition\Condition;
use zibo\library\orm\exception\OrmException;
use zibo\library\orm\query\tokenizer\symbol\ConditionSymbol;
use zibo\library\orm\query\tokenizer\symbol\FunctionSymbol;
use zibo\library\orm\query\tokenizer\symbol\MathematicalSymbol;
use zibo\library\tokenizer\symbol\SimpleSymbol;
use zibo\library\tokenizer\Tokenizer;

/**
 * Tokenizer for the conditions of a model query
 */
class ConditionTokenizer extends Tokenizer {

    /**
     * Constructs a new condition tokenizer
     * @return null
     */
    public function __construct() {
        $this->addSymbol(new SimpleSymbol(Condition::OPERATOR_AND));
        $this->addSymbol(new SimpleSymbol(Condition::OPERATOR_OR));
        $this->addSymbol(new ConditionSymbol($this));
        parent::setWillTrimTokens(true);
    }

    /**
     * Sets whether this tokenizer will trim the resulting tokens. Tokens which are empty after trimming
     * will be removed. Nested tokens are untouched.
     * @param boolean $willTrimTokens True to trim the tokens, false otherwise
     * @return null
     * @throws zibo\library\orm\exception\OrmException when this method is called, this tokenizer always trims resulting tokens.
     */
    public function setWillTrimTokens($willTrimTokens) {
        throw new OrmException('Not allowed to set the trim tokens property for this tokenizer');
    }

}