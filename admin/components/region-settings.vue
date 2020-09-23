<template>
  <div class="row">
    <div class="col-md-5 col-xs-12">
      <div class="style_theme">
        <div class="tab-content col-xs-12">
          <ul class="nav nav-tabs">
            <li class="active">
              <a data-toggle="tab" href="#content">Content</a>
            </li>
            <li>
              <a data-toggle="tab" href="#style">Style</a>
            </li>
            <li>
              <a data-toggle="tab" href="#uploadImages">Upload Images</a>
            </li>
          </ul>
          <div id="content" class="tab-pane in active">
            <div class="contentSettings bg_white">
              <span class="cartTitle">CUSTOM CONTENT
                <hr>
              </span>
              <div class="form-group col-xs-12">
                <label class="col-sm-3">Enable app</label>
                <div class="col-sm-9">
                  <md-switch class="md-primary" v-model="settings.enableApp"></md-switch>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label class="col-sm-3">Block for collection</label>
                <div class="col-sm-9">
                  <md-switch class="md-primary" v-model="settings.block_for_collection"></md-switch>
                </div>
              </div>
              <div class="form-group col-xs-12" v-if="settings.block_for_collection == true">
                <div class="choosenCollection">
                  <p>Select the collection you want to block</p>
                  <multiselect
                    :options="allCollection"
                    v-model="settings.collection_blocked"
                    :multiple="true"
                    label="title"
                    track-by="title"
                    placeholder="Search"
                  ></multiselect>
                </div>
              </div>
              <div class="form-group col-xs-12" style="displa:none;">
                <label class="col-sm-3">Block all</label>
                <div class="col-sm-9">
                  <md-switch class="md-primary" v-model="settings.block_all"></md-switch>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label class="col-sm-3">Enable redirect without popup</label>
                <div class="col-sm-9">
                  <md-switch class="md-primary" v-model="settings.enableRedirect"></md-switch>
                </div>
              </div>
              <div class="form-group col-xs-12" v-if="settings.block_all == false">
                <label class="col-sm-3">Matching rule</label>
                <div class="col-sm-9">
                  <select class="form-control" v-model="settings.block_list">
                    <option value="1">BlackList</option>
                    <option value="2">WhiteList</option>
                  </select>
                </div>
              </div>

              <div class="form-group col-xs-12">
                <label class="col-sm-3">Title header</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" v-model="settings.title">
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label class="col-sm-3">Title describe</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" v-model="settings.subTitle">
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label class="col-sm-3">Link redirect</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" v-model="settings.title_logo">
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label class="col-sm-3">Label Submit</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" v-model="settings.labelSubmit">
                </div>
              </div>
              <div class="form-group col-xs-12">
                <div class="col-sm-9 col-sm-offset-3">
                  <button class="btn btn-primary saveSettings" v-on:click="saveSettings">Save</button>
                </div>
              </div>
            </div>
          </div>
          <div id="style" class="tab-pane fade">
            <div class="styleSettings bg_white">
              <span class="cartTitle">AVANCE STYLE
                <hr>
              </span>
              <div class="form-group col-xs-6">
                <label class="col-sm-6 control-label">Color text</label>
                <div class="col-sm-6">
                  <input type="color" v-model="settings.title_color">
                </div>
              </div>

              <div class="form-group col-xs-6">
                <label class="col-sm-6 control-label">Color sub text</label>
                <div class="col-sm-6">
                  <input type="color" v-model="settings.sub_color">
                </div>
              </div>
              <div class="form-group col-xs-6">
                <label class="col-sm-6 control-label">Background Wrapper</label>
                <div class="col-sm-6">
                  <input type="color" v-model="settings.bg_wr">
                </div>
              </div>
              <div class="form-group col-xs-6">
                <label class="col-sm-6 control-label">Background Submit</label>
                <div class="col-sm-6">
                  <input type="color" v-model="settings.bg_submit">
                </div>
              </div>
              <div class="form-group col-xs-6">
                <label class="col-sm-6 control-label">Color Submit</label>
                <div class="col-sm-6">
                  <input type="color" v-model="settings.color_submit">
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label class="col-sm-4 control-label">Custom Css</label>
                <div class="col-sm-8">
                  <textarea
                    rows="4"
                    class="form-control"
                    cols="50"
                    type="color"
                    v-model="settings.customCss"
                  ></textarea>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <div class="col-sm-9 col-sm-offset-3">
                  <button class="btn btn-primary saveSettings" v-on:click="saveSettings">Save</button>
                </div>
              </div>
            </div>
          </div>
          <div id="uploadImages" class="tab-pane fade bg_white">
            <span class="cartTitle">UPLOAD IMAGES
              <hr>
            </span>

            <div class="form-group col-xs-12" style="margin-left: 17px;">
              <div class="file-upload-form">
                <label>Logo Images:</label>
                <input
                  type="file"
                  id="logo"
                  name="logo"
                  @change="previewImageLogo(event)"
                  accept="image/*"
                >
              </div>
              <img class="preview" :src="settings.logo" width="30%">
              <input type="hidden" v-model="settings.logo">
            </div>

            <div class="form-group col-xs-12">
              <label class="col-sm-3">Select your layout</label>
              <div class="col-sm-9">
                <select class="form-control" v-model="settings.av_layout">
                  <option value="1">Transparent</option>
                  <option value="2">Background</option>
                </select>
              </div>
            </div>
            <div class="form-group col-xs-12" v-if="settings.av_layout == 2">
              <div class="form-group col-xs-12">
                <div class="file-upload-form">
                  <label>Background Images:</label>
                  <input
                    type="file"
                    name="popup_bg"
                    id="popup_bg"
                    @change="previewImageBg(event)"
                    accept="image/*"
                  >
                </div>
                <img class="preview" :src="settings.popup_bg" width="30%">
                <input type="hidden" v-model="settings.popup_bg">
              </div>
            </div>
            <div class="form-group col-xs-12">
              <div class="col-sm-9 col-sm-offset-3">
                <button class="btn btn-primary saveSettings" v-on:click="saveSettings">Save</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-7 col-xs-12">
      <ul class="listPreview">
        <li @click="changePreview(false)">
          <a class="laptop">
            <span class="glyphicon glyphicon-blackboard"></span>Desktop
          </a>
        </li>
        <li @click="changePreview(true)">
          <a class="phone active">
            <span class="glyphicon glyphicon-phone"></span>Mobile
          </a>
        </li>
        <div class="clear_fix"></div>
      </ul>
      <div class="preview_phone" v-if="showPreview == true">
        <div class="wrapper_phone">
          <div
            class="content_phone"
            v-if="settings.av_layout == 1"
            :style="'background-color: ' + settings.bg_wr"
          >
            <h1 :style="'color: ' + settings.title_color">{{settings.title}}</h1>
            <img v-if="settings.logo != ''" alt width="30%" :src="settings.logo">
            <small :style="'color: ' + settings.sub_color">{{settings.subTitle}}</small>
            <p>
              <a
                href
                target="_blank"
                class="btn btn--has-icon-after"
                v-bind:style="{color:settings.color_submit,background:settings.bg_submit}"
              >{{settings.labelSubmit}}</a>
            </p>
          </div>
          <div
            class="content_phone"
            v-if="settings.av_layout == 2"
            :style="'background:url(' + settings.popup_bg+')no-repeat'"
          >
            <h1 :style="'color: ' + settings.title_color">{{settings.title}}</h1>
            <img v-if="settings.logo != ''" alt width="30%" :src="settings.logo">
            <small :style="'color: ' + settings.sub_color">{{settings.subTitle}}</small>
            <p>
              <a
                href
                target="_blank"
                class="btn btn--has-icsson-after"
                v-bind:style="{color:settings.color_submit,background:settings.bg_submit}"
              >{{settings.labelSubmit}}</a>
            </p>
          </div>
        </div>
      </div>
      <div class="next-card preview" v-if="showPreview == false">
        <div class="geoip-top-banner" style="width: 100%; position: absolute;">
          <div class="geoip-main-block" :style="'background-color: ' + settings.bg_wr">
            <span class="geoip-text-block">
              <h1 :style="'color: ' + settings.title_color">{{settings.title}}</h1>
              <img v-if="settings.logo != ''" alt width="30%" :src="settings.logo">
              <small :style="'color: ' + settings.sub_color">{{settings.subTitle}}</small>
              <p>
                <a
                  href
                  target="_blank"
                  class="btn btn--has-icon-after"
                  v-bind:style="{color:settings.color_submit,background:settings.bg_submit}"
                >{{settings.labelSubmit}}</a>
              </p>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
var shop = $(".shopName").text();
const rootLink = window.rootLink;
module.exports = {
  // bien truyen tu component cha
  props: [],
  data: function() {
    return {
      // khai bao bien
      settings: [],
      allCollection: [],
      showPreview: true
    };
  },
  mounted: function() {
    this.getSettings();
    this.getAllCollection();
  },
  methods: {
    getAllCollection: function() {
      let self = this;
      var data = { action: "getAllCollection", shop: shop };
      $.ajax({
        url: "services.php",
        type: "GET",
        data: data
      }).done(result => {
        if (typeof result == "string") {
          result = JSON.parse(result);
        }
        self.allCollection = result;
      });
    },
    saveCollectionBlocked: function() {
      var seft = this;
      $.ajax({
        url: "services.php",
        type: "GET",
        data: {
          shop: shop,
          collectionChoosen: seft.collectionChoosen,
          action: "saveCollectionBlocked"
        }
      }).done(result => {
        this.$toasted.show("Save success!", {
          type: "success",
          duration: 3000
        });
      });
    },
    getSettings: function() {
      $.ajax({
        url: "services.php",
        type: "GET",
        data: { action: "getSettings", shop: shop }
      }).done(result => {
        if (typeof result == "string") {
          result = JSON.parse(result);
        }
        this.settings = result;
        
        result.block_all == "1"
          ? (this.settings.block_all = true)
          : (this.settings.block_all = false);
          result.enableApp == "1"
          ? (this.settings.enableApp = true)
          : (this.settings.enableApp = false);
          result.enableRedirect == "1"
          ? (this.settings.enableRedirect = true)
          : (this.settings.enableRedirect = false);
        result.block_for_collection == "1"
          ? (this.settings.block_for_collection = true)
          : (this.settings.block_for_collection = false);
      });
    },
    changePreview: function(showPreview) {
      this.showPreview = showPreview;
      if (showPreview == true) {
        $(".phone").addClass("active");
        $(".laptop").removeClass("active");
      } else {
        $(".phone").removeClass("active");
        $(".laptop").addClass("active");
      }
    },
    saveSettings: function() {
      $(".saveSettings").attr("disabled", true);
      $(".saveSettings").empty();
      $(".saveSettings").text("Saving...");
      this.settings.block_all == true
        ? (this.settings.block_all = 1)
        : (this.settings.block_all = 0);
      this.settings.block_for_collection == true
        ? (this.settings.block_for_collection = 1)
        : (this.settings.block_for_collection = 0);
      this.settings.enableApp == true
      ? (this.settings.enableApp = 1)
      : (this.settings.enableApp = 0);
      $.ajax({
        url: "services.php",
        type: "GET",
        data: { action: "saveSetting", shop: shop, settings: this.settings }
      }).done(result => {
        $(".saveSettings").attr("disabled", false);
        $(".saveSettings").empty();
        $(".saveSettings").text("Save");
        this.getSettings();
        this.$toasted.show("Save success!", {
          type: "success",
          duration: 3000
        });
      });
    },
    previewImageLogo: function(event) {
      var fd = new FormData();
      var popup_bg = document.getElementById("logo").files[0];
      var fsize = document.getElementById("logo").files[0].size;
      fd.append("logo", popup_bg);
      fd.append("shop", shop);
      console.log("abcvd", fd);
      axios.post("services.php", fd).then(result => {
        if (typeof result == "string") {
          result = JSON.parse(result);
        }
        this.settings.logo = result.data;
      });
    },
    previewImageBg: function(event) {
      var fd = new FormData();
      var popup_bg = document.getElementById("popup_bg").files[0];
      var fsize = document.getElementById("popup_bg").files[0].size;
      fd.append("popup_bg", popup_bg);
      fd.append("shop", shop);
      axios.post("services.php", fd).then(result => {
        if (typeof result == "string") {
          result = JSON.parse(result);
        }
        this.settings.popup_bg = result.data;
      });
    }
  },
  components: {
    Multiselect: window.VueMultiselect.default
  }
};
</script>
 
<style>
</style>

