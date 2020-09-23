<?php 
header('Set-Cookie: cross-site-cookie=name; SameSite=None; Secure');
require 'vendor/autoload.php'; 
use sandeepshetty\shopify_api; 
require 'conn-shopify.php'; 
session_start(); 
if (isset($_GET["shop"])) $shop = $_GET["shop"]; 
$shop_data = $db->query("select * from tbl_usersettings where store_name = '" . $shop . "' and app_id = $appId");
$shop_data = $shop_data->fetch_object();
$installedDate = $shop_data->installed_date;
$confirmation_url = $shop_data->confirmation_url;
$clientStatus = $shop_data->status;
//$date1 = new DateTime($installedDate);
//$date2 = new DateTime("now");
//$interval = date_diff($date1, $date2);
//$diff = (int) $interval->format('%R%a');
if ($clientStatus != 'active') {
    header('Location: ' . $rootLink . '/chargeRequire.php?shop=' . $shop);
} else { 
    $select_settings = $db->query("SELECT * FROM tbl_appsettings WHERE id = $appId");
    $app_settings = $select_settings->fetch_object();
    $shop_data = $db->query("select * from tbl_usersettings where store_name = '" . $shop . "' and app_id = $appId");
    $shop_data = $shop_data->fetch_object();
    $shopify = shopify_api\client(
            $shop, $shop_data->access_token, $app_settings->api_key, $app_settings->shared_secret
    );
    $shopInfo = $shopify("GET", "/admin/shop.json");
    ?> 
    <head>
        <title>REGION RESTRICTIONS ADMIN</title>
       
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="admin/lib/bootstrap-vue.css">
        <link rel="stylesheet" href="admin/lib/bootstrap.min.css">
        <link rel="stylesheet" href="admin/lib/vue-toasted.min.css">  
        <link rel="stylesheet" href="admin/lib/vue-multiselect.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"> 
        <link rel="stylesheet" href="admin/lib//vue-multiselect.min.css">
        <link rel="stylesheet" href="admin/lib/vue-material.min.css">
        <link rel="stylesheet" type="text/css" href="admin/styles/styles.css">
        <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script src="admin/lib/jquery.min.js"></script>
        <script type="text/javascript">
        ShopifyApp.init({
            apiKey: '<?php echo $appId; ?>',
            shopOrigin: 'https://<?php echo $shop; ?>',
        });
        ShopifyApp.ready(function () {
            ShopifyApp.Bar.initialize({});
        });
        </script>	  
    </head>
    <body> 
        <?php
        if ($clientStatus != 'active') {
            $dayLeft = $trialTime - $diff;
            echo '<a href="' . $confirmation_url . '" style="position: absolute; right: 0;border: 1px solid red; padding: 5px;color: #ddd" target="_blank" class="refreshCharge">' . $dayLeft . ' days left<div>Buy now</div></a>';
        }
        ?> 
        <!-- review -->
        <?php require 'admin/review/star.php'; ?> 

        <span style="display: none" class="shopName"><?php echo $shop; ?></span>
        <div layout-padding style="width: 100%">
            <script>
                // CONST
                window.rootLink = "<?php echo $rootLink; ?>";
                window.shop = "<?php echo $shop; ?>";
            </script> 
            <div id="region-restrictions">
                 <md-tabs md-sync-route class="md-transparent" md-alignment="centered"  @md-changed="changeTab">
                    <md-tab id="add-region" md-label="Add Region">
                        <region-addip  v-if="showAdd == true"></region-addip>
                    </md-tab>

                    <md-tab id="settings" md-label="Settings">
                        <region-settings v-if="showSettings == true"></region-settings>
                    </md-tab>

                    <md-tab id="statistic" md-label="Statistic"> 
                        <region-statistic v-if="showStatistic == true"></region-statistic>
                    </md-tab>  
                </md-tabs>
            </div> 
        </div> 
		
            <!-- end facebookReviewsApp -->
			<?php include 'facebook-chat.html'; ?>

			
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-126587266-1"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'UA-126587266-1');
		</script>
		<?php include 'google_remarketing_tag.txt'; ?>		
		
        <!-- Vue Material Dependencies -->  
        <script src="admin/lib/bootstrap.min.js"></script>
        <script src="admin/lib/jquery.min.js"></script>  
        <script src="admin/lib/vue.min.js"></script>
        <script type="text/javascript" src="admin/lib/httpVueLoader.js"></script>
         <script src="admin/lib/vue-material.min.js"></script>
        <script src="admin/lib/vue-toasted.min.js"></script>
        <script src="admin/lib/Chart.min.js"></script>
        <script src="admin/lib/vue-chartjs.min.js"></script>
        <script src="admin/lib/axios.min.js"></script>
        <script src="admin/lib//bootstrap-vue.js"></script>
        <script src="admin/lib/vue-multiselect.min.js"></script>
        <script src="admin/scripts/main.js?v=3"></script>
		<?php //include 'notificationNewYear.php'; ?>
     </body>
<?php } ?>

