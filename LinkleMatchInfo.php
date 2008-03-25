<?php
class LinkleMatchInfo{
	//private
	var $full_text;
	var $link_type;
	var $link_term;
	var $link_text;
	var $custom_properties_map = array();

	//static
	function get_match_string(){
		return '/\[ln\s+([\w-_]+)(\s+[\w-_]+\s*=\s*&#8221;.*&#8221;)*\s*\]([^\[]*)(\[text]([^\[]*)\[\/text\])?\[\/ln\]/';
	}

	//static
	function build_from_match($match){
		$ret = new LinkleMatchInfo();
		$ret->full_text = $match[0];
		$ret->link_type = $match[1];
		$ret->link_term = $match[3];
		$ret->link_text = $match[5];
		$ret->parse_custom_properties($match[2]);
		return $ret;
	}


	function parse_property($match){
		$this->custom_properties_map[$match[1]] = $match[2];
	}

	function parse_custom_properties($prop_string){
		return preg_replace_callback(
                "/(\w+)\s*=\s*&#8221;(.*)&#8221;/",
                array(&$this, "parse_property"),
                $prop_string);
	}

	function get_full_text(){
		return $this->full_text;
	}

	function get_link_type(){
		return $this->link_type;
	}
	function get_link_term(){
		return $this->link_term;
	}
	function get_link_text(){
		if($this->link_text)
			return $this->link_text;
		return $this->get_link_term();
	}

	function get_custom_properties_map(){
		return $this->custom_properties_map;
	}

}
?>
