<?php
ini_set('display_errors', TRUE);
error_reporting(E_ALL);
date_default_timezone_set('UTC');

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require 'vendor/autoload.php';
use sandeepshetty\shopify_api;

 require 'help.php'; 

if (isset($_GET["action"])) {
    $action = $_GET["action"];
    $shop = $_GET["shop"];
     
    if ($action == "getRestrictions") { 
        $settings = getShopSettings($db, $shop);
        $response = array(
            "html" => '', 
        );
        $html = "";
        if(!isset($settings['enableApp']) || $settings['enableApp'] == 0) return $response;
        // check blacklisst or white list 
        if(isset($settings['block_for_collection'])) $block_for_collection = $settings['block_for_collection'];
            else $block_for_collection = 1;


        if($block_for_collection == 1){
            $location = 0;
        }else{
            $location = getShopLocation($settings, $db, $shop);
        } 

        if ($location == 1) {
            updateStatic($db, $shop); 
            
                if(isset($settings['logo'])) $settings_logo = $settings['logo'];
                else $settings_logo = '';
    
                if(isset($settings['title'])) $settings_title = $settings['title'];
                else $settings_title = '';
    
                if(isset($settings['sub_color'])) $settings_sub_color = $settings['sub_color'];
                else $settings_sub_color = '';
    
                if(isset($settings['subTitle'])) $settings_subTitle  = $settings['subTitle'];
                else $settings_subTitle  = '';
    
                if(isset($settings['title_logo']) && $settings['title_logo'] != "") $settings_title_logo  = $settings['title_logo'];
                else $settings_title_logo  = 'https://www.google.com/';
    
                if(isset($settings['bg_submit'])) $settings_bg_submit  = $settings['bg_submit'];
                else $settings_bg_submit  = '#804000';
    
                if(isset($settings['color_submit'])) $settings_color_submit  = $settings['color_submit'];
                else $settings_color_submit  = '#ffffff';
    
                if(isset($settings['labelSubmit'])) $settings_labelSubmit  = $settings['labelSubmit'];
                else $settings_labelSubmit  = 'Continue Anyway	';
    
                if(isset($settings['popup_bg'])) $settings_popup_bg  = $settings['popup_bg'];
                else $settings_popup_bg  = '';
    
                if(isset($settings['title_color'])) $settings_title_color  = $settings['title_color'];
                else $settings_title_color  = '#ffffff';
    
                
                if(isset($settings['av_layout'])) $settings_av_layout  = $settings['av_layout'];
                else $settings_av_layout  = 1; 
    
                if(isset($settings['popup_color'])) $settings_popup_color  = $settings['popup_color'];
                else $settings_popup_color  = 1;
                if(isset($settings['bg_wr'])) $bg_wr  = $settings['bg_wr'];
                else $bg_wr  = "#1f4647";
                if($settings['enableRedirect'] == 1){
                    $html .= "
                    <script>
                        window.location.href = '$settings_title_logo'; 
                    </script> 
                    ";
                }else{
                    if ($settings_logo != '')
                        $logo = "<img id='region_logo' src='" . $settings["logo"] . "'>";
                    else
                        $logo = "";
                    $html .= "<div class='main-content1' id='MainContent' role='main'>
                                    <div class='overlay_page text-center'>
                                        " . $logo . "
                                        <h1>" . $settings_title . "</h1>
                                        <p style='color:" . $settings_sub_color. ";'>" . $settings_subTitle  . "</p>
                                        <p>
                                            <a href='" . $settings_title_logo . "' class='btn btn--has-icon-after' style='background:" . $settings_bg_submit . ";color:" . $settings_color_submit . ";'>
                                                " . $settings_labelSubmit . "
                                            </a>
                                        </p>
                                    </div>
                                    </div>
                                    <script type='text/javascript'>
                                        document.body.className += ' ' + 'stopScrolling';
                                    </script> ";
                    $html .= "<style>";
                    if(isset($settings['customCss'])) $html .= $settings['customCss'];
                    if(isset($settings['av_layout'])) $av_layout = $settings['av_layout'];
                    else $av_layout = 1;
                    if ($av_layout == 1) {
                        $html .= ".overlay_page{background:" . $bg_wr . ";}";
                    } else {
                        $html .= ".overlay_page{background-image:url(" . $settings_popup_bg . ");background-position: center;background-repeat: no-repeat;background-size: cover;}";
                    }
                    $html .= ".overlay_page h1{color:" . $settings_title_color . ";}";
                    $html .= "</style>"; 
                } 
        } 
        
        $response = array(
            "html" => $html, 
        );
        echo json_encode($response);
    }
    if($action == "checkDetailproduct"){ 
        $shopify = shopifyInit($db, $shop, $appId);
        if(isset($_GET['id']))  $product_id = $_GET['id']; 
       
        $list_collection_by_productID = $shopify("GET", "/admin/api/2020-01/custom_collections.json?product_id={$product_id}"); 
        $list_collection_bloecked = [];
        $list_collection_bloecked = db_fetch_row("select collection_blocked from region_settings where shop = '$shop'"); 
        if(is_array($list_collection_by_productID)){
            foreach($list_collection_by_productID as $v){
                $collection_id = $v['id'];
                $check_collection_block = check_block_collection($collection_id,$shop,$list_collection_bloecked['collection_blocked']);
                if($check_collection_block == 1){
                    $settings = getShopSettings($db, $shop);
                    $html = "";
                    // check blacklist or white list 
                    $html .= getLayout($db, $shop,$settings);
                    $response = array(
                        "html" => $html,
                    );
                    echo json_encode($response);
                    break;
                }
            } 
        }
    }
    if($action == "checkCollectionproduct"){
        $shopify = shopifyInit($db, $shop, $appId);
        if(isset($_GET['collection_id']))  $collection_id = $_GET['collection_id']; 
        else $collection_id = null;
        $list_collection_bloecked = db_fetch_row("select collection_blocked from region_settings where shop = '$shop'");
        $check_collection_block = check_block_collection($collection_id,$shop,$list_collection_bloecked['collection_blocked']);
        if($check_collection_block == 1){
            $settings = getShopSettings($db, $shop);
            $html = "";
            // check blacklisst or white list  
            $location = getShopLocation($settings, $db, $shop); 
            $html .= getLayout($db, $shop,$settings);
            $response = array(
                "html" => $html,
            );
            echo json_encode($response);
        }
    }
}
function getLayout($db, $shop,$settings){
    $html = "";
    $location = getShopLocation($settings, $db, $shop); 
    if ($location == 1) {
      updateStatic($db, $shop);
      if ($settings["logo"] != '')
          $logo = "<img id='region_logo' src='" . $settings["logo"] . "'>";
      else
          $logo = "";
      $html .= "<div class='main-content1' id='MainContent' role='main'>
                      <div class='overlay_page text-center'>
                          " . $logo . "
                          <h1>" . $settings['title'] . "</h1>
                          <p style='color:" . $settings['sub_color'] . ";'>" . $settings['subTitle'] . "</p>
                          <p>
                              <a href='" . $settings['title_logo'] . "' class='btn btn--has-icon-after' style='background:" . $settings['bg_submit'] . ";color:" . $settings['color_submit'] . ";'>
                                  " . $settings['labelSubmit'] . "
                              </a>
                          </p>
                      </div>
                      </div>
                      <script type='text/javascript'>
                          document.body.className += ' ' + 'stopScrolling';
                      </script> ";
      $html .= "<style>";
      if ($settings['av_layout'] == 1) {
          $html .= ".overlay_page{background:" . $settings['bg_wr'] . ";}";
      } else {
          $html .= ".overlay_page{background-image:url(" . $settings['popup_bg'] . ");background-position: center;background-repeat: no-repeat;background-size: cover;}";
      }
      $html .= ".overlay_page h1{color:" . $settings['title_color'] . ";}";
      $html .= "</style>";
  }
  return $html;
}
function check_block_collection($collection_id,$shop,$list_collection_bloecked){ 
    $result = 0;
    $list_collection_bloecked = json_decode($list_collection_bloecked);
    if(is_array($list_collection_bloecked)){
        foreach($list_collection_bloecked as $v){
            if($v->id == $collection_id){
               return $result= 1;
            }
        }
    } 
    return $result;
}
function get_if() { 
    $ip = $_SERVER['REMOTE_ADDR']; 
    $userInfo = IpToInfo($ip);  
    $userInfo['ip'] = $ip;
    return $userInfo;
}

function getShopLocation($settings, $db, $shop) {
    $information = get_if();
    $blacklist = get_list_block($db, $shop, 1);
    $whitelist = get_list_block($db, $shop, 2); 
    if(!isset($information['ip'])) return 0; 
    $ip   = $information['ip'];
    $city = $information['region_name'];
    $code = $information['country_code'];
    $zipcode = $information['zip_code'];
    $city_name = $information['city_name'];

    if(isset($settings['block_all'])) $block_all = $settings['block_all'];
    else $block_all = 0;
    if ($block_all == 1) {
        $check_black = check_ip_in_array($blacklist, $ip, $city,$code,$zipcode,$city_name);
        $check_white = check_ip_in_array($whitelist, $ip, $city, $code,$zipcode,$city_name);
        if ($check_white == 1 || $check_black == 1) {
                $result = 0;
        } else {
                $result = 1;
        }
    } else {
        if(isset($settings['block_list'])) $block_list = $settings['block_list'];
        else $block_list = 0;
        if ($block_list == 1) {  
            $check_white = check_ip_in_array($whitelist, $ip, $city, $code,$zipcode,$city_name);
            if($check_white == 1){
                return 0;
            }
            $result = check_ip_in_array($blacklist, $ip, $city, $code,$zipcode,$city_name);
         } else {  
            $check_white = check_ip_in_array($whitelist, $ip, $city, $code,$zipcode,$city_name);
             if ($check_white == 1) {
                $result = 0;
            } else {
                 $result = 1;
            }
        }
    } 
    return $result;
} 
function check_ip_in_array($list, $ip, $city, $code,$zipcode,$city_name) { 
    $result = 0; 
    foreach ($list as $v) {
        $v['name_location'] = str_replace(" ", "", $v['name_location']);
        $array_location = explode(",", $v['name_location']);
         if (in_array($ip, $array_location) || in_array($code, $array_location) || in_array($city, $array_location) || in_array($zipcode, $array_location) || in_array($city_name, $array_location)) {
            return $result = 1;
        } else {
            $result = 0;
        }
    } 
    return $result;
} 
function updateStatic($db, $shop) {  
    $information = get_if();
    $ip = $information['ip'];
    $code = $information['country_code'];  
    $block = get_block($ip, $code, $db, $shop); 
    if (empty($block)) {
        $data = array(
            'name_location' => $ip,
            'code' => $code,
            'total' => 1,
            'time' => date('Y-m-d', time()),
            'shop' => "$shop",
        );
        db_insert('region_statistic', $data);
    } else {
        $data = array(
            'total' => ++$block['total'],
            'time' => date('Y-m-d', time())
        ); 
        db_update('region_statistic', $data, "shop = '$shop' and name_location = '$ip' ");
    }
}
function get_list_block($db, $shop, $status) {
    $result = db_fetch_array("SELECT name_location FROM region_location WHERE shop = '$shop' and status_location = '$status'");
    return $result;
}
function get_block($ip, $code, $db, $shop) {
    $sql = "select * from region_statistic where shop = '$shop' and name_location = '$ip' or name_location = '$code'  ";
    $query = $db->query($sql);
    $settings = array();
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $settings = $row;
        }
    }
    return $settings;
}
function getShopSettings($db, $shop) {
    $sql = "select * from region_settings where shop = '$shop'";
    $query = $db->query($sql);
    $settings = array();
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $settings = $row;
        }
    }
    return $settings;
} 
function testInputData($data) {
    $data = trim($data);
    $data = htmlspecialchars($data);
    $data = addslashes($data);

    return $data;
}
