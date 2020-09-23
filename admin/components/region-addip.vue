<template>
    <!--add regin-->
    <div class=""> 
        <div>
            <md-dialog :md-active.sync="showDialog" class="modalAdd">
                <md-dialog-title>ADD REGION</md-dialog-title> 
                <md-dialog-content>
                    <div class="md-layout"> 
                        <div class="md-layout-item md-size-40 md-small-size-100">  
                            <select v-model="newlocation.status_location" placeholder="Type list" class="form-control">
                                <option value="1">BlackList</option>
                                <option value="2">WhiteList</option> 
                            </select>  
                        </div>
                        <div class="md-layout-item md-size-100 md-small-size-100"> 
                            <label>Location IP, Name City or Country's code!</label>
                            <input v-model="newlocation.name_location" class="form-control" /> 
                        </div> 
                        <p>You can check country's code in 
                            <a href="https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements">here</a>
                        </p> 
                </div> 
                    
                     
                </md-dialog-content> 
                <md-dialog-actions>
                    <md-button class="md-primary" @click="showDialog = false">Close</md-button>
                    <button class="btn btn-primary" v-on:click="saveNewLocation">Save</button>
                </md-dialog-actions>
            </md-dialog>  
        </div> 
         <div v-if="listLocation.length === 0">
            <md-empty-state>
                <p><i class="fa fa-lock"></i></p>
                <strong class="md-empty-state-label">Create region you want to block</strong> 
                <md-button class="md-primary md-raised btn_add_region"  @click="showDialog = true">Add region <i class="fa fa-plus-circle"></i></md-button>
            </md-empty-state>
        </div>
        <div class="wrapper_list_region" v-else>
            <span class="cartTitle">LIST REGION BLOCKED<hr></span> 
            <div class="bg_white listRegion">
                <div class="justify-content-centermy-1">  
                    <b-form-input v-model="filter" placeholder="Enter IP or country code"></b-form-input>
                </div> 
                <!-- Main table element -->
                <b-table striped hover :items="listLocation" :fields="fields" :current-page="currentPage" :per-page="perPage" :filter="filter" >
                        <template slot="name_location" scope="item"  width="30%"> 
                        <p @click="showEditLocation(item.item.id_location)" v-bind:id="getClassName(item.item.id_location)"> {{item.value}} <small><i class="fa fa-pencil"></i></small></p>
                        <div class="editLocation"   v-bind:id="getClassEdit(item.item.id_location)">
                            <input type="text" class="form-control inputEdit" v-model="item.value" width="80%">
                            <button class="btn btn-primary btnEdit" @click="editLocation(item.item.id_location,item.value)">Save</button>
                        </div> 
                    </template> 
                    <template slot="status_location" scope="item">
                        <p v-show="item.item.status_location === '2'">
                            <button type="button" title="Change to Black List" @click="publishLocation(item.item.id_location)"   class="btn btn-primary">White List </i></button>
                        </p>
                        <p v-show="item.item.status_location === '1'">
                            <button type="button" title="Change to White List" @click="unpublishLocation(item.item.id_location)"  class="btn btn-warning">Black List</button>
                        </p>
                    </template>  
                    <template slot="actions" scope="item"> 
                        <p>
                            <button type="button" title="Delete Location" class="btn btn-danger" @click="deleteLocation(item.item.id_location)" ><i class="fa fa-trash-o"></i></button>
                        </p>
                    </template>
                </b-table> 
                <!-- pagination -->
                <div class="justify-content-center my-1">
                    <b-form-select :options="[{text:5,value:5},{text:10,value:10},{text:15,value:15}]" v-model="perPage" class="col-md-3" > </b-form-select>
                    <b-pagination size="md" :total-rows="listLocation.length" :per-page="perPage" v-model="currentPage" class="col-md-9 fl-right" />
                </div> 
            </div> 
            <md-button class="md-primary md-raised btn_add_region"  @click="showDialog = true">Add region <i class="fa fa-plus-circle"></i></md-button>
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
      // khai bao bien
      showDialog: false,
      newlocation: {status_location:1},
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
    };
  },
  mounted: function() {
     this.getListLocation();
  },
  methods : {  
       saveNewLocation: function () {
            if (this.newlocation.name_location != "") {
                $.ajax({
                    url: 'services.php',
                    type: 'GET',
                    data: {action: "saveNewLocation", shop: shop, location: this.newlocation}
                }).done((result) => {
                    this.$toasted.show("Save success!", {
                        type: "success",
                        duration: 3000
                    });
                    this.getListLocation();
                    this.showDialog = false
                    this.newlocation= {}
                }).fail((error) => {
                });
            } else {
                this.$toasted.show("Please fill in all fields !", {
                    type: "error",
                    duration: 3000
                });
            }
        },
        getListLocation: function () {
            var data = {action: "getListLocation", shop: shop};
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: data
            }).done((result) => {
                if (typeof result == "string") {
                    result = JSON.parse(result);
                }

                this.listLocation = result;
            })
        },
        deleteLocation: function (id) {
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: {action: "deleteLocation", shop: shop, id_location: id},
            }).done((result) => {
                this.$toasted.show("Delete success!", {
                    type: "success",
                    duration: 3000
                });
                this.getListLocation();
            })
        },
        publishLocation: function (id) {
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: {action: "publishLocation", shop: shop, id: id},
            }).done((result) => {
                this.$toasted.show("Publish success!", {
                    type: "success",
                    duration: 3000
                });
                this.getListLocation();
            })
        },
        unpublishLocation: function (id) {
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: {action: "unpublishLocation", shop: shop, id: id},
            }).done((result) => {
                this.$toasted.show("Unpublish success!", {
                    type: "success",
                    duration: 3000
                });
                this.getListLocation();
            })
        },
        showEditLocation: function (id) {
            $('#nameIP_' + id).hide();
            $('#showEdit_' + id).show();
        },
        editLocation: function (id, value) {
            $.ajax({
                url: 'services.php',
                type: 'GET',
                data: {action: "editLocation", shop: shop, id: id, name_location: value},
            }).done((result) => {
                $('#showEdit_' + id).hide();
                $('#nameIP_' + id).show();
                this.getListLocation();
                this.$toasted.show("Edit success!", {
                    type: "success",
                    duration: 3000
                });
            })
        },
        getClassName: function (id) {
            return 'nameIP_' + id
        },
        getClassEdit: function (id) {
            return 'showEdit_' + id
        },
         
  },
  components: {
    }
};

</script>
 
<style>
.modalAdd{
    background:#fff;
}

</style>

