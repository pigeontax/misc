<?php

include_once "iMac/utils.php";

class Movie {
	public $schema = array(
      //'dbName'      => 'apiName',
		'Name'        => 'Title',
		'Media'       => 'Type',
		'Genre'       => 'Genre',
		'RunTime'     => 'Runtime',
		'Image'       => 'Poster',
		'IMDB'        => 'imdbID',
		'Rating'      => 'Ratings',
		'Description' => 'Plot'
	);
	public $limit = ' limit 20 ';
	public $db;
	public $apikey = '2d744e09';  //OMDb API Key

	function __construct() {
		$this->db = new db();
	}

	/*
	 * sync movies against OMDB API
	 */
	public function sync() {
		return $this->syncMovies();
	}

	protected function syncMovies() {
		//make connection and retrieve list of movies that have no runtime and needs processing
		$query = "SELECT * from movies where RunTime IS NULL ";
		$results = $this->db->get_results($query);

		if(empty($results)) {
		    return "All movies have been updated!";
        }

		//iterate through list of results and find fields that need updating
        $cnt = 0;
		foreach ( $results as $movie ) {

		    //create list of arguments to use with OMDB API
            $args = array(
                'plot' => 'short',
                'r' => 'json',
                'type' => 'movie',
                'apikey' => $this->apikey
            );

            if (!empty($movie->IMDB)) {
                $args['i'] = $movie->IMDB;
            } else {
                $args['t'] = $movie->Name;
            }

            //call function to make api request
			$rez  = call( 'http://www.omdbapi.com/', '', 'GET', $args );

			//iterate through fields and create query to populate any missing fields
			$update = false;
			$sql    = "UPDATE movies SET ";

			foreach ( $this->getSchema() as $_db_key => $_imdb_key ) {

				//make sure data exists and local field is empty.  Do NOT over write fields
                if ( empty( $movie->$_db_key ) && ! empty( $rez->$_imdb_key ) ) {

					//add comma if field has already been added
					if ( $update ) {
						$sql .= ', ';
					}

					//handle the case of ratings which returns an array of objects
					if ( $_imdb_key == 'Ratings' ) {
                        //rating is 50 by default, unless rotten tomatoes rating is passed back
					    $ratingVal = 50;

					    if (is_array($rez->$_imdb_key)) {
                            foreach ( $rez->$_imdb_key as $ratingObj ) {
                                //use rotten tomatoes rating if found
                                if ( !empty($ratingObj->Source) &&  !empty($ratingObj->Value) && $ratingObj->Source ==  "Rotten Tomatoes"){
                                    $ratingVal = $ratingObj->Value;
                                }
                            }
                        }
                        //escape rating value and make part of query
                        $sql .= $_db_key . ' = "' . $this->db->escape($ratingVal) . '"';

					} else {

						//escape passed in values and make part of query
						$sql .= $_db_key . ' = "' . $this->db->escape($rez->$_imdb_key) . '"';

					}

					//set update flag to true
					$update = true;
				}
			}

			//only run update query if a field needs to be updated
			if ( $update ) {

                $sql .= ' where Name = "' . $movie->Name . '" ';
				$this->db->query($sql);
                //_log($sql);
				$cnt++;
			}
		}
		_log("updated $cnt movies ");
		return "updated $cnt movies";
	}


	public function getAllMovies($bot = '',$top = ''){
		$limit = $this->limit;

		//retrieve
		if(!empty($bot) && !empty($top)) {
			$limit = "limit $bot, $top ";
		}
		return $this->db->get_results( "SELECT * from movies $limit" );
	}


	public function searchMovies($srch = ''){
		if(empty($srch)) {
			_log("An error Occurred, IMDB id was not provided");
			return false;
		}

		$srch = $this->db->escape($srch);

		return $this->db->get_results( "SELECT * from movies where name like '%$srch%' or genre like '%$srch%' $this->limit" );

	}
	//
	public function getMoviebyName($srch = ''){
		if(empty($srch)) {
			_log("An error Occurred, IMDB id was not provided");
			return false;
		}

		$srch = $this->db->escape($srch);

		return $this->db->get_results( "SELECT * from movies where name like '%$srch%' " );

	}

	public function getMoviebyID($srch = ''){
		if(empty($imdb)) {
			_log("An error Occurred, IMDB id was not provided");
			return false;
		}

		$srch = $this->db->escape($srch);

		return $this->db->get_results( "SELECT * from movies where IMDB like '%$srch%'" );
	}


	/**
	 * @return array
	 */
	public function getSchema() {
		return $this->schema;
	}
}
