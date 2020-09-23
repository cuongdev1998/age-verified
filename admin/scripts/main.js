var shop = window.shop; 
 Vue.use(VueMaterial.default)  
 Vue.use(Toasted)
new Vue({
    el: '#region-restrictions',
    components: {
        Multiselect: window.VueMultiselect.default,
        'region-addip' : httpVueLoader(`${window.rootLink}/admin/components/region-addip.vue?v=1`), 
        'region-settings' : httpVueLoader(`${window.rootLink}/admin/components/region-settings.vue?v=3`), 
        'region-statistic' : httpVueLoader(`${window.rootLink}//admin/components/region-statistic.vue`), 
    },
    data : function () {
        return {   
            showAdd:true,
            showStatistic:true,
            showSettings:true, 
        }
    }, 
    mounted:async function() { 
       this.changeTab("add-region")
     }, 
    methods : {  
        changeTab (tabId) {
            let listTabs = ["add-region", "settings", "statistic"];
            let listConditions = ["showAdd", "showSettings", "showStatistic"];
            for (let i = 0; i < listTabs.length; i++) {
                if (listTabs[i] == tabId) {
                    this[listConditions[i]] = true;
                } else {
                    this[listConditions[i]] = false;
                }
            }
        }
    }, 
}) 