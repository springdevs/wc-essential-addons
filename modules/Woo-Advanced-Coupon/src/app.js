import Vue from "vue/dist/vue.min.js";
window.axios = require("axios");
window.Qs = require('qs');
import Toasted from 'vue-toasted';

Vue.use(Toasted)

Vue.component("supertype", require("./components/type.vue").default);
Vue.component("superfilter", require("./components/filter.vue").default);
Vue.component("superdiscount", require("./components/discount.vue").default);
Vue.component("superrules", require("./components/rules.vue").default);
Vue.component("supertabs", require("./components/tabs.vue").default);

new Vue({
  el: "#wac_post",
  data: {
    sdwac_coupon_form: {
      type: "product"
    }
  }
});
