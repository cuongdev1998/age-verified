<?php
// Cloudflare get UserIP
if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
  $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
}
$apiKey = "1fea2c06cad29a7a2ff47ed9c5b1b5cc";
$appId = "36";
$rootLink = "https://apps.omegatheme.com/region-restrictions";
$trialTime =7;
$chargeType = "monthly";
$price = 1;

/*$trialTime =0;
$chargeType = "free";
$price = 0;*/

//true or null
$testMode = "false";
$appName = "Region Restrictions";
$linkApp = 'https://apps.shopify.com/region-restrictions?reveal_new_review=true';
$chargeTitle = "Region Restrictions Monthly charge";
$iemid = "50";
$iemid_unsub = "51";

$db = new Mysqli("p:10.12.82.14", "shopify_read", "", "shopify");
if($db->connect_errno){
  die('Connect Error: ' . $db->connect_errno);
}
$db->query("set names 'utf8mb4'");

function connectMasterDB(){
    global $dbw;
    if(!isset($dbw)){ 
        $dbw = new Mysqli("10.12.82.13", "shopify", "", "shopify");
        $dbw->query("set names 'utf8mb4'");
        if($dbw->connect_errno){
          die('Connect Error: ' . $dbw->connect_errno);
        }
    }
}