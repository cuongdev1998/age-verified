<?php  
ini_set('display_errors', TRUE);
error_reporting(E_ALL); 
date_default_timezone_set('UTC');
require 'vendor/autoload.php'; 
use sandeepshetty\shopify_api; 
 require 'help.php'; 
if (isset($_GET["action"])) {
    $action = $_GET["action"];
    $shop = $_GET["shop"]; 
    if ($action == "saveNewLocation") {
        $location = $_GET["location"];
        $data = array(
            'name_location'   => $location['name_location'],
            'status_location' => $location['status_location'],
            'shop'            => $shop,
        );
        $respone = db_insert('region_location', $data); 
    }
    if ($action == "saveSetting") {
        $settings = $_GET["settings"]; 
        if(!isset($settings["collection_blocked"])) $settings["collection_blocked"] = [];
        $data = array(
            'block_all'           => $settings["block_all"],
            'av_layout'           => $settings["av_layout"], // logo color
            'title'               => $settings["title"],
            'subTitle'            => $settings["subTitle"],
            'popup_color'         => $settings["popup_color"],
            'labelSubmit'         => $settings["labelSubmit"],
            'sub_color'           => $settings["sub_color"],
            'title_logo'          => $settings["title_logo"],
            'title_color'         => $settings["title_color"],
            'color_submit'        => $settings["color_submit"],
            'bg_submit'           => $settings["bg_submit"],
            'bg_wr'               => $settings["bg_wr"],
            'block_list'          => $settings["block_list"],
            'logo'                => $settings["logo"],
            'popup_bg'            => $settings["popup_bg"],
            'block_for_collection'=> $settings["block_for_collection"],
            'customCss'           => $settings["customCss"],
            'enableRedirect'      =>($settings["enableRedirect"] == "true")? 1 : 0,
            'collection_blocked'  => json_encode($settings["collection_blocked"]),
            'enableApp'  => $settings["enableApp"],
        ); 
        db_update('region_settings', $data, "shop = '$shop'");
    }
    if($action == "getAllCollection"){ 
        $shopify = shopifyInit($db, $shop, $appId); 
        $custom_collections = $shopify("GET", "/admin/api/2020-01/custom_collections.json?fields=id,title&limit=250");
        $smart_collection = $shopify("GET", "/admin/api/2020-01/smart_collections.json?fields=id,title&limit=250");
        $collections = array_merge($custom_collections,$smart_collection);
        echo json_encode($collections);
    }
    if ($action == "getSettings" || $action == "getUploadSettings") {
        $settings = getShopSettings($db, $shop); 
        echo json_encode($settings);
    } 
  
    if ($action == "get_blocked") {
        $condition = "";
        if (isset($_GET['condition']))  $condition = $_GET['condition']; 
        $result = get_blocked($db, $shop, $condition);
        echo json_encode($result); 
    }
    if ($action == "getListBlocked") {
        if (isset($_GET['condition'])) {
            $condition = $_GET['condition'];
        } else {
            $condition = "";
        }
        $result = getListBlocked($db, $shop, $condition);
        echo json_encode($result);
    }
    if ($action == "get_blocked_chart") {
        $result = get_blocked_chart($db, $shop);
        echo json_encode($result);
    }
    if ($action == "get_contries") {
        $result = get_contries($db);
        echo json_encode($result);
    }
    if ($action == "deleteLocation") {
        $id = $_GET["id_location"];
        db_delete('region_location', "`shop` = '$shop' and `id_location` = '$id'");
    }
    if ($action == "deleteBlock") {
        $id = $_GET["id"];
        db_delete('block', "`shop` = '$shop' and `id` = '$id'");
    }
    if ($action == "publishLocation") {
        $id = $_GET["id"];
        $data = array(
            'status_location' => '1'
        );
        db_update('region_location', $data, "id_location = '$id' and shop = '$shop'");
    }
    if ($action == "unpublishLocation") { 
        $id = $_GET["id"];
        $data = array(
            'status_location' => '2'
        );
        db_update('region_location', $data, "id_location = '{$id}' and shop = '$shop'");
    }
    if ($action == "editLocation") {
        $name_location = $_GET["name_location"];
        $id = $_GET["id"];
        $data = array(
            'name_location' => $name_location,
        );
        db_update('region_location', $data, "id_location = '{$id}' and shop = '$shop'");
    }
    if ($action == "blockLocation") {
        $id = $_GET["id"];
        $data = array(
            'block' => '1'
        );
        db_update('region_location', $data, "id_location = '$id' and shop = '$shop'");
    }
    if ($action == "unblockLocation") {
        $id = $_GET["id"];
        $data = array(
            'block' => '0'
        );
        db_update('region_location', $data, "id_location = '{$id}' and shop = '$shop'");
    }

    if ($action == "getListLocation") {
        $result = getLocation($db, $shop);
        echo json_encode($result);
    }
    if ($action == "getlocalbyId") {
        $id = $_GET["id"]; 
        $resul = getlocalbyID($id, $shop, $db);
        echo json_encode($resul);
    } 
}

if (isset($_FILES['popup_bg']['name']) || isset($_FILES['logo']['name'])) {
 
    $shop = $_POST['shop'];
    if (isset($_FILES['popup_bg']['name'])) {
        $filename = $_FILES['popup_bg']['name'];
        $tmpname  = $_FILES['popup_bg']['tmp_name'];
        $db_name  = 'popup_bg';
    } else {
        $filename = $_FILES['logo']['name'];
        $tmpname  = $_FILES['logo']['tmp_name'];
        $db_name  = 'logo';
    }
    $target_dir = "upload/";
    $target_file = $target_dir . basename($filename);
    move_uploaded_file($tmpname, $target_file);
    $shopify = shopifyInit($db, $shop, $appId);  
    $rand = rand(0, 10000);
    $imgUrl = $rootLink . '/' . $target_file;
    $themeId = $shopify('GET', '/admin/api/2020-01/themes.json', array('role' => 'main', 'fields' => 'id'));
    $result = $shopify('PUT', '/admin/api/2020-01/themes/' . $themeId[0]['id'] . '/assets.json', array('asset' => array('key' => 'assets/' . $rand . '_' . $filename . '', 'src' => $imgUrl)));
    unlink($target_file);
    $url = $result['public_url'];
    echo json_encode($url);
    $db->query("UPDATE region_settings SET $db_name='$url' WHERE shop = '$shop'");
} 
function getCollectionChoosen($shop){
    $collectionChoosen = db_fetch_row("select collection_blocked from region_collection where shop = '$shop'");
    return json_decode($collectionChoosen['collection_blocked']);
}
function getListBlocked($db, $shop, $condition) {
    $time = strtotime(date("y-m-d", time()));
    $listBlock = db_fetch_array("SELECT * FROM region_statistic WHERE shop = '$shop'");
    $result = array();
    if ($condition != null) {
        foreach ($listBlock as $k => &$v) {
            $compareTime = strtotime($v['time']);
            $subTime = (($time - $compareTime) / 86400);
            if ($condition == 0) {
                if ($subTime <= (int) $condition) {
                    array_push($result, $v);
                }
            } else {
                if ($subTime <= (int) $condition & $subTime > 0) {
                    array_push($result, $v);
                }
            }
        }
        return $result;
    } else {
        return $listBlock;
    }
}
function get_blocked($db, $shop, $condition) {
    $time = strtotime(date("y-m-d", time()));
    $listBlock = db_fetch_array("SELECT * FROM region_statistic WHERE shop = '$shop'");
    $result = array();
    if ($condition != null) {
        foreach ($listBlock as $k => &$v) {
            $compareTime = strtotime($v['time']);
            $subTime = (($time - $compareTime) / 86400);
            if ($condition == 0) {
                if ($subTime <= (int) $condition) {
                    array_push($result, $v);
                }
            } else {
                if ($subTime <= (int) $condition & $subTime > 0) {
                    array_push($result, $v);
                }
            }
        }
        return $result;
    } else {
        $result['labelForStatistic'] = array();
        $result['countForStatistic'] = array();
        foreach ($listBlock as $v) {
            array_push($result['labelForStatistic'], $v['name_location']);
            array_push($result['countForStatistic'], $v['total']);
        }
        return $result;
    }
}
// check tên file trước khi upload
function FixSpecialChars($text) {
    $map = array(array("\ufffd", ""),
        array(" ", ""),
        array(" ", "_"),
        array("&", "_"),
        array("!", "_"),
        array("%", "_"),
        array("#", "_"),
        array("@", "_"),
        array("%", "_"),
        array("^", "_"),
        array("*", "_"),
        array("(", "_"),
        array(")", "_"),
        array("+", "_"),
        array("=", "_"),
        array("~", "_"),
        array("?", "_"),
        array("<", "_"),
        array(">", "_"),
        array(" ", "")
    );
    if (is_array($map)) {
        foreach ($map as $pair)
            $text = str_replace($pair[0], $pair[1], $text);
    }
    return $text;
}

function getBlockAll($db, $shop) {
    $sql = "SELECT * FROM region_location WHERE shop = '$shop' and name_location = 'all'";
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
    $result = db_fetch_row("SELECT * FROM region_settings WHERE shop = '$shop'");
    $result['collection_blocked'] = json_decode($result['collection_blocked']);
    if(!is_array($result['collection_blocked'])) $result['collection_blocked']= [];
    foreach($result['collection_blocked'] as &$v){
        $v = cvf_convert_object_to_array($v);
    }
    $settings = $result;
    return $settings;
}
function get_blocked_chart($db, $shop) {
    $listBlock = db_fetch_array("SELECT * FROM region_statistic WHERE shop = '$shop'");
    return $listBlock;
} 
 
function get_contries($db) {
    $sql = "SELECT * FROM countries";
    $query = $db->query($sql);
    while ($row = mysqli_fetch_assoc($query)) {
        $result[] = $row;
    }
    return $result;
}
function getLocation($db, $shop) {
    $result = db_fetch_array("SELECT * FROM region_location WHERE shop = '$shop' ");
    return $result;
}
function getlocalbyID($id, $shop, $db) {
    $sql = "SELECT * FROM region_location WHERE shop = '$shop' and id_location = '$id'";
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
