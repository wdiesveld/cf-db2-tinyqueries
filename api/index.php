<?php

require_once( dirname(__FILE__) . '/../libs/DB2/DB2.php');
require_once( dirname(__FILE__) . '/../libs/TinyQueries/TinyQueries.php');

/**
 * Add multi-byte function needed for TQ in case mbstring extension is not installed
 *
 */
if (!function_exists('mb_detect_encoding')) 
{ 
	function mb_detect_encoding ($string, $enc=null, $ret=null) 
	{ 
        static $enclist = array( 
            'UTF-8', 'ASCII', 
            'ISO-8859-1', 'ISO-8859-2', 'ISO-8859-3', 'ISO-8859-4', 'ISO-8859-5', 
            'ISO-8859-6', 'ISO-8859-7', 'ISO-8859-8', 'ISO-8859-9', 'ISO-8859-10', 
            'ISO-8859-13', 'ISO-8859-14', 'ISO-8859-15', 'ISO-8859-16', 
            'Windows-1251', 'Windows-1252', 'Windows-1254', 
            );
        
        $result = false; 
        
        foreach ($enclist as $item) { 
            $sample = iconv($item, $item, $string); 
            if (md5($sample) == md5($string)) { 
                if ($ret === NULL) { $result = $item; } else { $result = true; } 
                break; 
            }
        }
        
		return $result; 
	}
}

try
{
	//parse vcap
	if( getenv("VCAP_SERVICES") ) {
		$json = getenv("VCAP_SERVICES");
	} 
	# No DB credentials
	else {
		throw new Exception("No Database Information Available.", 1);
	}

	# Decode JSON and gather DB Info
	$services_json = json_decode($json,true);
	$bludb_config = $services_json["sqldb"][0]["credentials"];

	// create DB connect string
	$conn_string = 
		"DRIVER={IBM DB2 ODBC DRIVER};DATABASE=".$bludb_config["db"].
	   ";HOSTNAME=". $bludb_config["host"].
	   ";PORT=". $bludb_config["port"].
	   ";PROTOCOL=TCPIP;UID=". $bludb_config["username"].
	   ";PWD=".$bludb_config["password"].
	   ";";
	   
	$db2 = new DB2($conn_string);
	
	$api = new TinyQueries\Api();
	
	$api->db = new TinyQueries\DB( $db2 ); 
	
	$api->sendResponse();
}
catch (Exception $e)
{
	echo "error: ". $e->getMessage();
}   

?>