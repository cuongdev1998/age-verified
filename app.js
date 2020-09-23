 
shop = Shopify.shop
function parseQuery(query) {
    var Params = new Object();
    if (!query)
        return Params; // return empty object
    var Pairs = query.split(/[;&]/);
    for (var i = 0; i < Pairs.length; i++) {
        var KeyVal = Pairs[i].split('=');
        if (!KeyVal || KeyVal.length != 2)
            continue;
        var key = unescape(KeyVal[0]);
        var val = unescape(KeyVal[1]);
        val = val.replace(/\+/g, ' ');
        Params[key] = val;
    }
    return Params;
} 
if (typeof window.otRegionCheckExistFile === 'undefined') {
    otRegionInit();
    window.otRegionCheckExistFile = false;
} 
function otRegionInit(){
    $("body").append("<div class='ot-redirect'></div>");
    //shop name 
    var dataRR = {};
    dataRR.shop = shop;
    dataRR.action = "getRestrictions"; 
    $.get(`${rootLinkRegion}region.php`, dataRR, function (result) {
        $("head").append("<link rel='stylesheet' type='text/css' href='https://apps.omegatheme.com/region-restrictions/assets/css/region.css' />");
        if (typeof result == "string") {
            result = JSON.parse(result);
        } 
       
        $(".ot-redirect").append(result.html);
    });
    var page = __st.p
    if (page == 'product') {
         jQuery.ajax({
            type: 'GET',
            url: `${rootLinkRegion}region.php`,
            data: {action:"checkDetailproduct",id:meta.product.id,shop:shop},
            dataType: 'json',
            success: function (result) {
                if (typeof result == "string") {
                    result = JSON.parse(result);
                } 
                console.log(result)
                $(".ot-redirect").append(result.html);
            },  
        });
    }
    
    if (page == 'collection') {
         jQuery.ajax({
           type: 'GET',
           url: `${rootLinkRegion}region.php`,
           data: {action:"checkCollectionproduct",collection_id:__st.rid,shop:shop},
           dataType: 'json',
           success: function (result) {
               if (typeof result == "string") {
                   result = JSON.parse(result);
               } 
               console.log(result)
               $(".ot-redirect").append(result.html);
           },  
       });
    }
}
