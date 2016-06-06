<?php

//define( 'DEBUG', TRUE );
header("Content-Type: text/html; charset=utf-8");
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'WebScraper.php'; 

$matches = WebScraper::i( 'http://www.less-real.com/quotes/search/Hikigaya%20Hachiman?p={ITERATOR_VAR}' )
		->iterateOverPages( 1, 5 )
		->convertToAscii()
		->getContentInsideTag( 'span', ['class'=>'quoteText'] )
		->writeToFile( '*', TRUE, PHP_EOL.PHP_EOL, 1 )
		->getMatches()[1];

$str = implode( PHP_EOL, $matches );
echo $str;
echo mb_detect_encoding($str);
//echo mb_convert_encoding( $str, 'UTF-8' );
?>