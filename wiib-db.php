<?php
// WIIB - Version 0.6.0
// DATABASE WIIB

class wiib_db extends wiib_db_init {

	var $image_count;
	var $unrated_count;
	var $portfolio_list;

        //////////////////////////////////////////////////////////
	function __construct() {
                parent::__construct();
		//$this->debug('wiib_db:__construct()');
	}
	
	//////////////////////////////////////////////////////////
	function vote_for($id='') {
		$this->debug("wiib_db:vote_for($id)");
		if( !$id ) { $this->error("wiib_db:vote_for() ERROR no id"); return FALSE; }
		$sql = 'UPDATE images SET votes_for = votes_for + 1, last_seen = DATETIME("now") WHERE pageid = :id';
		return $this->query_as_bool($sql, $bind = array(':id'=>$id) );
	}

        //////////////////////////////////////////////////////////
	function vote_against($id='') {
                $this->debug("wiib_db:vote_against($id)");
		if( !$id ) { $this->error("wiib_db:vote_against() ERROR no id"); return FALSE; }
                $sql = 'UPDATE images SET votes_against = votes_against + 1, last_seen = DATETIME("now") WHERE pageid = :id';
		return $this->query_as_bool($sql, $bind = array(':id'=>$id) );
        }

        //////////////////////////////////////////////////////////
	function promote_image($id='') { 
                $this->debug("wiib_db:promote_image($id)");
		if( !$id ) { $this->error("wiib_db:promote_image() ERROR no id"); return FALSE; }
                $sql = 'UPDATE images 
		SET portfolio = portfolio + 1, votes_for = votes_for + 1, last_seen = DATETIME("now") WHERE pageid = :id';
		return $this->query_as_bool($sql, $bind = array(':id'=>$id) );
	}

	//////////////////////////////////////////////////////////
	function save_images_to_database($images='') {
		$this->debug('save_images_to_database()'); // :' . print_r($images,1));
		if( !$images || !is_array($images) ) { 
			$this->error('save_images_to_database() ERROR: no image array'); 
			return FALSE; 
		}
		while( list(,$image) = each($images) ) {
			if( !isset($image['descriptionurl']) || !$image['descriptionurl'] ) { 
				//$this->error('save_images_to_database() ERROR: no descriptionurl'); 
				$this->debug('save_images_to_database() ERROR: no descriptionurl'); 
				continue;
			} 
			if( !isset($image['mime']) || $image['mime'] == 'application/ogg' ) { 
				$this->debug('save_images_to_database() ERROR: skipping ogg'); 
				continue;
			}
			if( !isset($image['mime']) || $image['mime'] == 'application/pdf' ) { 
				$this->debug('save_images_to_database() ERROR: skipping pdf'); 
				continue;
			}
			$sql = 'INSERT OR REPLACE INTO images (pageid,title,sha1,timestamp,portfolio,thumburl,thumbheight,thumbwidth,url,
				height,width,descriptionurl,user,mime,last_seen) VALUES (:pageid,:title,
				:sha1,:timestamp,:portfolio,:thumburl,:thumbheight,:thumbwidth,:url,:height,:width,:descriptionurl,
				:user,:mime,DATETIME("now"))';
			$r = $this->query_as_bool($sql, $bind = array(
				':pageid'=> $image['pageid'],
				':title'=> $image['title'],
				':sha1'=> $image['sha1'],
				':timestamp'=> $image['timestamp'],
				':portfolio'=> $this->portfolio,
				':user'=> $image['user'],
				':mime'=> $image['mime'],
				':thumburl'=> $image['thumburl'],
				':thumbheight'=> $image['thumbheight'],
				':thumbwidth'=> $image['thumbwidth'],
				':url'=> $image['url'],
				':height'=> $image['height'],
				':width'=> $image['width'],
				':descriptionurl'=> $image['descriptionurl'],
			));
			if( !$r ) { 
				$this->error('save_images_to_database() ERROR'); 
			} else { 
				$this->debug('save_images_to_database() - saved pageid: '. $image['pageid']);
			}
		}
		$this->get_image_count();
	}

	//////////////////////////////////////////////////////////
	function get_image_from_db($pageid) {
		$this->debug("wiib-db:get_image_from_db($pageid)");
		if( !$pageid || !$this->is_number($pageid) ) { 
			$this->error('get_image_from_db: ERROR no id'); return FALSE;
		}
		$sql = 'SELECT * FROM images WHERE pageid = :pageid';
		return $this->query_as_array( $sql , $bind = array(':pageid'=>$pageid) );
	}

	//////////////////////////////////////////////////////////
	function get_images_by_portfolio($limit=2, $sort='pageid', $sort_o='DESC') {
		$this->debug("wiib-db:get_images_by_portfolio($limit, $sort, $sort_o) portfolio: " . $this->portfolio);
		if( !$limit || !$this->is_positive_number($limit) ) { 
			$this->error("wiib-db:get_images_by_portfolio: ERROR bad limit: $limit");
			$limit = 2;
		} 
		if( $sort_o != 'DESC' ) { $sort_o = 'ASC'; } 
		//$sql = "SELECT * FROM images WHERE portfolio = :portfolio ORDER BY :sort $sort_o LIMIT :limit";
		$sql = "SELECT * FROM images WHERE portfolio = :portfolio ORDER BY $sort $sort_o LIMIT :limit";
		//$this->error($sql . " | p=$this->portfolio s=$sort l=$limit");
		$this->images = $this->query_as_array( $sql, 
				$bind = array(
					':portfolio'=>$this->portfolio, 
					//':sort'=>$sort,
					':limit'=>$limit 
				) 
			);
	} 

	//////////////////////////////////////////////////////////
	function get_random_images_from_db($limit=2) {
		$this->debug("get_random_images_from_db($limit)");

		if( $this->count_unrated() > 0 ) { 
			$sql = 'SELECT * FROM images 
				WHERE portfolio = :portfolio 
				AND votes_for = 0 AND votes_against = 0
				ORDER BY RANDOM() 
				LIMIT :limit';
		} else {
	
			if (mt_rand(1,4)==1) { 
				// select from ALL
				$sql = 'SELECT * FROM images WHERE portfolio = :portfolio 
					ORDER BY RANDOM() LIMIT :limit';
			} else {
				// select from least-voted-on images
				$sql = 'SELECT *, (votes_for + votes_against) AS votes FROM images 
					WHERE portfolio = :portfolio 
					ORDER BY votes ASC, RANDOM() LIMIT :limit';
			} 

		}
		return $this->query_as_array( $sql , $bind = array(':portfolio'=>$this->portfolio, 'limit'=>$limit) );
	}

	//////////////////////////////////////////////////////////
	function delete_image($pageid, $fulldelete=false) { 
		$this->debug("wiib_db:delete_image($pageid)");
                $sql = 'UPDATE images 
			SET votes_against = votes_against + 1, last_seen = DATETIME("now"), 
			-- portfolio = portfolio - 1, 
			portfolio = -1 
			WHERE pageid = :pageid';
		if( $fulldelete ) { 
			$sql = 'DELETE FROM images WHERE pageid = :pageid';
		} 
		$r = $this->query_as_bool($sql, $bind = array(':pageid'=>$pageid) );
		if( !$r ) { return FALSE; } 
		$this->debug('delete_image: DELETED ' . $pageid);
		$this->get_image_count();
		return TRUE;
	}

	//////////////////////////////////////////////////////////
	function clear_all_ratings() {
		$this->debug('clear_all_ratings()');
                $sql = 'UPDATE images SET votes_for = 0, votes_against = 0, portfolio = 0, 
			last_seen = DATETIME("now")
			WHERE portfolio >= 0';
		return $this->query_as_bool($sql);
	}

	//////////////////////////////////////////////////////////
	function empty_trash() {
		$this->debug('empty_trash()');
		$sql = 'DELETE FROM images WHERE portfolio < 0';
		return $this->query_as_bool($sql);
	}

	//////////////////////////////////////////////////////////
	function empty_images() {
		$this->debug('empty_images()');
		$sql = 'DELETE FROM images';
		return $this->query_as_bool($sql);
	}

	//////////////////////////////////////////////////////////
	function get_image_count() {
		$this->debug('wiib_db:get_image_count()');
		$sql = 'SELECT count(pageid) AS count FROM images WHERE portfolio = :portfolio';
		$x = $this->query_as_array($sql, $bind=array(':portfolio'=>$this->portfolio) );
		if( !$x ) { $this->error('get_image_count() ERROR db'); return FALSE; }
		$this->image_count = $x[0]['count'];
		return $this->image_count;
	}

	//////////////////////////////////////////////////////////
	function get_portfolio_list() {
		$this->debug('wiib_db:get_portfolio_list()');
		if( $this->portfolio_list ) { return $this->portfolio_list; }  
		$sql='SELECT portfolio, count(portfolio) AS count, (votes_for+votes_against) AS votes,
		sum(votes_for) AS votes_for, sum(votes_against) AS votes_against
		FROM images GROUP BY portfolio ORDER BY portfolio ASC';
		return $this->portfolio_list = $this->query_as_array($sql);	
	}


	//////////////////////////////////////////////////////////
	function get_category_list() { 
		$this->debug("wiib_db:get_categories()");
		$sql = 'SELECT name FROM categories ORDER BY name';
		$x = $this->query_as_array( $sql );
		$r = array();
		if( !$x || !is_array($x) ) { return $r; } 
		while( $y = each($x) ) { 
			$r[] = $y['value']['name'];
		} 
		return $r;
 
	}

	//////////////////////////////////////////////////////////
	function insert_category($c='') { 
		$this->debug("wiib_db:insert_category($c)");
		if( !$c ) { return false; } 	
		$sql = 'INSERT OR IGNORE INTO categories (name) VALUES (:name)';
		return $this->query_as_bool( $sql , $bind = array(':name'=>$c) );
	}

	//////////////////////////////////////////////////////////
	function count_unrated() {
		$sql = 'SELECT count(pageid) AS count FROM images 
			WHERE votes_for = 0 AND votes_against = 0 AND portfolio = :portfolio';
		$r = $this->query_as_array( $sql, array(':portfolio'=>$this->portfolio) );
		$count = @$r[0]['count'];
		if( !$count ) { $count = '0'; } 
		return $this->unrated_count = $count;
	}

	//////////////////////////////////////////////////////////
	function number_of_votes($portfolio) { 
		$sql = 'SELECT (votes_for+votes_against) as votes, count(pageid) as count
			FROM images 
			WHERE portfolio = :portfolio
			GROUP BY votes
			ORDER BY votes ASC
			';
		return $this->query_as_array( $sql, array(':portfolio'=>$portfolio) );
	}

} // END class wiib_db 

