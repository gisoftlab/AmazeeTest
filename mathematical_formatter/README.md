
# Installation
1.) Go to the /modules folder in your Drupal 8 project (normally DRUPAL_ROOT/modules)
2.) Clone this repository (or download a zip with the source)
3.) Enable the Drupal module by drush pm-enable mathematical_formatter or via the UI

# Example usage
1.) Admin/Structure/Content types/Basic page/ add field 'test' with type 'Text (plain)' 
2.) Admin/Structure/Content types/Basic page/Manage display
    - in 'test' change format to mathematical_formatter 
    

# Example routing with react displaying
/mathematical/compute

# PHP UNIT test
./vendor/bin/phpunit -c web/core web/modules/mathematical_formatter/tests/src/Functional/LexerTest.php
./vendor/bin/phpunit -c web/core web/modules/mathematical_formatter/tests/src/Functional/ParserTest.php

# Examples

``` php
<?php

 $lexer = new Lexer([' 2 +6 /3 -1 *1 ']);
 $lexer->run();
 $lexer->moveNext();
 $lexer->getToken();
 
 while ($lexer->moveNext()) {
     $lexer->getToken();
     $lexer->moveNext();
 }
        
```


``` php
<?php

$parser = new Parser([' 2 +6 /3 -1 *6 ']);
$val = $parser->compute();
        
```
