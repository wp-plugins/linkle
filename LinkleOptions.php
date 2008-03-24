<?php
class LinkleLinkInfo{
	//private
	var $description;
	var $code;
	var $func = NULL;

	//public
	function get_description(){
		return $this->description;
	}

	//public
	function get_code(){
		return $this->code;
	}

	//public static
	function build_from_storage($storage_string){
		$ret = new LinkleLinkInfo();
		$ret->load_from_storage_string($storage_string);
		return $ret;
	}

	//public static
	function build($description, $code){
		$ret = new LinkleLinkInfo();
		$ret->description = $description;
		$ret->code = $code;
		return $ret;
	}

	//private
	function load_from_storage_string($string){
		$array = unserialize($string);
		$this->description = $array[0];
		$this->code = $array[1];
	}

	function get_storage_string(){
		return serialize(array($this->description, $this->code));
	}

	function execute($everything, $term, $link_text){
		if($this->func == NULL)
			$this->func = create_function('$wrapper, $term, $text', $this->code);
		$func = $this->func;
		return $func($everything, $term, $link_text);
	}
}

class LinkleOptions{
	//private
	var $type_to_info_map = array();

	//public static
	function install(){
		$map = array();
		$wikipedia = LinkleLinkInfo::build("Link to specified wikipedia topic.",
			'return \'<a href="http://www.wikipedia.org/search-redirect.php?language=en&amp;go=go&search=\'.urlencode($term).\'">\'.$text.\'</a>\';');
		$map["wikipedia"] = $wikipedia->get_storage_string();
		$amazon = LinkleLinkInfo::build("Link to amazon search result.",
			'return \'<a href="http://www.amazon.com/s/?selected=search-alias=aps&amp;field-keywords=\'.urlencode($term).\'">\'.$text.\'</a>\';');
		$map["amazon"] = $amazon->get_storage_string();
		$php = LinkleLinkInfo::build("Link to php.org documentation for specified term.",
			'return \'<a href="http://www.php.net/search.php?show=quickref&amp;pattern=\'.urlencode($term).\'">\'.$text.\'</a>\';');
		$map["php"] = $php->get_storage_string();
		$gravatar = LinkleLinkInfo::build("Generate a gravatar image.",
			'$hash = md5($term);'.
			'return \'<img src="http://www.gravatar.com/avatar.php?gravatar_id=\'.$hash.\'"/>\';');
		$map["gravatar"] = $gravatar->get_storage_string();
		$twitter_rss = LinkleLinkInfo::build("Link to a twitter rss feed.",
			'return \'<a href="http://twitter.com/statuses/user_timeline.rss?id=\'.urlencode($term).\'">\'.$text.\'</a>\';');
		$map["twitter-rss"] = $twitter_rss->get_storage_string();
		add_option("linkle_handler_map", serialize($map), "serialized map of type to code for linkle");
	}

	//public static
	function uninstall(){
		delete_option("linkle_handler_map");
	}

	//public static
	function build_from_request(){
		$ret = new LinkleOptions();
		$ret->load_from_request();
		return $ret;
	}

	//public static
	function build_from_options(){
		$ret = new LinkleOptions();
		$ret->load_from_options();
		return $ret;
	}

	//private
	function load_from_options(){
		if(get_option("linkle_handler_map")){
			$map = unserialize(get_option("linkle_handler_map"));
			foreach(array_keys($map) as $key){
				$this->type_to_info_map[$key] = LinkleLinkInfo::build_from_storage($map[$key]);
			}
		}
	}

	//private
	function load_from_request(){
		$clean_request = $_REQUEST;
		if(get_magic_quotes_gpc())
			$clean_request = array_map(stripslashes, $_REQUEST);
		for($i=0; $i < $clean_request['linkle_handler_count']; $i++){
			if($clean_request['linkle_handler_'.$i.'_type']){
				$type = $clean_request['linkle_handler_'.$i.'_type'];
				$description = $clean_request['linkle_handler_'.$i.'_description'];
				$code = $clean_request['linkle_handler_'.$i.'_code'];
				if(strcmp($type,"") != 0){
					$this->type_to_info_map[$type] = LinkleLinkInfo::build($description, $code);
				}
			}
		}
	}

	//public store current option map to the wordpress database
	function store(){
		$map = array();
		foreach(array_keys($this->type_to_info_map) as $key){
			$map[$key] = $this->type_to_info_map[$key]->get_storage_string();
		}
		update_option("linkle_handler_map", serialize($map));
	}

	//public process a tag from a wordpress document
	function process_tag($match){
		$everything = $match[0];
		$link_type = $match[1];
		$term = $match[2];
		$link_text = $match[4];
		if(strcmp($link_text, "") == 0){
			$link_text = $term;
		}

		if(array_key_exists($link_type, $this->type_to_info_map)){
			return $this->type_to_info_map[$link_type]->execute($everything, $term, $link_text);
		}
		return $everything;
	}

	//public iterate over the type->code map call $func with each key, code, count triple
	function each($func){
		$count = 0;
		$keys = array_keys($this->type_to_info_map);
		sort($keys, SORT_STRING);
		foreach($keys as $key){
			$func($key, $this->type_to_info_map[$key], $count);
			$count++;
		}
	}

	//total number of types in the map
	function count(){
		return sizeof(array_keys($this->type_to_info_map));
	}
}
?>
