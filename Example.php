<?php
define( 'DEBUG', TRUE );
header("Content-Type: text/html; charset=utf-8");
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'WebScraper.php'; 

$matches = WebScraper::i()
		->fetchContent()
		->convertToAscii()
		->getImages()
		//->getContentInsideTag( 'span', ['class'=>'quoteText'] )
		//->writeToFile( '*', TRUE, PHP_EOL.PHP_EOL, 1 )
		->getMatches();

print_r( $matches );

?>