# WebScraper
A small library written in PHP to extract specific content from Websites.

# Basic Usage
You can use this library to extract from just one page, or you can use it to iterate over multiple pages by specifying a ITERATOR_VAR that will be replaced in each iteration to the next value.

#### Content from a single page
```php
$matches = WebScraper::i( 'http://www.less-real.com/quotes/search/Hikigaya%20Hachiman?p=2' )
		->fetchContent()
		->convertToAscii()
		->getContentInsideTag( 'span', ['class'=>'quoteText'] )
		->writeToFile( '*', TRUE, PHP_EOL.PHP_EOL, 1 )
		->getMatches()[1];
```

#### Content from Multiple pages
```php
$matches = WebScraper::i( 'http://google.com/search?q=idk#page={ITERATOR_VAR}' )
		->iterateOverPages( 1, 5 )
		->convertToAscii()
		->getContentInsideTag( 'span', ['class'=>'quoteText'] )
		->writeToFile( '*', TRUE, PHP_EOL.PHP_EOL, 1 )
		->getMatches()[1];
```

# Functions
#### fetchContent()
Returns all the content of the webpage supplied. If you use this after iterateOverPages(), it will contain the content of all the pages, concatenated together.

```php
echo WebScraper::i( 'http://google.com/' )
    ->fetchContent()
    ->getContent();
```

#### setUrl($url)
If you want to set the url manually, use this.

```php
WebScraper::i()->setUrl( 'http://google.com' );
```

#### getContent()
Returns the content of the web page.

#### getLinks()
Returns all the links in the web page.

```php
$matches = WebScraper::i( 'http://google.com/idk' )
        ->fetchContent()
		->convertToAscii()
		->getLinks()
		->getMatches()[1];
```

#### getImages()
Returns all the images in the web page.

```php
$matches = WebScraper::i( 'http://google.com/idk' )
        ->fetchContent()
		->convertToAscii()
		->getImages()
		->getMatches();
```

#### getScripts()
Returns all the scripts (&lt;script&rt;&lt;/script&rt;) in the page.

```php
$matches = WebScraper::i( 'http://google.com/idk' )
        ->fetchContent()
		->convertToAscii()
		->getScripts()
		->getMatches();
```

#### getInlineScripts()
Returns all the inline scripts in the page.

```php
$matches = WebScraper::i( 'http://google.com/idk' )
        ->fetchContent()
		->convertToAscii()
		->getInlineScripts()
		->getMatches();
```

#### getStylesheets()
Returns all the stylesheets in the page.

```php
$matches = WebScraper::i( 'http://google.com/idk' )
        ->fetchContent()
		->convertToAscii()
		->getStylesheets()
		->getMatches();
```

#### printMatches( $printPre = TRUE )
Prints the content of $this->matches.
$printPre variable is used to tell the library to print the contents of $this->matches inside the html tag &lt;pre&rt;&lt;/pre&rt; or not. By default, it does output the content inside &lt;pre&rt; tag.

#### getMatches()
Return $this->matches array

#### getAttributes( $tags )
Extracts the attributes of the tags supplied.
$tags is the array of tags from which attributes are to be extracted.

```php
$matches = WebScraper::i( 'http://www.google.com/' )
		->fetchContent()
		->convertToAscii()
		->getLinks()
		->getAttributes( WebScrapper::i()->getMatches()[0] )
		->getMatches()[1];
```

#### getAttributesWithKey( $tags, $key )
Same as getAttributes() except that you can extract specific attributes using this.
For example, href from links.

```php
$matches = WebScraper::i( 'http://www.google.com/' )
		->fetchContent()
		->convertToAscii()
		->getLinks()
		->getAttributesWithKey( WebScrapper::i()->getMatches()[0], 'href' )
		->getMatches()[1];
```

#### getAttributesWithValue( $tags, $value )
Same as getAttributes() except that you can extract attributes having a specific value using this.
For example, links having an attribute value "#modal"

```php
$matches = WebScraper::i( 'http://www.google.com/' )
		->fetchContent()
		->convertToAscii()
		->getLinks()
		->getAttributesWithValue( WebScrapper::i()->getMatches()[0], '#modal' )
		->getMatches()[1];
```

#### getContentInsideTag( $tag, $attributes )
Allows you to get content inside a specific tag, with specific attribute key-value pairs.

```php
$matches = WebScraper::i( 'http://www.google.com/' )
		->fetchContent()
		->convertToAscii()
		->getContentInsideTag( 'span', ['class'=>'someClassValue'] )
		->getMatches()[1];
```
#### convertToAscii( $inputEncoding=NULL )
Converts the $this->content to ASCII encoding. I wrote this because most web pages seem to use a variety of non-ascii characters, that I don't like. I have used this in every example above. But its upto you.

$inputEncoding var allows you to supply an input encoding. If not supplied, defaults to UTF-8

#### iterateOverPages( $start, $end, $function = NULL )
It is used when you want to iterate over multiple pages or in simple english, get content from multiple pages between a range.

$start is used to specify the starting page.
$end points to the ending page.
$function is a custom function that you can use when the pages are not integers, and contain string, for example http://google.com/page=somestring-1
So in this case you might want to write a custom function that returns the page number prefixed with "somestring-"

{ITERATOR_VAR} will be replaced with the value of the page. If you did not supply a function, it will default to integer values, starting from $start to $end. Otherwise, your function's return values will be used in each iteration to shift over to pages.
```php
$matches = WebScraper::i( 'http://google.com/?p={ITERATOR_VAR}' )
		->iterateOverPages( 1, 5 )
		->convertToAscii()
		->getContentInsideTag( 'span', ['class'=>'someClass'] )
		->writeToFile( '*', TRUE, PHP_EOL.PHP_EOL, 1 )
		->getMatches()[1];
```

#### writeToFile( $filename='*', $lineNumbers=FALSE, $delimiter=PHP_EOL, $defaultArray=0 )
Writes the $this->matcches array to a file.

$filename, ofcourse is the path to the file in which you want to save the content. Defaults to ./scrapedContent.txt

$lineNumbers puts line numbers before printing each match in the file. Defaults to false.

$delimiter is use to separate the matches as they are written in the file. Defaults to PHP_EOL or the Environment Endline character.

$defaultArray is the index of the array inside $this->matches that you want to write. Because, it will contain the matched content as well as the Groups captured by the regex. Defaults to 0.
