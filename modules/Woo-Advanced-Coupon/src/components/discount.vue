<template>
  <div>
    <div v-if="loading" class="spinner is-active sdwac_coupon_spinner"></div>
    <div v-else>
      <div class="sdwac_coupon-flex sdwac_coupon-filter" v-if="$root.sdwac_coupon_form.type !== 'bulk'">
        <div class="sdwac_coupon-col-3">
          <div class="sdwac_coupon-form">
            <label for="sdwac_coupon_discount_type">
              <strong>Discount Type</strong>
            </label>
            <select
              id="sdwac_coupon_discount_type"
              name="sdwac_coupon_discount_type"
              v-model="discounts.type"
            >
              <option value="percentage">Percentage discount</option>
              <option value="fixed">Fixed discount</option>
            </select>
          </div>
        </div>
        <div class="sdwac_coupon-filter-list">
          <div class="sdwac_coupon-form">
            <label for="sdwac_coupon_discount_value">
              <strong>Value</strong>
            </label>
            <input
              type="text"
              id="sdwac_coupon_discount_value"
              name="sdwac_coupon_discount_value"
              placeholder="0.00"
              v-model="discounts.value"
            />
          </div>
        </div>
      </div>
      <div v-else>
        <div
          class="sdwac_coupon-flex sdwac_coupon-filter sdwac_coupon-bulk-discount"
          v-for="(sdwac_couponDiscount,index) in sdwac_couponDiscounts"
          :key="'sdwac_couponDiscount-'+index"
        >
          <input type="hidden" name="discountLength" :value="sdwac_couponDiscounts.length" />
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form">
              <label :for="'sdwac_coupon_discount_min_'+index">
                <strong>Min</strong>
              </label>
              <input
                type="text"
                :id="'sdwac_coupon_discount_min_'+index"
                v-model="sdwac_couponDiscount.min"
                :name="'sdwac_coupon_discount_min_'+index"
                placeholder="Min"
              />
            </div>
          </div>
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form">
              <label :for="'sdwac_coupon_discount_max_'+index">
                <strong>Max</strong>
              </label>
              <input
                type="text"
                :id="'sdwac_coupon_discount_max_'+index"
                v-model="sdwac_couponDiscount.max"
                :name="'sdwac_coupon_discount_max_'+index"
                placeholder="Max"
              />
            </div>
          </div>
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form">
              <label :for="'sdwac_coupon_discount_type_'+index">
                <strong>Type</strong>
              </label>
              <select
                :id="'sdwac_coupon_discount_type_'+index"
                v-model="sdwac_couponDiscount.type"
                :name="'sdwac_coupon_discount_type_'+index"
              >
                <option value="percentage">Percentage discount</option>
                <option value="fixed">Fixed discount</option>
              </select>
            </div>
          </div>
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form">
              <label :for="'sdwac_coupon_discount_value_'+index">
                <strong>Value</strong>
              </label>
              <input
                type="text"
                :id="'sdwac_coupon_discount_value_'+index"
                v-model="sdwac_couponDiscount.value"
                :name="'sdwac_coupon_discount_value_'+index"
                placeholder="0.00"
              />
            </div>
          </div>
          <div class="sdwac_coupon-filter-close" v-if="sdwac_couponDiscounts.length > 1">
            <span @click="removeRange(index)" class="dashicons dashicons-no-alt"></span>
          </div>
        </div>
        <div class="sdwac_coupon_buttons">
          <button @click="AddRange" type="button" class="button-primary">Add Range</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "sdwac_coupondiscount",
  data() {
    return {
      loading: true,
      discounts: {
        type: "percentage",
        value: null,
      },
      sdwac_couponDiscounts: [
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
      this.sdwac_couponDiscounts.push({
        min: null,
        max: null,
        type: "percentage",
        value: null,
      });
    },
    removeRange(index) {
      this.sdwac_couponDiscounts.splice(index, 1);
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
            if (root.$root.sdwac_coupon_form.type === "bulk") {
              root.sdwac_couponDiscounts = response.data;
            } else {
              root.discounts = response.data;
            }
          }
          root.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
  mounted() {
    this.getDiscounts();
  },
};
</script>
