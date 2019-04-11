<?php


namespace Drupal\mathematical_formatter\Utility\Lexer;

use Exception;

/**
 * Class Parser
 * @package Drupal\mathematical_formatter\Utility\Lexer
 */
class Parser {

    /** @var Lexer */
    private $lexer;

    /** @var Token[] */
    private $tokens;

    /**
     * Parser constructor.
     * @param null $source
     * @param null $config
     * @throws Exception
     */
    public function __construct($source = null , $config = null)
    {
        $this->lexer = new Lexer($source, $config);
        $this->lexer->run();
    }

    /**
     * @return Lexer
     */
    public function getLexer()
    {
        return $this->lexer;
    }

    /**
     * @return string
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @param $tokens
     */
    private function setTokens($tokens)
    {
        foreach ($tokens as $index => $token) {
            $this->tokens[$index] = clone $token;
        }
    }

    /**
     * @return null
     * @throws Exception
     */
    public function compute()
    {
        $tokens = $this->getLexer()->getTokens();
        $this->setTokens($this->getLexer()->getTokens());

        $this->validate($tokens);
        $this->computeByOperators($tokens);

        return (isset($tokens[0]) ? $tokens[0]->getValue() : null);
    }

    /**
     * @return string
     */
    public function showFormula()
    {
        $formula = [];
        while ($this->getLexer()->moveNext()) {
            $formula[] =  $this->getLexer()->getToken()->getValue();
        }
        return implode("",$formula);
    }

    /**
     * @param Token[] $tokens
     * @throws Exception
     */
    private function computeByOperators(&$tokens)
    {
        $operators = array_flip (Lexer::getOperators());
        ksort($operators);
        foreach ($operators as $name) {
            foreach ($tokens as $token) {
                $pos = $token->getPosition();

                if($token->getName() == $name){
                    if($name == Lexer::T_MUL) {
                        $tokens[$pos - 1] = $this->createToken($tokens[$pos - 1],(float)$tokens[$pos - 1]->getValue() * (float)$tokens[$pos + 1]->getValue());
                    }

                    if($name == Lexer::T_DIV) {
                        $tokens[$pos - 1] = $this->createToken($tokens[$pos - 1],(float)$tokens[$pos - 1]->getValue() / (float)$tokens[$pos + 1]->getValue());
                    }

                    if($name == Lexer::T_PLUS) {
                        $tokens[$pos - 1] = $this->createToken($tokens[$pos - 1],(float)$tokens[$pos - 1]->getValue() + (float)$tokens[$pos + 1]->getValue());
                    }

                    if($name == Lexer::T_MINUS) {
                        $tokens[$pos - 1] = $this->createToken($tokens[$pos - 1],(float)$tokens[$pos - 1]->getValue() - (float)$tokens[$pos + 1]->getValue());
                    }

                    //var_dump($pos);
                    unset($tokens[$pos]);
                    unset($tokens[$pos+1]);

                    Lexer::resetTokenPositions($tokens);
                }
            }
        }
    }

    /**
     * @param Token $token
     * @param $value
     * @return Token
     */
    private function createToken(Token $token, $value)
    {
        $token->setValue($value);
        return $token;
    }

    /**
     * @param Token[] $tokens
     * @throws Exception
     */
    private function validate($tokens)
    {
        foreach($tokens as $index => $token) {
            if(Lexer::hasOperator($token->getName())){
                if (isset($tokens[$token->getPosition() - 1])) {
                    if ($tokens[$token->getPosition() - 1]->getName() != Lexer::T_NUMBER) {
                        throw new Exception("Incorrect input '".$this->getLexer()->getSourceLine($token->getLine())."'.");
                    }
                } else {
                    throw new Exception("Incorrect input '".$this->getLexer()->getSourceLine($token->getLine())."'.");
                }

                if (isset($tokens[$token->getPosition() + 1])) {
                    if ($tokens[$token->getPosition() + 1]->getName() != Lexer::T_NUMBER) {
                        throw new Exception("Incorrect input '".$this->getLexer()->getSourceLine($token->getLine())."'.");
                    }
                } else {
                    throw new Exception("Incorrect input '".$this->getLexer()->getSourceLine($token->getLine())."'.");
                }
            }
            elseif($token->getName() == Lexer::T_NUMBER) {
                if(isset($tokens[$token->getPosition() - 1])) {
                    if ($tokens[$token->getPosition() - 1]->getName() == Lexer::T_NUMBER) {
                        throw new Exception(
                            "Incorrect input '".$this->getLexer()->getSourceLine($token->getLine())."'."
                        );
                    }
                }
                elseif(isset($tokens[$token->getPosition() + 1])) {
                    if ($tokens[$token->getPosition() + 1]->getName() == Lexer::T_NUMBER) {
                        throw new Exception(
                            "Incorrect input '".$this->getLexer()->getSourceLine($token->getLine())."'."
                        );
                    }
                }
            }
        }
    }
}