import Vue from "vue";
window.axios = require('axios');
window.Qs = require('qs');

Vue.component("productadmin", require("./components/productadmin.vue").default);

new Vue({
  el: "#custompftapp"
});