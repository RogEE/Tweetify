<?php

/*
=====================================================

RogEE "Tweetify"
a plug-in for ExpressionEngine 2
by Michael Rog
v3.2

Inspired by Javascript "ify" by Dustin Diaz:
>> http://www.dustindiaz.com/basement/ify.html

Uses John Gruber's URL-matching regex:
>> http://daringfireball.net/2009/11/liberal_regex_for_matching_urls

Please e-mail me with questions, feedback, suggestions, bugs, etc.
>> michael@michaelrog.com
>> http://rog.ee

This plugin is compatible with NSM Addon Updater:
>> http://github.com/newism/nsm.addon_updater.ee_addon

Changelog:
0.1 - alpha
2.0 - the release (for EE2)
2.1 - made the file more readable
2.2 - enabled Tweetify as a text-processor in weblogs
2.3 - using John Gruber's URL-matching regex (fixed a lot of URL corner cases)
2.4 - added CSS class parameters
2.5 - added support for NSM Addon Updater (http://github.com/newism/nsm.addon_updater.ee_addon)
2.6 - class attribute only added to code if class parameters are provided
2.7 - bug fix: got rid of rogue slashes in rel=nofollow
2.8 - better (more Twitter-like) regex for @ and # links
2.9 - updated documentation and doc links
3.0 - updated hash tag search URL to use search.twitter.com instead of twitter.com
3.1 - eliminated leading slash when @handle is preceded by quotes
3.2 - updated hashtag regex to ignore HTML entity codes

=====================================================

*/

if (! defined('BASEPATH') && ! defined('EXT')) exit('No direct script access allowed');

$plugin_info = array(
						'pi_name'			=> 'RogEE Tweetify',
						'pi_version'		=> '3.2.0',
						'pi_author'			=> 'Michael Rog',
						'pi_author_url'		=> 'http://rog.ee',
						'pi_description'	=> 'Formats @shoutouts, #hashtags, and URLs as links, a la Twitter.',
						'pi_usage'			=> Tweetify::usage()
					);

/** ----------------------------------------
/**  Tweetify class
/** ----------------------------------------*/

class Tweetify {

	var $return_data = "";

	/** ----------------------------------------
	/**  Constructor
	/** ----------------------------------------*/

	function Tweetify($str = '')
	{

		$this->EE =& get_instance();
    
		if ($str == '')
		{
			$str = $this->EE->TMPL->tagdata;
		}
		
		$this->css = $this->EE->TMPL->fetch_param('class');    

		$this->return_data = $this->hash($this->at($this->url($str)));

	} // END Tweetify() constructor

	/** ----------------------------------------
	/**  URI auto-linker
	/** ----------------------------------------*/

	function url($str_url = '')
	{
  	
		if ($str_url == '')
		{
			$str_url = $this->EE->TMPL->tagdata;
		}
		
		$urlclass = $this->EE->TMPL->fetch_param('urlclass');
		
		switch (TRUE) {
			case (!empty($this->css) && !empty($urlclass)) :
				$classString = trim($this->css)." ".trim($urlclass);
				break;
			case (!empty($urlclass)) :
				$classString = trim($urlclass);
				break;
			case (!empty($this->css)) :
				$classString = trim($this->css);
				break;
			default:
				$classString = "" ;
				break ;        
		}
		
		$links = preg_replace('`(([\w-]+://?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))`si', '<a href="$1"'.(empty($classString)?"":' class="'.$classString.'"').' rel="nofollow">$1</a>', $str_url);    
		
		return preg_replace('[href=\"www.]','href="http://www.',$links);

	} // END url()
  
	/** ----------------------------------------
	/**  @ At links	
	/** ----------------------------------------*/
  
	function at($str_user = '')
	{

		if ($str_user == '')
		{
			$str_user = $this->EE->TMPL->tagdata;
		}
	
		$atclass = $this->EE->TMPL->fetch_param('atclass');
		
		switch (TRUE) {
			case (!empty($this->css) && !empty($atclass)) :
				$classString = trim($this->css)." ".trim($atclass);
				break;
			case (!empty($atclass)) :
				$classString = trim($atclass);
				break;
			case (!empty($this->css)) :
				$classString = trim($this->css);
				break;        
			default:
				$classString = "" ;
				break ; 
		}
		
		return stripslashes(preg_replace("#(^|\W)@(\w{1,20})#ise", "'\\1@<a href=\"http://www.twitter.com/\\2\"".(empty($classString)?"":' class="'.$classString.'"').">\\2</a>'", $str_user));

	} // END at()

	/** ----------------------------------------
	/**  # Hash links
	/** ----------------------------------------*/

	function hash($str_tag = '')
	{

		if ($str_tag == '')
		{
			$str_tag = $this->EE->TMPL->tagdata;
		}

		$hashclass = $this->EE->TMPL->fetch_param('hashclass');

		switch (TRUE) {
			case (!empty($this->css) && !empty($hashclass)) :
				$classString = trim($this->css)." ".trim($hashclass);
				break;
			case (!empty($hashclass)) :
				$classString = trim($hashclass);
				break;
			case (!empty($this->css)) :
				$classString = trim($this->css);
				break;        
			default:
				$classString = "" ;
				break ; 
		}

		return preg_replace("#(^|[\W])(?<!\&)\#(\w+)#ise", "'\\1<a href=\"http://search.twitter.com/search?q=%23\\2\"".(empty($classString)?"":' class="'.$classString.'"').">#\\2</a>'", $str_tag);

	} // END hash()

					
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
		
		4. You can style the links generated by Tweetify using the parameters: class, urlclass, atclass, hashclass.
		
		For example:
		
		{exp:tweetify:url class="css"}, or
		{exp:tweetify urlclass="css"}, or
		{exp:tweetify class="css" urlclass="cssU" atclass="cssA" hashclass="cssH1"}
		
		You can even supply multiple class names:
		
		{exp:tweetify urlclass="css css1 css2"}
		
		5. This plugin is compatible with NSM Addon Updater:
		
		http://github.com/newism/nsm.addon_updater.ee_addon
	
		<?php
		$buffer = ob_get_contents();
		
		ob_end_clean(); 
	
		return $buffer;
		
	} // END usage()

} // END Tweetify class

/* End of file pi.tweetify.php */ 
/* Location: ./system/expressionengine/third_party/tweetify/pi.tweetify.php */