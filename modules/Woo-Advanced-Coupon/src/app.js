import Vue from "vue/dist/vue.min.js";
window.axios = require("axios");
window.Qs = require('qs');

Vue.component("superdiscount", require("./components/discount.vue").default);
Vue.component("superrules", require("./components/rules.vue").default);

new Vue({
  el: "#wac_post",
  data: {
    sdwac_coupon_form: {
      type: "product"
    }
  }
});
