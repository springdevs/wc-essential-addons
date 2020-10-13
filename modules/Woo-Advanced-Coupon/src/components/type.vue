<template>
  <div>
    <div v-if="loading" class="spinner is-active sdwac_coupon_spinner"></div>
    <div v-else>
      <div>
        <input type="hidden" name="sdwac_coupon_main_nonce" :value="nonce" />
        <div class="sdwac_coupon-flex">
          <div class="sdwac_coupon-col-2">
            <div class="sdwac_coupon-form">
              <label for="discount">
                <strong>Discount Type</strong>
              </label>
              <select id="discount" v-model="value" @change="changeType" name="sdwac_coupon_type">
                <option
                  v-for="(discount, index) in discounts"
                  :key="index"
                  :value="index"
                >{{ discount.label }}</option>
              </select>
            </div>
          </div>

          <div class="sdwac_coupon-col-3 sdwac_coupon_buttons" v-if="show_label">
            <div class="sdwac_coupon-form">
              <label for="sdwac_coupon_discount_label">
                <strong>Discount Label</strong>
              </label>
              <input
                type="text"
                id="sdwac_coupon_discount_label"
                name="sdwac_coupon_discount_label"
                v-model="label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
export default {
  props: ["nonce"],
  data() {
    return {
      loading: true,
      value: "product",
      label: null,
      show_label: false,
      discounts: [],
    };
  },
  created() {
    this.getData();
  },
  methods: {
    changeType() {
      this.discount_label();
      this.$root.sdwac_coupon_form.type = this.value;
    },
    discount_label() {
      if (this.discounts[this.value].has_label) {
        this.show_label = true;
      } else {
        this.show_label = false;
      }
    },
    getData() {
      this.loading = true;
      let formData = {
        action: "sdwac_coupon_get_main",
        post_id: sdwac_coupon_post.id,
      };
      let root = this;
      axios
        .post(sdwac_coupon_helper_obj.ajax_url, Qs.stringify(formData))
        .then((response) => {
          if (response.data.post_meta != [] && response.data.post_meta != "") {
            let post_meta = response.data.post_meta;
            root.value = post_meta.type;
            root.label = post_meta.label;
            root.$root.sdwac_coupon_form.type = post_meta.type;
          }
          root.discounts = response.data.discount_type;
          root.discount_label();
          root.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
