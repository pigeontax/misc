<?php
// Include ezSQL core
include_once "libs/ezSQL/shared/ez_sql_core.php";

// Include ezSQL database specific component
include_once "libs/ezSQL/mysqli/ez_sql_mysqli.php";



class db extends ezSQL_mysqli {

	function __construct($dbuser = 'root', $dbpassword = 'root', $dbname = 'outerBox', $dbhost='localhost', $encoding = '')
	{
		parent::__construct($dbuser, $dbpassword, $dbname, $dbhost, $encoding);
	}

}



//print a line to the log file
function _log($entry, $logName = 'log', $path = '') {
	global $mod_strings, $dce_config, $client_name;
	$nld = '
'.date("Y-m-d H:i:s").'.. ';

	if(!empty($path)) {
		$path = checkSlash($path);
	}
	$log = $path . $logName . '.log';

	// create if not exists
	if(!file_exists($log)) {
		$fp = @fopen($log, 'w+'); // attempts to create file
		if(!is_resource($fp)) {
			die('could not create the process log file');
		}
	} else {
		//clear filesize cache
		clearstatcache();
		//grab size of file and check to see if it is 10MB or more)
		$size = filesize($log);
		if($size > 10000000){
			//if size is more than 10 MB, rename log file
			rename($log, $log.strtotime("now"));
			//now attempt to recreate file
			$fp = @fopen($log, 'w+'); // attempts to create file
			if(!is_resource($fp)) {
				die('could not create the process log file');
			}

		}else{
			//size of log is under 10MB, so open up file to write to
			$fp = @fopen($log, 'a+'); // write pointer at end of file
			if(!is_resource($fp)) {
				die('could not open/lock process log file');
			}
		}

	}

	if(@fwrite($fp, $nld.$entry) === false) {
		die('could not write to process log: '.$entry);
	}

	if(is_resource($fp)) {
		fclose($fp);
	}
}

function checkSlash($dirSTR){
	if(substr($dirSTR, -1, 1) != '/'){
		$dirSTR .= '/';
	}
	return $dirSTR;

}

//generate random string of n characters
function createRandPass($numChars=7){

	//chars to select from
	$charBKT = "abcdefghijklmnpqrstuvwxyz123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
	// seed the random number generator
	srand((double)microtime()*1000000);
	$password="";
	for ($i=0;$i<$numChars;$i++)  // loop and create password
		$password = $password . substr ($charBKT, rand() % strlen($charBKT), 1);

	return $password;

}

/**
 * Generic function to make cURL request.
 * @param $url - The URL route to use.
 * @param string $oauthtoken - The oauth token.
 * @param string $type - GET, POST, PUT, DELETE. Defaults to GET.
 * @param array $arguments - Endpoint arguments.
 * @param array $encodeData - Whether or not to JSON encode the data.
 * @param array $returnHeaders - Whether or not to return the headers.
 * @return mixed
 *
 * example:
 *
$parameters = array(	"grant_type" => "password", 	"client_id" => "testing", 	"client_secret" => "",
"username" => $username, 	"password" => $password, 	"platform" => "base" );

$url = $base_url . "/oauth2/token";
$token_result = call($url, '', 'POST', $parameters);

 *
 */
function call(
	$url,
	$oauthtoken='',
	$type='GET',
	$arguments=array(),
	$encodeData=true,
	$returnHeaders=false
)
{
	$type = strtoupper($type);

	if ($type == 'GET')
	{
		$url .= "?" . http_build_query($arguments);
	}
    //_pp($url);
	$curl_request = curl_init($url);

	if ($type == 'POST')
	{
		curl_setopt($curl_request, CURLOPT_POST, 1);
	}
	elseif ($type == 'PUT')
	{
		curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "PUT");
	}
	elseif ($type == 'DELETE')
	{
		curl_setopt($curl_request, CURLOPT_CUSTOMREQUEST, "DELETE");
	}

	curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
	curl_setopt($curl_request, CURLOPT_HEADER, $returnHeaders);
	curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);

	if (!empty($oauthtoken))
	{
		$token = array("oauth-token: {$oauthtoken}");
		curl_setopt($curl_request, CURLOPT_HTTPHEADER, $token);
	}

	if (!empty($arguments) && $type !== 'GET')
	{
		if ($encodeData)
		{
			//encode the arguments as JSON
			$arguments = json_encode($arguments);
		}
		curl_setopt($curl_request, CURLOPT_POSTFIELDS, $arguments);
	}

	$result = curl_exec($curl_request);


	if ($returnHeaders)
	{
		//set headers from response
		list($headers, $content) = explode("\r\n\r\n", $result ,2);
		foreach (explode("\r\n",$headers) as $header)
		{
			header($header);
		}

		//return the nonheader data
		return trim($content);
	}

	curl_close($curl_request);

	//decode the response from JSON
	$response = json_decode($result);

	return $response;
}

/********************************************************/
/********************************************************/
/********************************************************/
/**
 * The pp stands for Pretty Print.
 */
function _pp($mixed, $loggit=false)
{

    if($loggit) ob_start();

      "\n<pre>\n";
    print_r($mixed);

    echo "";
    $stack  = debug_backtrace();
    if (!empty($stack) && isset($stack[0]['file']) && $stack[0]['line']) {
        echo "\n\n _pp caller, file: " . $stack[0]['file']. ' line#: ' .$stack[0]['line'];
    }
    echo "\n</pre>\n";

    if($loggit) {
        $buffer = ob_get_clean();
	    _log($buffer);
    }
}

function _pstack_trace($mixed=NULL)
{

    echo "\n<pre>\n_pstack_trace: '";
    if(!empty($mixed))
        print_r($mixed);

    echo "'<BR>\n";
    debug_print_backtrace();

    echo "\n</pre>\n";
}


/**
 * The ppt stands for Pretty Print Trace.
 */
function _ppt($mixed, $loggit=false, $textOnly=true)
{
    if($loggit) ob_start();

    echo "\n<pre>\n";
    print_r($mixed);
    echo "\n</pre>\n";
    display_stack_trace($textOnly);

    if($loggit) {
        $buffer = ob_get_clean();
	    _log($buffer);
    }

}

/**
 * The pptd stands for Pretty Print Trace Die.
 */
function _pptd($mixed, $loggit=false)
{
    if($loggit) ob_start();

    echo "\n<pre>\n";
    print_r($mixed);
    echo "\n</pre>\n";
    display_stack_trace();

    if($loggit) {
        $buffer = ob_get_clean();
	    _log($buffer);
    }
    die();

}


/**
 * creates readable debug stack trace
 */
function display_stack_trace($textOnly=false){

    $stack  = debug_backtrace();

    echo "\n\n display_stack_trace caller, file: " . $stack[0]['file']. ' line#: ' .$stack[0]['line'];

    if(!$textOnly)
        echo '<br>';

    $first = true;
    $out = '';

    foreach($stack as $item) {
        $file  = '';
        $class = '';
        $line  = '';
        $function  = '';

        if(isset($item['file']))
            $file = $item['file'];
        if(isset($item['class']))
            $class = $item['class'];
        if(isset($item['line']))
            $line = $item['line'];
        if(isset($item['function']))
            $function = $item['function'];

        if(!$first) {
            if(!$textOnly) {
                $out .= '<font color="black"><b>';
            }

            $out .= $file;

            if(!$textOnly) {
                $out .= '</b></font><font color="blue">';
            }

            $out .= "[L:{$line}]";

            if(!$textOnly) {
                $out .= '</font><font color="red">';
            }

            $out .= "({$class}:{$function})";

            if(!$textOnly) {
                $out .= '</font><br>';
            } else {
                $out .= "\n";
            }
        } else {
            $first = false;
        }
    }

    echo $out;
}
