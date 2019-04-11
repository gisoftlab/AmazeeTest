<?php

namespace Drupal\mathematical_formatter\Functional;

use Drupal\mathematical_formatter\Lexer\Lexer;
use Drupal\mathematical_formatter\Lexer\Token;

use Drupal\Tests\UnitTestCase;

class LexerTest extends  UnitTestCase
{
    public function test_static_scan_algebra()
    {
        $lexer = new Lexer([' 2 +6 /3 -1 *1 ']);
        $lexer->run();

        $this->assertEquals(
            [ Lexer::T_NUMBER, Lexer::T_PLUS, Lexer::T_NUMBER, Lexer::T_DIV, Lexer::T_NUMBER, Lexer::T_MINUS, Lexer::T_NUMBER, Lexer::T_MUL, Lexer::T_NUMBER],
            array_map(function (Token $t) { return $t->getName(); }, $lexer->getTokens())
        );
        $this->assertEquals(
            ['2', '+', '6', '/', '3', '-', '1', '*', '1'],
            array_map(function (Token $t) { return $t->getValue(); }, $lexer->getTokens())
        );
    }

    public function test_static_scan_algebra_unable_parse()
    {
        try {
            $lexer = new Lexer([' 2 +6 /3 -1 *1_']);
            $lexer->run();
        }catch (\Exception $ex){
            $this->assertEquals('Unable to parse line  2 +6 /3 -1 *1_.', $ex->getMessage());
        }

        try {
            $lexer = new Lexer([' 2 +6 /3 -1 *1!']);
            $lexer->run();
        }catch (\Exception $ex){

            $this->assertEquals('Unable to parse line  2 +6 /3 -1 *1!.', $ex->getMessage());
        }

        try {
            $lexer = new Lexer([' 2 +6 /3 -1 *1a']);
            $lexer->run();
        }catch (\Exception $ex){

            $this->assertEquals('Unable to parse line  2 +6 /3 -1 *1a.', $ex->getMessage());
        }

        try {
            $lexer = new Lexer([' 2 +6.']);
            $lexer->run();
        }catch (\Exception $ex){

            $this->assertEquals('Unable to parse line  2 +6..', $ex->getMessage());
        }

        try {
            $lexer = new Lexer([' 2 +6=']);
            $lexer->run();
        }catch (\Exception $ex){

            $this->assertEquals('Unable to parse line  2 +6=.', $ex->getMessage());
        }
    }

    public function test_move_next()
    {
        $lexer = new Lexer([' 2 +6 /3']);
        $lexer->run();

        $this->assertNull($lexer->getToken());

        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals('+', $lexer->getToken()->getValue());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals(Lexer::T_NUMBER, $lexer->getToken()->getName());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals(3, $lexer->getToken()->getPosition());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals(4, $lexer->getToken()->getPosition());
        $this->assertNull($lexer->moveNext());
        $this->assertNull($lexer->getToken());

        $lexer->reset();

        $this->assertEquals(0, $lexer->getPosition());
        $this->assertEquals(0, $lexer->getToken()->getPosition());
        $this->assertInstanceOf(Token::class, $lexer->getToken());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals(1, $lexer->getToken()->getPosition());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals(2, $lexer->getToken()->getPosition());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals(3, $lexer->getToken()->getPosition());
        $this->assertInstanceOf(Token::class, $lexer->moveNext());
        $this->assertEquals(4, $lexer->getToken()->getPosition());

        $this->assertNull($lexer->moveNext());
        $this->assertNull($lexer->getToken());

        $lexer->reset();

        $this->assertEquals(0, $lexer->getPosition());
        $this->assertInstanceOf(Token::class, $lexer->getToken());


    }

    public function test_loop_move_next()
    {
        $lexer = new Lexer([' 2 +6 /3']);
        $lexer->run();

        while ($lexer->moveNext()) {
            $this->assertInstanceOf(Token::class, $lexer->getToken());

            if($lexer->getToken()->getPosition() == 0){
                $this->assertEquals(2, $lexer->getToken()->getValue());
                $this->assertEquals(Lexer::T_NUMBER, $lexer->getToken()->getName());
            }

            if($lexer->getToken()->getPosition() == 1){
                $this->assertEquals('+', $lexer->getToken()->getValue());
                $this->assertEquals(Lexer::T_PLUS, $lexer->getToken()->getName());
            }

            if($lexer->getToken()->getPosition() == 2){
                $this->assertEquals(6, $lexer->getToken()->getValue());
                $this->assertEquals(Lexer::T_NUMBER, $lexer->getToken()->getName());
            }

            if($lexer->getToken()->getPosition() == 3){
                $this->assertEquals('/', $lexer->getToken()->getValue());
                $this->assertEquals(Lexer::T_DIV, $lexer->getToken()->getName());
            }

            if($lexer->getToken()->getPosition() == 4){
                $this->assertEquals(3, $lexer->getToken()->getValue());
                $this->assertEquals(Lexer::T_NUMBER, $lexer->getToken()->getName());
            }
        }
    }
}
