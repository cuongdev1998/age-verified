<?php
require 'vendor/autoload.php';

use sandeepshetty\shopify_api;

require 'help.php';

$shop = $_GET['shop']; //shop name
$select_settings = $db->query("SELECT * FROM tbl_appsettings WHERE id = $appId");
$app_settings = $select_settings->fetch_object();
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
if($chargeType == "one-time"){
    $recu = $shopify("POST", "/admin/application_charges.json", $charge);
    $confirmation_url = $recu["confirmation_url"];
} else {
    $recu = $shopify("POST", "/admin/recurring_application_charges.json", $charge);
    $confirmation_url = $recu["confirmation_url"];
}
db_update("tbl_usersettings",['confirmation_url' => $confirmation_url],"store_name = '$shop' and app_id = $appId");
echo $confirmation_url;
 