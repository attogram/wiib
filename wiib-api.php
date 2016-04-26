<?php
// WIIB
// API

class wiib_api extends wiib_user {

	var $commons_api_url;
	var $commons_response;

	//////////////////////////////////////////////////////////
	function __construct() {
                parent::__construct();
		//$this->debug('wiib_api:__construct()');
		$this->commons_api_url = 'https://commons.wikimedia.org/w/api.php';
		ini_set('user_agent','WiiB - Which image is better? - <URL HERE> ');
	}

	//////////////////////////////////////////////////////////
	function get_api_images_by_search($search, $limit='50') {
		if( !$limit || !$this->is_positive_number($limit) ) { return false; }
		if( !$search || $search=='' ) { return false; } 

		$call = $this->commons_api_url . '?action=query&format=json'
		. '&list=search'
		. '&srnamespace=6|14' // 6 = File   14 = Category
		. '&srprop=' // score|titlesnippet|hasrelated' 
		//. '&srinfo=' 
		. '&srlimit=' . $limit // max 50
		. '&srsearch=' . urlencode($search)
		;

        $r = $this->call_commons($call, $key='search');
		$totalhits = $this->commons_response['query']['searchinfo']['totalhits'];
		$this->error("totalhits=$totalhits");

		$file = array();
		$category = array();
		while( list(,$x) = each($this->commons_response['query']['search']) ) { 
			switch( $x['ns'] ) { 
				case '6': $file[] = $x['title']; break;
				case '14': $category[] = $x['title']; break;
			} 
		} 

		reset($category);
		while( list(,$x) = each($category) ) {
			$ic = $this->insert_category($x);
		} 	

		$this->error('Categories: ' . print_r($category,1));
		$this->error('Files: ' . print_r($file,1));
		$ir = $this->get_api_image(false, implode('|',$file) );

		$this->error('IMPORT DONE.   <a href="/image/list/?port=' . $this->portfolio 
			. '&s=last_seen&o=DESC&n=50">List new images</a>');
		exit;
	}

	//////////////////////////////////////////////////////////
	function get_api_image($pageids='',$titles='') {
		$this->debug("wiib_api:get_api_image($pageids)");
		//if( !$pageids || !$this->is_number($pageids) ) {  // needs to allow  123|456|789  format
		if( !$pageids && !$titles) { 
			$this->debug('wiib_api:get_api_image: Error: missing pageids or titles');
			return false; 
		} 
		if( $pageids && $titles) { 
			$this->debug('wiib_api:get_api_image: Error: pageids AND titles');
			return false; 
		} 
	
		$call = $this->commons_api_url . '?action=query&format=json&iilimit=500'
                . '&prop=imageinfo'
                . '&iiprop=user|url|size|mime|sha1|timestamp'
                . '&iiurlwidth=300'
		;
		if( $pageids ){ $call .= '&pageids=' . $pageids; } 
		if( $titles ){ $call .= '&titles=' . urlencode($titles); } 

                $r = $this->call_commons($call, $key='pages');
		if( !$r ) { 
			$this->error('wiib_api:get_api_image: ERROR call');
			return false;
		} 
                $this->save_images_to_database($r);
                return $r;
	}

	//////////////////////////////////////////////////////////
	function get_images_from_category($category='') { 
		$this->debug("wiib_api:get_images_from_category($category)");
		$category = trim($category);
		if( !$category ) { return false; } 
		$category = ucfirst($category);
		if ( !preg_match('/^[Category:]/i', $category)) { 
			$category = 'Category:' . $category; 
		} 


		$call = $this->commons_api_url . '?action=query&format=json&cmlimit=50'
		. '&list=categorymembers'
		. '&cmtype=file|subcat'
		. '&cmprop=ids|title|type'
		. '&cmtitle=' . urlencode($category);
		$d = $this->call_commons($call, $key='categorymembers');
		if( !$d ) { 
			$this->error('wiib_api:get_images_from_category: ERROR: call');
			return false;
		} 

		//$this->error($call);
		//$this->error('commons_response: '. print_r($this->commons_response,1));

		$files = $categories = array();
		reset($this->commons_response);
		while( list(,$x) = each( $this->commons_response['query']['categorymembers']  ) ) { 
			switch( $x['ns'] ) { 
				case '6': $files[$x['title']] = $x['pageid']; break;
				case '14': $categories[] = $x['title']; @$this->insert_category($x['title']); break;
			} 
		} 

		$this->error('categories: ' . print_r($categories,1));
		$this->error('files: ' . print_r($files,1));
	

		@$this->insert_category($category);

		if( !empty($files) ) { 
			$pageids = implode('|',$files);
			$call = $this->commons_api_url . '?action=query&format=json&iilimit=500'
			. '&prop=imageinfo'
			. '&iiprop=user|url|size|mime|sha1|timestamp'
			. '&iiurlwidth=300'
			. '&iilimit=500'
			. '&pageids=' . $pageids;
			$r = $this->call_commons($call, $key='pages');
			$this->save_images_to_database($r);
		} 


		$this->error('IMPORT DONE.   <a href="/image/list/?port=' . $this->portfolio 
			. '&s=last_seen&o=DESC&n=50">List new images</a>');
		 exit;
		return $r;
	} 

	//////////////////////////////////////////////////////////
	function get_random_images_from_api($limit=2) {
		$this->debug("wiib_api:get_random_images_from_api($limit)");
		$call = $this->commons_api_url . '?action=query&format=json'
		. '&generator=random'
		. '&grnlimit=' . $limit
		. '&grnnamespace=6'
		. '&prop=imageinfo'
		. '&iiprop=url|size|mime|user|sha1|timestamp'  // |comment|parsedcomment'
		. '&iiurlwidth=300';
		$r = $this->call_commons($call, $key='pages');
		$this->save_images_to_database($r);
		return $r;
	}

	//////////////////////////////////////////////////////////
	function call_commons($url, $key='') {
		$this->debug("wiib_api:call_commons: url=$url key=$key)");
		if( !$url ) { return FALSE; } 
		if( !$key ) { return FALSE; } 
		$x = @file_get_contents($url);
                if( $x === FALSE ) {
                        $this->error('wiib_api:call_commons: ERROR: get failed');
                        return FALSE;
                }
                $d = @json_decode($x,$assoc=TRUE);
                if( !$d ) {
                        $this->error('wiib_api:call_commons: ERROR: json_decode failed. Error: ' . json_last_error() );
                        $this->error('wiib_api:call_commons: ERROR: ' . $this->wiib_json_last_error_msg() );
                        return FALSE;
                }
		$this->commons_response = $d;
		if( !$d['query'][$key] || !is_array($d['query'][$key])  ) { 
                        $this->error("wiib_api:call_commons: ERROR: missing key [query][$key].");
			return FALSE;
		} 

                $r = $this->api_format_to_standard_format($d['query'][$key]);
                return $r;
	}

	//////////////////////////////////////////////////////////
	function wiib_json_last_error_msg() {
		static $errors = array(
			JSON_ERROR_NONE             => null,
			JSON_ERROR_DEPTH            => 'Maximum stack depth exceeded',
			JSON_ERROR_STATE_MISMATCH   => 'Underflow or the modes mismatch',
			JSON_ERROR_CTRL_CHAR        => 'Unexpected control character found',
			JSON_ERROR_SYNTAX           => 'Syntax error, malformed JSON',
			JSON_ERROR_UTF8             => 'Malformed UTF-8 characters, possibly incorrectly encoded'
		);
		$error = json_last_error();
		return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
	}

	//////////////////////////////////////////////////////////
	function api_format_to_standard_format($images) {
		$this->debug('api_format_to_standard_format()'); // : images: ' . print_r($images,1));
		$r = array();
		$count = 0;
		while( list(,$x) = each($images) ) { 
			$r[$count]['pageid'] = @$x['pageid'];
			$r[$count]['title'] = @$x['title'];
			//$r[$count]['size'] = @$x['imageinfo'][0]['size'];
			$r[$count]['width'] = @$x['imageinfo'][0]['width'];
			$r[$count]['height'] = @$x['imageinfo'][0]['height'];
			$r[$count]['url'] = @$x['imageinfo'][0]['url'];
			$r[$count]['thumbwidth'] = @$x['imageinfo'][0]['thumbwidth'];
			$r[$count]['thumbheight'] = @$x['imageinfo'][0]['thumbheight'];
			$r[$count]['thumburl'] = @$x['imageinfo'][0]['thumburl'];
			$r[$count]['descriptionurl'] = @$x['imageinfo'][0]['descriptionurl'];
			$r[$count]['mime'] = @$x['imageinfo'][0]['mime'];
			$r[$count]['user'] = @$x['imageinfo'][0]['user'];
			$r[$count]['sha1'] = @$x['imageinfo'][0]['sha1'];
			$r[$count]['timestamp'] = @$x['imageinfo'][0]['timestamp'];
			$r[$count]['categories'] = array();
			$count++;
		}
		//$this->debug('api_format_to_standard_format(): return: ' . print_r($r,1));
		return $r;
	}

} // END class wiib_api

