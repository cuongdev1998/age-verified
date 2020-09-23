<?php
date_default_timezone_set('UTC');
require 'vendor/autoload.php';
require 'help.php';

use sandeepshetty\shopify_api;

 
$select_settings = $db->query("SELECT * FROM tbl_appsettings WHERE id = $appId");
$app_settings = $select_settings->fetch_object();
if (!empty($_GET['shop']) && !empty($_GET['code'])) {
    $shop = $_GET['shop']; //shop name
    //get permanent access token
    $access_token = shopify_api\oauth_access_token(
            $_GET['shop'], $app_settings->api_key, $app_settings->shared_secret, $_GET['code']
    );
    $installed = checkInstalled($db, $shop, $appId);
    if ($installed["installed"]) {
        $date_installed = $installed["installed_date"];
        db_insert("tbl_usersettings",[
            "access_token" => $access_token,
            "store_name" => $shop,
            "app_id" => $appId,
            "installed_date" => $date_installed,
            "confirmation_url" => ''
        ]);  
        $date1 = new DateTime($installed["installed_date"]);
        $date2 = new DateTime("now");
        $interval = date_diff($date1, $date2);
        $diff = (int) $interval->format('%R%a');
        $trialTime = $trialTime - $diff;
        if ($trialTime < 0) {
            $trialTime = 0;
        }
    } else {
        db_insert("tbl_usersettings",[
            "access_token" => $access_token,
            "store_name" => $shop,
            "app_id" => $appId,
            "installed_date" => date('Y-m-d H:i:s'),
            "confirmation_url" => ''
        ]);  
        db_insert("shop_installed",[
            "shop" => $shop,
            "app_id" => $appId,
            "date_installed" => date('Y-m-d H:i:s'), 
        ]); 
      
    } 
//insert shop setting for  app
    if (getShopSettings($db, $shop) == false) {
        db_insert("region_settings",[
            "shop" => $shop, 
        ]);  
    }

    $shop_data = $db->query("select * from tbl_usersettings where store_name = '" . $shop . "' and app_id = $appId");
    $shop_data = $shop_data->fetch_object();
    $shopify = shopify_api\client(
            $shop, $shop_data->access_token, $app_settings->api_key, $app_settings->shared_secret
    ); 
    //charge fee
    $charge = array(
        "recurring_application_charge" => array(
            "name" => $chargeTitle,
            "price" => $price,
            "return_url" => "$rootLink/charge.php?shop=$shop",
            "test" => $testMode,
            "trial_days" => $trialTime
        )
    );
    if ($chargeType == "one-time") {
        $recu = $shopify("POST", "/admin/application_charges.json", $charge);
        $confirmation_url = $recu["confirmation_url"];
    } else {
        $recu = $shopify("POST", "/admin/recurring_application_charges.json", $charge);
        $confirmation_url = $recu["confirmation_url"];
    }
    db_update("tbl_usersettings",["confirmation_url" => $confirmation_url],"store_name = '" . $shop . "' and app_id = $appId");  
    // Gui email cho customer khi cai dat 
	require 'email/install_email.php';
    //add js to shop
    $check = true;
    $check1 = true;
    $putjs1 = $shopify('GET', '/admin/script_tags.json');
    if ($putjs1) {
        foreach ($putjs1 as $value) {
            if ($value["src"] == $rootLink . '/regionrestrictions.js') {
                $check = false;
            }
        }
    }
    if ($check) {
        $putjs = $shopify('POST', '/admin/script_tags.json', array('script_tag' => array('event' => 'onload', 'src' => $rootLink . '/regionrestrictions.js')));
    }
    //hook when user remove app
    $webhook = $shopify('POST', '/admin/webhooks.json', array('webhook' =>
        array(
            'topic' => 'app/uninstalled',
            'address' => $rootLink . '/uninstall.php',
            'format' => 'json')));
 
    if($chargeType == "free"){
        db_update("tbl_usersettings",["status" => 'active'],"store_name = '$shop' and app_id = $appId"); 
        header('Location: https://'.$shop.'/admin/apps/'.$apiKey.'');
    } else {
        header('Location: ' . $confirmation_url);
    }
    
} 
function checkInstalled($db, $shop, $appId) {
    $sql = "select * from shop_installed where shop = '$shop' and app_id = $appId";
    $query = $db->query($sql);
    if ($query->num_rows > 0) {
        while ($row = $query->fetch_assoc()) {
            $date_instaled = $row["date_installed"];
            $result = array(
                "installed_date" => $date_instaled,
                "installed" => true
            );
            return $result;
        }
    } else {
        $result = array(
            "installed" => false
        );
        return $result;
    }
} 
function getShopSettings($db, $shop) {
    $sql = "SELECT * FROM region_settings WHERE shop = '$shop'";
    $query = $db->query($sql);
    if ($query->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
