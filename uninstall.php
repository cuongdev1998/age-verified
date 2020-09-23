<?php

 
require 'help.php';
db_insert("test_data",[
    "test" => 'da go', 
]);  
 

session_start();
  
unset($_SESSION['shop']);
$webhookContent = "";
$webhook = fopen('php://input', 'rb');
while (!feof($webhook)) {
    $webhookContent .= fread($webhook, 4096);
}
fclose($webhook);
$webhookContent = json_decode($webhookContent);
if (isset($webhookContent->myshopify_domain)) {
    $shop = $webhookContent->myshopify_domain;
    $db->query('delete from tbl_usersettings where store_name = "' . $shop . '" and app_id = ' . $appId);
    db_delete("tbl_usersettings",'store_name = "' . $shop . '" and app_id = ' . $appId);
    db_delete("custom_order_settings",'store_name = "' . $shop . '"');
 	// Gui email cho customer khi uninstalled
	require 'email/uninstall_email.php';	
} else if (isset($webhookContent->domain)) {
    $shop = $webhookContent->domain;
    db_delete("tbl_usersettings",'store_name = "' . $shop . '" and app_id = ' . $appId);
    db_delete("custom_order_settings",'store_name = "' . $shop . '"');
 
	// Gui email cho customer khi uninstalled
	require 'email/uninstall_email.php';	
}