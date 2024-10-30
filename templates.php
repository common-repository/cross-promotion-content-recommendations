<?php


	$engageya_templates = array();
	
	class Engageya_Template
	{
		protected $name;
		protected $is_excerpt;
		protected $bad_version;
		
		function __construct($name, $is_excerpt, $bad_version = -1)
		{
			$this->name = $name;
			$this->is_excerpt = $is_excerpt;
			$this->bad_version = $bad_version;
		}
		
		public function showRecommendations($content)
		{
			global $wp_version;
			$is_excerpt = $this->is_excerpt;
			
			if(strcmp(wp_get_theme(), $this->name) == 0 && !$is_excerpt($content) && ($this->bad_version == -1 || strcmp($wp_version, $this->bad_version) != 0))
				return true;
			return false;
		}
	}
	
	function engageya_add_all_templates()
	{
		
		global $engageya_templates;
		
		$engageya_templates = array();
		
		array_push($engageya_templates, new Engageya_Template('WordPress Default', 'engageya_wordpress_default', '2.0'));
		array_push($engageya_templates, new Engageya_Template('Twenty Ten', 'engageya_twenty_ten'));
		array_push($engageya_templates, new Engageya_Template('Twenty Eleven', 'engageya_twenty_eleven'));
		array_push($engageya_templates, new Engageya_Template('Suffusion', 'engageya_suffusion'));
		array_push($engageya_templates, new Engageya_Template('PageLines', 'engageya_page_lines'));
		
		//and many more...
		
	}
	
	function engageya_show_recommendations($content)
	{
		global $engageya_templates;
		for($i = 0; $i < sizeof($engageya_templates); $i++)
		{
			if($engageya_templates[$i]->showRecommendations($content))
				return true;
		}
		return false;
	}
	
	////////////////////////////////////////////////////////////////////////////////////////
	///////////////// Templates is_excerpt functions ////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	
	function engageya_wordpress_default($content)
	{
		return preg_match("/<a href=\".*?\"[^>]* class=\"more-link\">/i", $content);
	}
	
	function engageya_twenty_ten($content)
	{
		return preg_match("/<a href=\".*?\"[^>]* class=\"more-link\">/i", $content);
	}
	
	function engageya_twenty_eleven($content)
	{
		return preg_match("/<a href=\".*?\"[^>]* class=\"more-link\">/i", $content);
	}
	
	function engageya_suffusion($content)
	{
		return preg_match("/<a href=\".*?\"[^>]* class=\"more-link\">/i", $content);
	}
	
	function engageya_page_lines($content)
	{
		return true; //in the PageLines template, all posts in homepage are excerpts
	}
?>