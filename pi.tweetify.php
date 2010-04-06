<?php

/*
=====================================================

RogEE "Tweetify"
a plug-in for ExpressionEngine 2
by Michael Rog
v2.3

inspired by (and pretty much directly ripped from) the Javascript "ify" version
by Dustin Diaz
>> http://www.dustindiaz.com/basement/ify.html

also uses John Gruber's URL-matching regex
>> http://daringfireball.net/2009/11/liberal_regex_for_matching_urls

email Michael with questions, feedback, suggestions, bugs, etc.
>> michael@michaelrog.com

=====================================================

*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
						'pi_name'			=> 'RogEE Tweetify',
						'pi_version'		=> '2.3',
						'pi_author'			=> 'Michael Rog',
						'pi_author_url'		=> 'http://michaelrog.com/go/ee',
						'pi_description'	=> 'Formats @shoutouts, #hashtags, and URLs as links, a la Twitter.',
						'pi_usage'			=> Tweetify::usage()
					);

class Tweetify {

var $return_data = "";

  function Tweetify($str = '')
  {

    $this->EE =& get_instance();
    
    if ($str == '')
    {
      $str = $this->EE->TMPL->tagdata;
    }
    
    $this->return_data = $this->hash($this->at($this->url($str)));

  }


 function url($str_url = '')
  {
  	
  	if ($str_url == '')
    {
      $str_url = $this->EE->TMPL->tagdata;
    }
  	
  	$in=array(
        '`(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))`si'
        );
        
	$out=array(
        '<a href="$1" rel=\"nofollow\">$1</a>'
        );
        
    $links = preg_replace('`(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))`si', '<a href="$1" rel=\"nofollow\">$1</a>', $str_url);    
	
	return preg_replace('[href=\"www.]','href="http://www.',$links) ;
	// return preg_replace($in,$out,$str_url);

  }
  
  
 function at($str_user = '')
  {
  
	if ($str_user == '')
    {
      $str_user = $this->EE->TMPL->tagdata;
    }
	
	return preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $str_user);

  }

 function hash($str_tag = '')
  {
  
	if ($str_tag == '')
    {
      $str_tag = $this->EE->TMPL->tagdata;
    }

	return preg_replace("#(^|[\n ])\#([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://twitter.com/search?q=%23\\2\" >#\\2</a>'", $str_tag);

  }


					
	/** ----------------------------------------
	/**  Plugin Usage
	/** ----------------------------------------*/
	function usage()
	{
	ob_start(); 
	?>

	1. Format @shoutouts, #hash-tags, and URLs as links by enclosing text in the {exp:tweetify} tags...
	
	BEFORE:
	
	{exp:tweetify} @michaelrog check this out: http://www.zombo.com/ #lolz {/exp:tweetify}
	
	AFTER:
	
	<a href="http://twitter.com/michaelrog">@michaelrog</a> check this out: <a href="http://www.zombo.com/">http://www.zombo.com/</a> <a href="http://search.twitter.com/search?q=%23lolz">#lolz</a>


	2. Or, use the individual functions:
	
	{exp:tweetify:url} ... {/exp:tweetify:url}
	{exp:tweetify:at} ... {/exp:tweetify:at}
	{exp:tweetify:hash} ... {/exp:tweetify:hash}


	3. Tweetify can be selected as a text formatting option for channel fields.
	

	<?php
	$buffer = ob_get_contents();
	
	ob_end_clean(); 

	return $buffer;
	}

} // END Tweetify class

/* End of file pi.tweetify.php */ 
/* Location: ./system/expressionengine/third_party/tweetify/pi.tweetify.php */