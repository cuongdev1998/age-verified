<?php
//ini_set('display_errors', TRUE);
//error_reporting(E_ALL);
require 'help.php';
require 'vendor/autoload.php';

use sandeepshetty\shopify_api;

session_start();

if (isset($_GET['charge_id'])) {
    $charge_id = $_GET['charge_id'];
    $shop = $_GET['shop'];
    $shopify = shopifyInit($db, $shop, $appId);
    $theCharge = $shopify("GET", "/admin/recurring_application_charges/$charge_id.json");

    if ($theCharge['status'] == 'accepted') {
        activeClient($appId, $shop, $db, $shopify, $charge_id, $apiKey);
    } else {
        deactiveClient($rootLink, $shop);
    }
}


function activeClient($appId, $shop, $db, $shopify, $charge_id, $apiKey) {
    $recu = $shopify("POST", "/admin/recurring_application_charges/$charge_id/activate.json");
    db_update("tbl_usersettings",["status" => 'active'],"app_id = $appId and store_name = '$shop'"); 
    header('Location: https://'.$shop.'/admin/apps/'.$apiKey.'');
}

function deactiveClient($rootLink, $shop) { 
    header('Location: '.$rootLink.'/declineCharge.php?shop='.$shop);
}