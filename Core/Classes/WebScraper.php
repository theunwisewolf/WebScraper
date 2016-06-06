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
	private $content = NULL;

	/** 
	 * @var 	string 		Contains the match of a regexp
	 */
	public $matches = NULL;

	/** 
	 * @return 	void
	 */
	public function __construct( $url, $quoteType=NULL )
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
		$this->content = @file_get_contents( $this->url );

		if( $this->content === FALSE )
		{
			throw new \Exception( 'Failed to fetch content from url: '.$this->url );
		}

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
	 * @param 	string 		 The url to use when scraping
	 */
	public function setUrl( $url )
	{
		$this->url = $url;
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

		$this->matches = array_filter( $matches );

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
}

?>