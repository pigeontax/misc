<?php

include_once "../../utils.php";
include_once "../../libs/plates/src/Engine.php";

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = str_replace('/imac/movieNight/api/', '', $_SERVER['REQUEST_URI']);
$requests = explode('/', $request);


// create SQL based on HTTP method
include '../Movie.php';
$m = new Movie();
$list = '';

switch ($method) {
	case 'GET':
		if (strtolower($requests[0]) == 'moviesync') {
			echo $m->sync();

		}elseif (strtolower($requests[0]) == 'getmovies') {
			if(!empty($requests[1]) && !empty($requests[2])) {
				$first  = intval($requests[1]);
			    $second = intval($requests[2]);

				//make sure the filters make sense
				if ($second > $first) {
                    //return a list within the range provided
					$list = $m->getAllMovies($first, $second);
				}
			} else {
				//return a list of all movies provided
				$list = $m->getAllMovies();
			}
		} elseif (strtolower($requests[0]) == 'search') {
			//make sure a search string is available
			if(!empty($requests[1])) {
				//escape string and search for it
				//$search  = $m->db->escape($request[1]);
				//no need to escape string, it is escaped by movie class
				$search  = $requests[1];
				$list = $m->searchMovies($search);
                //_pp($list);


			}else{
				//no search string, send back all
				$list = $m->getAllMovies();
			}

            $code = 200;

            if(empty($list)) {
                $code = 202;
            }

            //header('Content/Type: application/json');
            //header("Http/1.1 $code OK");
            echo json_encode($list);
            //break;
		}



		break;
	case 'PUT':
	case 'POST':
	case 'DELETE':
		//redirect to piss off page
		break;
}


