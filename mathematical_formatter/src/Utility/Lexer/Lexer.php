<?php
namespace Drupal\mathematical_formatter\Utility\Lexer;

use Exception;

/**
 * Class Lexer
 * @package Drupal\mathematical_formatter\Utility\Lexer
 */
class Lexer {

    const T_WHITESPACE = 'T_WHITESPACE';
    const T_NUMBER = 'T_NUMBER';
    const T_PLUS = 'T_PLUS';
    const T_MINUS = 'T_MINUS';
    const T_MUL = 'T_MUL';
    const T_DIV = 'T_DIV' ;

    static $_operators = [
        self::T_MUL => 0,
        self::T_DIV => 1,
        self::T_PLUS => 2,
        self::T_MINUS => 3
    ];

    static $_default_config = [
        '/^\s/i' => self::T_WHITESPACE,
        '/^\d+/i' => self::T_NUMBER,
        '/^\+/i' => self::T_PLUS,
        '/^-/i' => self::T_MINUS,
        '/^\*/i' => self::T_MUL,
        '|^/|i' => self::T_DIV,
    ];

    /** @var array */
    private $source;

    /** @var string */
    private $config;

    /** @var Token[] */
    private $tokens = [];

    /** @var int */
    private $position = -1;

    /** @var Token */
    private $token;

    /**
     * Lexer constructor.
     * @param null $source
     * @param null $config
     */
    public function __construct($source = null , $config = null)
    {
        $this->source = $source;
        $this->config = ($config)?$config:self::$_default_config;
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param $index
     * @return mixed|string
     */
    public function getSourceLine($index)
    {
        return (isset($this->source[$index-1]))?$this->source[$index-1]:'';
    }

    /**
     * @param string $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param string $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return Token[]
     */
    public function getTokens()
    {
        $this->removeEmptyspaces($this->tokens);
        return $this->tokens;
    }

    /**
     * @param Token $tokens
     */
    public function setTokens(Token $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @param int $index
     */
    public function removeToken($index)
    {
        if(isset($this->tokens[$index])){
            unset($this->tokens[$index]);
        }
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
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param Token $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param $name
     * @return bool
     */
    public static function hasOperator($name)
    {
        return (isset(static::$_operators[$name]))?true:false;
    }

    /**
     * @return array
     */
    public static function getOperators()
    {
        return static::$_operators;
    }

    /**
     * run
     *
     * @param null|string $source
     * @return Token[]
     * @throws Exception
     */
    public function run($source = null) {
        $this->source = ($source)?$source:$this->source;

        foreach($this->source as $number => $line) {
            $offset = 0;
            $position = 0;

            while($offset < strlen($line)) {
                ++$position;
                $result = $this->_match($line, $number, $offset, $position);

                if($result === false) {
                    throw new Exception("Unable to parse line " . $line. ".");
                }

                $this->tokens[] = $result;
                $offset += strlen($result->getValue());
            }
        }

        return $this->getTokens();
    }

    /**
     * _match
     *
     * @param $line
     * @param $number
     * @param $offset
     * @param $position
     * @return bool|Token
     */
    protected function _match($line, $number, $offset, $position) {
        $string = substr($line, $offset);

        foreach($this->config as $pattern => $name) {
            if(preg_match($pattern, $string, $matches)) {
                $number++;
                if(isset($matches[0])) {
                    return new Token($name, $matches[0], $number, $offset, $position);
                }
            }
        }

        return false;
    }

    /**
     * @param Token[] $tokens
     */
    private function removeEmptyspaces(&$tokens) {
        foreach ($tokens as $key => $token) {
            if($token->getName() == Lexer::T_WHITESPACE){
                unset($tokens[$key]);
            }
        }

        self::resetTokenPositions($tokens);

    }

    public function reset()
    {
        $position = 0;
        $this->token = (isset($this->tokens[$position]))
            ? $this->tokens[$position]
            : null;
        $this->position = $position;
    }

    /**
     * @return Token|null
     */
    public function moveNext()
    {
        $this->position++;
        $this->token = (isset($this->tokens[$this->position]))
            ? $this->tokens[$this->position]
            : null;

        return $this->token;
    }

    /**
     * @param Token[] $tokens
     */
    public static function resetTokenPositions(&$tokens) {
        $tokensTemp = [];
        $i = -1;
        foreach ($tokens as $key => $token) {
            $i++;
            $token->setPosition($i);
            $tokensTemp[$i] = $token;
        }

        $tokens = $tokensTemp;
    }
}