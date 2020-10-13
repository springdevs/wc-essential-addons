<template>
  <div class="options_group">
    <p class="form-field">
      <label for="sdwac_coupon_feature">Coupon Feature</label>
      <select
        class="select short"
        name="sdwac_coupon_feature"
        id="sdwac_coupon_feature"
        v-model="sdwac_coupon_feature"
      >
        <option value>Select Coupon Feature</option>
        <option
          v-for="(coupon, index) in coupons"
          :key="'coupon-'+index"
          :value="coupon.value"
        >{{ coupon.label }}</option>
      </select>
    </p>
    <p class="form-field">
      <label for="overwrite_discount">OverWrite Discount</label>
      <input
        type="checkbox"
        name="overwrite_discount"
        id="overwrite_discount"
        value="yes"
        class="checkbox"
        :checked="overwrite_discount === 'yes' ? true : false"
      />
      <span class="description">
        Check this box if you want to OverWrite default Discount.
        <br />
        <i>Bulk Discount can be written without check or UnCheck !!</i>.
      </span>
    </p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      sdwac_coupon_feature: "",
      overwrite_discount: null,
      coupons: [],
    };
  },
  created() {
    this.getLists();
    this.getData();
  },
  methods: {
    getLists() {
      let formData = {
        action: "sdwac_coupon_get_woocoupons",
        post_id: sdwac_coupon_post.id,
      };
      let root = this;
      axios
        .post(sdwac_coupon_helper_obj.ajax_url, Qs.stringify(formData))
        .then((response) => {
          root.coupons = response.data;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    getData() {
      let formData = {
        action: "sdwac_coupon_get_sdwac_coupon_panel",
        post_id: sdwac_coupon_post.id,
      };
      let root = this;
      axios
        .post(sdwac_coupon_helper_obj.ajax_url, Qs.stringify(formData))
        .then((response) => {
          root.sdwac_coupon_feature = response.data.list_id;
          root.overwrite_discount = response.data.overwrite_discount;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>