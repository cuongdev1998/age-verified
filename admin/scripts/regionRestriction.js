var shop = $('.shopName').text();
Vue.use(Toasted);
new Vue({
    el: '#omegaVue',
    components: {
        Multiselect: window.VueMultiselect.default,
    },
    data: {
        newlocation: {},
        fields: {
            name_location: {
                label: 'LOCATION',
            },
            status_location: {
                label: 'STATUS LOCATION', 
                class: 'text-center'
            },
            actions: {
                label: 'Actions',
                class: 'actions'
            },
        },
        image: '',
        currentPage: 1,
        perPage: 5,
        filter: null,
        listLocation: [],
        fields_blocked: {
            name_location: {
                label: 'IP Blocked',
            },
            code: {
                label: "Country's code",
                class: 'text-center'
            },
            total: {
                label: 'Total access',
                class: 'actions'
            },
            time: {
                label: 'Latest time',
                class: 'actions'
            },
        },
        currentPage_blocked: 1,
        perPage_blocked: 5,
        filter_blocked: null,
        listBlocked: [],
        settings: [],
        showPreview: true
    },
    created: function () {
        this.getBlocked();
        this.getListLocation();
        this.getSettings();
    },
    methods: {
           
        getBlocked: function () {
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: {action: "getListBlocked", shop: shop, condition: null},
            }).done((result) => {
                if (typeof result == "string") {
                    result = JSON.parse(result);
                }
                this.listBlocked = result;
            })
        },
       
        getBlockByCondition: function (condition) {
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: {action: "getListBlocked", shop: shop, condition: condition},
            }).done((result) => {
                if (typeof result == "string") {
                    result = JSON.parse(result);
                }
                this.listBlocked = result;
            })
        },
         
    },

})
var data = {
    count: [],
    background: ['#00695c', '#b71c1c', '#3F729B', '#f4ff81', '#f87979', '#33691e', '#424242'],
    labels: [],
};
Vue.component('pie-chart', {
    data: function () {
        return data;
    },
    extends: VueChartJs.Pie,
    props: [],

    mounted() {
        this.getIPBlocked();
    },
    methods: {
        getIPBlocked: function () {
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: {action: "get_blocked", shop: shop},
            }).done((result) => {
                 if (typeof result == "string") {
                    result = JSON.parse(result);
                }
                data.labels = result.labelForStatistic
                data.count = result.countForStatistic
                this.renderChart({
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Data One',
                            backgroundColor: data.background,
                            data: data.count
                        }
                    ]
                }, {responsive: true, maintainAspectRatio: false});
            })
        },

    }
});