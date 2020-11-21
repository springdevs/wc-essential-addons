<template>
  <div>
    <div
      class="sdwac_coupon-flex sdwac_coupon-filter sdwac_coupon-bulk-discount"
      v-for="(sdwac_couponDiscount, index) in discounts"
      :key="'sdwac_couponDiscount-' + index"
    >
      <input type="hidden" name="discountLength" :value="discounts.length" />
      <div class="sdwac_coupon-bulk-list">
        <div class="sdwac_coupon-form sdevs-form">
          <label :for="'sdwac_coupon_discount_min_' + index">
            <strong>Min</strong>
          </label>
          <input
            type="text"
            :id="'sdwac_coupon_discount_min_' + index"
            v-model="sdwac_couponDiscount.min"
            :name="'sdwac_coupon_discount_min_' + index"
            placeholder="Min"
          />
        </div>
      </div>
      <div class="sdwac_coupon-bulk-list">
        <div class="sdwac_coupon-form sdevs-form">
          <label :for="'sdwac_coupon_discount_max_' + index">
            <strong>Max</strong>
          </label>
          <input
            type="text"
            :id="'sdwac_coupon_discount_max_' + index"
            v-model="sdwac_couponDiscount.max"
            :name="'sdwac_coupon_discount_max_' + index"
            placeholder="Max"
          />
        </div>
      </div>
      <div class="sdwac_coupon-bulk-list">
        <div class="sdwac_coupon-form sdevs-form">
          <label :for="'sdwac_coupon_discount_type_' + index">
            <strong>Type</strong>
          </label>
          <select
            :id="'sdwac_coupon_discount_type_' + index"
            v-model="sdwac_couponDiscount.type"
            :name="'sdwac_coupon_discount_type_' + index"
          >
            <option value="percentage">Percentage discount</option>
            <option value="fixed">Fixed discount</option>
          </select>
        </div>
      </div>
      <div class="sdwac_coupon-bulk-list">
        <div class="sdwac_coupon-form sdevs-form">
          <label :for="'sdwac_coupon_discount_value_' + index">
            <strong>Value</strong>
          </label>
          <input
            type="text"
            :id="'sdwac_coupon_discount_value_' + index"
            v-model="sdwac_couponDiscount.value"
            :name="'sdwac_coupon_discount_value_' + index"
            placeholder="0.00"
          />
        </div>
      </div>
      <div class="sdwac_coupon-filter-close" v-if="discounts.length > 1">
        <span
          @click="removeRange(index)"
          class="dashicons dashicons-no-alt"
        ></span>
      </div>
    </div>
    <div class="sdwac_coupon_buttons">
      <button @click="AddRange" type="button" class="button-primary">
        Add Range
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "sdwac_discount",
  data() {
    return {
      loading: true,
      discounts: [
        {
          min: null,
          max: null,
          type: "percentage",
          value: null,
        },
      ],
    };
  },
  created() {
    this.getDiscounts();
  },
  methods: {
    AddRange() {
      this.discounts.push({
        min: null,
        max: null,
        type: "percentage",
        value: null,
      });
    },
    removeRange(index) {
      this.discounts.splice(index, 1);
    },
    getDiscounts() {
      this.loading = true;
      let formData = {
        action: "sdwac_coupon_get_discounts",
        post_id: sdwac_coupon_post.id,
      };
      let root = this;
      axios
        .post(sdwac_coupon_helper_obj.ajax_url, Qs.stringify(formData))
        .then((response) => {
          if (response.data != [] && response.data != "") {
            root.discounts = response.data;
          }
          root.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>
