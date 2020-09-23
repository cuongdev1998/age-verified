<template>
  <div class="clear_fix">  
    <div class="clear_fix wrapper_list_block bg_white col-md-6">
        <span class="cartTitle">LIST BLOCKED<hr></span>
        <ul class="fill_day">
            <li class="fill_0"><a @click="getBlockByCondition(0)">Today</a></li>
            <li class="fill_1"><a @click="getBlockByCondition(1)">Yesterday</a></li>
            <li class="fill_6"><a @click="getBlockByCondition(6)">The last 7 days</a></li>
            <li class="fill_29"><a @click="getBlockByCondition(29)">The last 30 days</a></li>
        </ul>
        <div class="justify-content-centermy-1 filter_blocked">  
            <b-form-input v-model="filter_blocked" placeholder="Enter IP..."></b-form-input>
        </div> 
        <b-table striped hover :items="listBlocked" :fields="fields_blocked" :current-page="currentPage_blocked" :per-page="perPage_blocked" :filter="filter_blocked" >
            <template slot="name_location" scope="item"  width="30%"> 
                {{item.item.name_location}}
            </template> 
            <template slot="code" scope="item">
                {{item.item.code}}
            </template>
            <template slot="total" scope="item">
                {{item.item.total}}
            </template>
            <template slot="time" scope="item">
                {{item.item.time}}
            </template> 
        </b-table>
        <div class="justify-content-center my-1 clear_fix">
            <b-form-select :options="[{text:5,value:5},{text:10,value:10},{text:15,value:15}]" v-model="perPage_blocked" style="float: right;width: 16%;margin-bottom: 25px;"> </b-form-select>
            <b-pagination size="md" :total-rows="listBlocked.length" :per-page="perPage_blocked" v-model="currentPage_blocked"/>
        </div> 
    </div>
    <div class="col-md-6">
        <pie-chart></pie-chart>
    </div>
</div>
</template>
<script>
var shop = $('.shopName').text();
const rootLink = window.rootLink 
module.exports = {
  // bien truyen tu component cha 
  props: [],
  data: function() {
    return {
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
    };
  },
  mounted: function() {
   this.getBlocked();
  },
  methods : {  
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
            var element = $('.fill_day li') 
            element.removeClass('activeDay');
            $('.fill_'+condition).addClass('activeDay'); 
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
  components: {
    }
};
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
</script>
 
<style>


</style>

