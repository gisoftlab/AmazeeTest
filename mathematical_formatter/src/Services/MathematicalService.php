<?php
namespace Drupal\mathematical_formatter\Services;

use Drupal\mathematical_formatter\Utility\Lexer\Parser;

/**
 * CowService is a simple exampe of a Drupal 8 service.
 *
 * $our_service = \Drupal::service('mathematical_service.compute');
 * $our_service-> parse(array('2 + 3 * 2 - 6 / 2'));
 */
class MathematicalService {


    /**
     * MathematicalService constructor.
     */
    public function __construct() {}

    /**
     * @param array $input
     * @return string|null
     */
    public function parse(Array $input) {
        try{
            $parser = new Parser($input);
            return $parser->compute();

        }catch (\Exception $ex){
            return $ex->getMessage();
        }
    }
}