<?php

namespace Drupal\mathematical_formatter\Functional;

use Drupal\mathematical_formatter\Lexer\Lexer;
use Drupal\mathematical_formatter\Lexer\Parser;
use Drupal\mathematical_formatter\Lexer\Token;

use Drupal\Tests\UnitTestCase;

class ParserTest extends  UnitTestCase
{
    public function test_static_scan_algebra()
    {
        $parser = new Parser([' 2 +6 /3 -1 *6 ']);
        $parser->compute();

        $this->assertEquals(
            [ Lexer::T_NUMBER, Lexer::T_PLUS, Lexer::T_NUMBER, Lexer::T_DIV, Lexer::T_NUMBER, Lexer::T_MINUS, Lexer::T_NUMBER, Lexer::T_MUL, Lexer::T_NUMBER],
            array_map(function (Token $t) { return $t->getName(); }, $parser->getTokens())
        );

        $this->assertEquals(
            ['2', '+', '6', '/', '3', '-', '1', '*', '6'],
            array_map(function (Token $t) { return $t->getValue(); }, $parser->getTokens())
        );
    }

    public function test_validation_input()
    {
        try {
            $parser = new Parser([' 2 +6 /3 -1 *6= ']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals('Unable to parse line  2 +6 /3 -1 *6= .', $ex->getMessage());
        }

        try {
            $parser = new Parser([' 2 +6 /3 -1 *6! ']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals('Unable to parse line  2 +6 /3 -1 *6! .', $ex->getMessage());
        }

        try {
            $parser = new Parser([' #2 +6 /3 -1 *6 ']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals('Unable to parse line  #2 +6 /3 -1 *6 .', $ex->getMessage());
        }

        try {
            $parser = new Parser([' ,2 +6 /3 -1 *6 ']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals('Unable to parse line  ,2 +6 /3 -1 *6 .', $ex->getMessage());
        }

        try {
            $parser = new Parser([' .2 +6 /3 -1 *6 ']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals('Unable to parse line  .2 +6 /3 -1 *6 .', $ex->getMessage());
        }

        try {
            $parser = new Parser([' 2 +6 /3 -1 *6- ']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals("Incorrect input ' 2 +6 /3 -1 *6- '.", $ex->getMessage());
        }

        try {
            $parser = new Parser([' 2 +6 /3 -1 *']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals("Incorrect input ' 2 +6 /3 -1 *'.", $ex->getMessage());
        }

        try {
            $parser = new Parser([' +6 /3 -1 * 2']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals("Incorrect input ' +6 /3 -1 * 2'.", $ex->getMessage());
        }

        try {
            $parser = new Parser([' 6 /3 -1 * 2 2']);
            $parser->compute();
        }catch (\Exception $ex){
            $this->assertEquals("Incorrect input ' 6 /3 -1 * 2 2'.", $ex->getMessage());
        }
    }

    public function test_show_formula()
    {
        $parser = new Parser([' 2* 2 +12 /3 - 1 *6 /2 ']);
        $formula = $parser->showFormula();

        $this->assertEquals("2*2+12/3-1*6/2",$formula);
    }


    /**
     * @dataProvider provider
     */
    public function test_compute($source, $value)
    {
        $parser = new Parser([$source]);
        $compute = $parser->compute();
        $this->assertEquals($value, $compute);
    }

    public function provider()
    {
        return array(
            [' 2 +6 /3 -1 *6 ', -2],
            [' 2* 2 +12 /3 - 1 *6 ', 2],
            [' 2* 2 +12 /3 - 1 *6 /2 ', 5],
            [' 7* 2 +24 /3 - 1 *6 /2 +7 *2 - 6 /2 ', 2],
            [' 17* 2 +24 /3 - 1 *6 /2 +7 *2 - 6 /2 ', 22],
            [' 17* 2 +24 /3 - 1 *6 /2 +7 *2 - 6 /2 + 2 / 2', 21],
            [' 17* 2 +24 /3 - 1 *6 /2 +7 *2 - 6 /2 + 2 / 4', 21.5],
            [' 17* 2 +24 /3 - 1 *6 /2 +7 *2 - 6 /2 + 2 / 4 - 12 /6', 19.5],
        );
    }
}
