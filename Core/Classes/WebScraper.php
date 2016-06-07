<?php 
/**
 * Web Scraper Core Class
 * 
 * @author 		AmN
 */

class WebScraper 
{
	public static $instance = NULL;

	/** 
	 * @return 		Object 		Returns instance of current class
	 */
	public static function i()
	{
		if( static::$instance === NULL )
		{
			$class = new ReflectionClass( get_called_class() );
			static::$instance = $class->newInstanceArgs( func_get_args() );
		}

		return static::$instance;
	}	

	/** 
	 * @var 	string 		The address of the webpage
	 */
	public $url = NULL;

	/** 
	 * @var 	string 		The HTML Content
	 */
	private $content = '';

	/** 
	 * @var 	string 		Contains the match of a regexp
	 */
	public $matches = NULL;

	/** 
	 * @var 	string 		Contains the matched attributes
	 */
	public $attributes = NULL;

	/** 
	 * @var 	string 		Flag for iteration
	 */
	private $iteration = FALSE;

	/** 
	 * @return 	void
	 */
	public function __construct( $url=NULL, $quoteType=NULL )
	{
		if( defined( 'DEBUG' ) )
		{
			$url = './Debug/index.html';

			if(!file_exists($url))
			{
				echo 'File does not exist.';
			}	
		}

		$this->url = $url;

		// But why am I even defining it? idk... too sleepy to change.
		if( !defined( 'QUOTE_TYPE' ) AND empty( $quoteType ) )
		{
			define( 'QUOTE_TYPE', '[\"\\\']' );
		}
		else if( !defined( 'QUOTE_TYPE' ) AND !empty( $quoteType ) )
		{
			define( 'QUOTE_TYPE', $quoteType );
		}
	}

	/** 
	 * Fetches content
	 */
	public function fetchContent( $url=NULL )
	{
		if( $url != NULL )
			$content = @file_get_contents( $url );
		else
			$content = @file_get_contents( $this->url );

		if( $content === FALSE )
		{
			echo ( 'Failed to fetch content from url: '. ( $url === NULL ) ? $this->url : $url );
		}

		$this->content .= $content;

		return static::$instance;
	}

	/** 
	 * @param 	string 		 The url to use when scraping
	 */
	public function setUrl( $url )
	{
		$this->url = $url;

		return static::$instance;
	}

	/** 
	 * @return 	string  	 The original web page content
	 */
	public function getContent()
	{
		return $this->content;	
	}

	/** 
	 * Gets all links in the page
	 */
	public function getLinks()
	{
		if( empty( $this->content ) )
		{
			return FALSE;
		}

		preg_match_all( '/<a\s*?(((\w+='.QUOTE_TYPE.'(.*?)'.QUOTE_TYPE.')\s*)*)>(.*?)<\/a>/is', $this->content, $matches );

		$this->matches = $this->arrayFilter( $matches );

		return static::$instance;
	}

	/** 
	 * Gets all images in page
	 */
	public function getImages()
	{
		if( empty( $this->content ) )
		{
			return FALSE;
		}

		preg_match_all( '/<img\s*?(((\w+='.QUOTE_TYPE.'(.*?)'.QUOTE_TYPE.')\s*)*)\/?>/is', $this->content, $matches );

		$this->matches = $this->arrayFilter( $matches );

		return static::$instance;
	}

	/** 
	 * Gets all scripts in page
	 */
	public function getScripts()
	{
		if( empty( $this->content ) )
		{
			return FALSE;
		}

		preg_match_all( '/<script\s*?(((\w+='.QUOTE_TYPE.'(.*?)'.QUOTE_TYPE.')\s*)*)>(.*?)<\/script>/is', $this->content, $matches );

		$this->matches = $this->arrayFilter( $matches );

		return static::$instance;
	}

	/** 
	 * Gets all inline scripts in page
	 */
	public function getInlineScripts()
	{
		if( empty( $this->content ) )
		{
			return FALSE;
		}

		//preg_match_all( '/<script\s*?(((\w+='.QUOTE_TYPE.'(.*?)'.QUOTE_TYPE.')\s*)*)>(.+?)<\/script>/is', $this->content, $matches );
		preg_match_all( '/<script(\s+type='.QUOTE_TYPE.'text\/javascript'.QUOTE_TYPE.')?>(.+?)<\/script>/is', $this->content, $matches );

		$this->matches = $this->arrayFilter( $matches );

		return static::$instance;
	}

	/** 
	 * Gets all stylesheets in page
	 */
	public function getStylesheets()
	{
		if( empty( $this->content ) )
		{
			return FALSE;
		}

		preg_match_all( '/<link\s*?(((\w+='.QUOTE_TYPE.'(.*?)'.QUOTE_TYPE.')\s*)*)\/?>/is', $this->content, $matches );

		$this->matches = $this->arrayFilter( $matches );

		return static::$instance;
	}

	/** 
	 * Outputs the property
	 */
	public function printMatches( $printPre = TRUE )
	{
		if( is_array( $this->matches ) )
		{
			if( $printPre === TRUE )
				echo '<pre>';
			print_r( $this->matches );
			if( $printPre === TRUE )
				echo '</pre>';
		}
		else
		{
			print "Matches empty!";
		}

		return static::$instance;
	}

	/** 
	 * Get matches
	 */
	public function getMatches()
	{
		return $this->matches;
	}

	/** 
	 * Extracts attributes from a tag
	 */
	public function getAttributes( $tags )
	{
		$i = 0;
		foreach( $tags AS $tag )
		{
			preg_match_all( '/\w+='.QUOTE_TYPE.'(.*?)'.QUOTE_TYPE.'/', $tag, $matches );

			foreach( $matches[0] AS $match )
			{
				$bits = explode( '=', $match );
				$this->attributes[$i][$bits[0]] = $bits[1];
			}

			$i++;
		}

		return $this->attributes;
	}

	/**
	 * Get attribute with key
	 */
	public function getAttributesWithKey( $tags, $key )
	{
		$key = preg_quote( $key );
		$i = 0;
		foreach( $tags AS $tag )
		{
			preg_match_all( '/'. $key .'='.QUOTE_TYPE.'(.*?)'.QUOTE_TYPE.'/', $tag, $matches );

			foreach( $matches[0] AS $match )
			{
				$bits = explode( '=', $match );
				$this->attributes[$i][$bits[0]] = $bits[1];
			}

			$i++;
		}

		return $this->attributes;
	}

	/**
	 * Get attribute with value
	 */
	public function getAttributesWithValue( $tags, $value )
	{
		$value = preg_quote( $value );
		$i = 0;
		foreach( $tags AS $tag )
		{
			preg_match_all( '/\w+='.QUOTE_TYPE.$value.QUOTE_TYPE.'/', $tag, $matches );

			foreach( $matches[0] AS $match )
			{
				$bits = explode( '=', $match );
				$this->attributes[$i][$bits[0]] = $bits[1];
			}

			$i++;
		}

		return $this->attributes;
	}

	/** 
	 * Gets content inside a specific tag
	 *
	 * @param 	string
	 * @param 	array 		key-pair values for attribute-value
	 */
	public function getContentInsideTag( $tag, $attributes )
	{
		if( empty( $tag ) )
		{
			return [];
		}

		$tag = preg_quote( $tag );
		$regex = '';

		if( !empty( $attributes ) )
		{
			foreach( $attributes AS $key => $value )
			{
				if( $value != '*' )
				{
					$value = preg_quote( $value );
				}
				else
				{
					$value = '(.*?)';
				}

				$key = preg_quote( $key );

				$regex .= '\s+'.$key.'='.QUOTE_TYPE.$value.QUOTE_TYPE;
			}
		}

		$regex = '/<'.$tag.$regex.'>(.*?)<\/'.$tag.'>/is';

		preg_match_all( $regex, $this->content, $matches );
		
		$this->matches = $matches;
		return static::$instance;
	}

	/** 
	 * For converting utf-8 horrible quotes to ascii quotes
	 */
	public function convertToAscii( $inputEncoding=NULL )
	{
		$inputEncoding = ( $inputEncoding === NULL ) ? 'UTF-8' : $inputEncoding; 
 		$this->content = iconv( $inputEncoding, 'ASCII//TRANSLIT', $this->content );

 		return static::$instance;
	}

	/** 
	 * For iterating over pages
	 */
	public function iterateOverPages( $start, $end, $function = NULL )
	{
		$this->iteration = TRUE;

		// Initialize the iteration var
		$i = ( $function === NULL ) ? $start : call_user_func( $function );

		while( $i != $end )
		{
			$this->fetchContent( str_replace( '{ITERATOR_VAR}', $i, $this->url ) );
			$i = ( $function === NULL ) ? $i+1 : call_user_func( $function );
		}

		// Fetch one last time because the while will end at the last position, so it won't be matched
		$this->fetchContent( str_replace( '{ITERATOR_VAR}', $i, $this->url ) );

		return static::$instance;
	}

	/** 
	 * Write the matched content to a file
	 * Be default it writes $matches[0] to file. But this can be changed via the $defaultArray parameter
	 */
	public function writeToFile( $filename='*', $lineNumbers=FALSE, $delimiter=PHP_EOL, $defaultArray=0 )
	{
		if( empty( $this->matches ) )
		{
			return static::$instance;
		}

		$content = '';
		if( $lineNumbers === FALSE )
		{
			$content = implode( $delimiter, $this->matches[ $defaultArray ] );
		}
		else
		{
			$i = 0;
			foreach( $this->matches[ $defaultArray ] AS $match )
			{
				$content .= ++$i . '. ' . $match . $delimiter;
			}
		}

		if( $filename == '*' )
		{
			$filename = './scrapedContent.txt';
		}

		file_put_contents( $filename, $content );

		return static::$instance;
	}

	/** 
	 * Recursive Array Filter
	 */
	public function arrayFilter( $array )
	{
	    foreach( $array AS &$value ) 
	    { 
			if( is_array( $value ) ) 
			{ 
				$value = $this->arrayFilter( $value ); 
			} 
	    } 

	    return array_filter( $array ); 
	}
}

?>