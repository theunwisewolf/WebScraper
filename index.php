<?php

define( 'DEBUG', TRUE );

require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Classes' . DIRECTORY_SEPARATOR . 'WebScraper.php'; 

$matches = WebScraper::i( 'http://www.less-real.com/quotes/search/Hikigaya%20Hachiman' )->getLinks()->getMatches();
print_r( WebScraper::i()->getAttributesWithKey( $matches[0], 'href' ) );
?>