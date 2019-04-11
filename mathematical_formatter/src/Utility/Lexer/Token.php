<?php

namespace Drupal\mathematical_formatter\Utility\Lexer;

/**
 * Class Token
 * @package Drupal\mathematical_formatter\Utility\Lexer
 */
class Token
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $value;

    /** @var int */
    protected $offset;

    /** @var int */
    protected $position;

    /** @var int */
    protected $line;

    /**
     * Token constructor.
     * @param $name
     * @param $value
     * @param $line
     * @param $offset
     * @param $count
     */
    public function __construct($name, $value, $line, $offset, $count)
    {
        if($name == Lexer::T_NUMBER){
            $this->value = (int)$value;
        }else{
            $this->value = $value;
        }

        $this->name = $name;
        $this->offset = $offset;
        $this->position = $count;
        $this->line = $line;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param int $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

    public function is($token)
    {
        if ($token instanceof self) {
            return $this->name === $token->getName();
        } elseif (is_string($token)) {
            return $this->name === $token;
        } else {
            throw new \InvalidArgumentException('Expected string or Token');
        }
    }
}
