<?php

use sandeepshetty\shopify_api; 

require 'conn-shopify.php';

function db_fetch_row($query_string) {
    global $db;
    $result = array();
    $mysqli_result = db_query($query_string);
    $result = mysqli_fetch_assoc($mysqli_result);
    mysqli_free_result($mysqli_result);
    return $result;
}
function db_queryw($query_string) {
    global $dbw;
    if(!isset($dbw)){
        connectMasterDB();
    }
    $db = $dbw;
    $result = mysqli_query($db, $query_string);
    if (!$result) {
        echo ('Query Error' . $query_string); die();
    }
    return $result;
}
function db_query($query_string) {
    global $db; 
    $result = mysqli_query($db, $query_string);
    if (!$result) {
        echo ('Query Error' . $query_string); die();
    }
    return $result;
}

function redirect($data) {
    echo $data;
    header("Location: $data");
}



function db_insert($table, $data) {
    global $dbw;
    if(!isset($dbw)){
        connectMasterDB();
    }
    $db = $dbw;
    $fields = "(" . implode(", ", array_keys($data)) . ")";
    $values = "";
    foreach ($data as $field => $value) {
        if ($value === NULL)
            $values .= "NULL, ";
        else
            $values .= "'" . addslashes($value) . "', ";
    }
    $values = substr($values, 0, -2);
   
    db_queryw("
            INSERT INTO $table $fields
            VALUES($values)
        ");
    return mysqli_insert_id($db);
}

function db_update($table, $data, $where) {
    global $dbw;
    if(!isset($dbw)){
        connectMasterDB();
    }
    $db = $dbw;
    $sql = "";
    foreach ($data as $field => $value) {
        if ($value === NULL)
            $sql .= "$field=NULL, ";
        else
            $sql .= "$field='" . addslashes($value) . "', ";
    }
    $sql = substr($sql, 0, -2);
    db_queryw("
            UPDATE `$table`
            SET $sql
            WHERE $where
   ");
    return mysqli_affected_rows($db);
}

function db_duplicate($table,$data,$content_duplicate){
    global $dbw;
    if(!isset($dbw)){
        connectMasterDB();
    }
    $db = $dbw;
    $fields = "(" . implode(", ", array_keys($data)) . ")";
    $values = "(";
    foreach ($data as $field => $value) {
        if ($value === NULL)
            $values .= "NULL, ";
        elseif ($value === TRUE || $value === FALSE)
            $values .= "$value, ";
        else
            $values .= "'" . addslashes($value) . "',";
    }  
    $values = rtrim($values,',');
    $values .= ")";
    $query = "INSERT INTO $table  $fields  VALUES $values ON DUPLICATE KEY UPDATE $content_duplicate";  
    db_queryw($query);
    return  mysqli_insert_id($db);  
}
function db_delete($table, $where) {
    global $dbw;
    if(!isset($dbw)){
        connectMasterDB();
    }
    $db = $dbw;
    $query_string = "DELETE FROM " . $table . " WHERE $where";
    db_queryw($query_string);
    return mysqli_affected_rows($db);
}

function db_fetch_array($query_string) {
    global $db;
    $result = array();
    $mysqli_result = db_query($query_string);
    while ($row = mysqli_fetch_assoc($mysqli_result)) {
        $result[] = $row;
    }
    mysqli_free_result($mysqli_result);
    return $result;
}
 
function IpToInfo($userIp) {
    $table_ipv4_to_location = "ip2location_db11";
    $table_ipv6_to_location = "ip2location_db11_ipv6";

    // Connect to database
    $db = ConnectToIpDb(); 

    $info = array();
    if (isIpV6($userIp)) {
        $ipno = Dot2LongIPv6($userIp);
        $query = $db->query("SELECT * FROM $table_ipv6_to_location WHERE ip_to >= $ipno ORDER BY ip_to LIMIT 1");
    } else {
        $query = $db->query("SELECT * from $table_ipv4_to_location WHERE inet_aton('$userIp') <= ip_to LIMIT 1");
    }
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $info = $row;
        }
    }
    return $info;
}

function ConnectToIpDb () {
	$db = new Mysqli("p:localhost", "shopify", "h1yw5ovS78iYaGRX", "ip2location");
	if($db->connect_errno){
	  die('Connect Error: ' . $db->connect_errno);
	}	
    return $db;
}

function isIpV6($ip) {
    if (strpos($ip, ':')) {
        return true;
    }
    return false;
}

// Function to convert IP address to IP number (IPv6)
function Dot2LongIPv6 ($IPaddr) {
    $int = inet_pton($IPaddr);
    $bits = 15;
    $ipv6long = 0;
    while($bits >= 0){
        $bin = sprintf("%08b", (ord($int[$bits])));
        if($ipv6long){
            $ipv6long = $bin . $ipv6long;
        }
        else{
            $ipv6long = $bin;
        }
        $bits--;
    }
    $ipv6long = gmp_strval(gmp_init($ipv6long, 2), 10);
    return $ipv6long;
}

function show_array($data) {
    if (is_array($data)) {
        echo "<pre>";
        print_r($data);
        echo "<pre>";
    }
}
function shopifyInit($db, $shop, $appId) {
    $select_settings = $db->query("SELECT * FROM tbl_appsettings WHERE id = $appId");
    $app_settings = $select_settings->fetch_object();
    $shop_data1 = $db->query("select * from tbl_usersettings where store_name = '" . $shop . "' and app_id = $appId"); 
    $shop_data = $shop_data1->fetch_object();
    if(!isset($shop_data->access_token)){
        die("Please check the store: ".$shop." seems to be incorrect access_token.");
    }
    $shopify = shopify_api\client( 
            $shop, $shop_data->access_token, $app_settings->api_key, $app_settings->shared_secret
    );
    return $shopify;
} 
function cvf_convert_object_to_array($data) {
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    } else {
        return $data;
    }
}
function getCurl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1');
    $response = curl_exec($ch);
    if ($response === false) {
        $api_response = curl_error($ch);
    } else {
        $api_response = $response;
    }
    curl_close($ch);
    return $api_response;
}

function valaditon_get($data) {
    if ($data) {
        return $data;
    } else {
        $data = "";
        return $data;
    }
}

function result_fetch_object($data) {
    $result = $data->fetch_object();
    return $result;
}