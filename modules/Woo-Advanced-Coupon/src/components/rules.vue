<template>
  <div>
    <div v-if="loading" class="spinner is-active sdwac_coupon_spinner"></div>
    <div v-else>
      <div>
        <input type="hidden" name="rulesLength" :value="conditions.length" />
        <div class="sdwac_coupon-form sdevs-form">
          <div class="sdwac_coupon-checkbox">
            <label for="sdwac_rule_match_all">
              <input
                name="sdwac_coupon_rule_relation"
                id="sdwac_rule_match_all"
                type="radio"
                value="match_all"
                v-model="relation"
              />
              <strong>Match All</strong>
              <p>The coupon will be applied only if all rules are matched.</p>
            </label>
            <label for="sdwac_rule_match_any">
              <input
                name="sdwac_coupon_rule_relation"
                id="sdwac_rule_match_any"
                type="radio"
                value="match_any"
                v-model="relation"
              />
              <strong>Match Any</strong>
              <p>
                The coupon will be applied only if one of the rules are matched.
              </p>
            </label>
          </div>
        </div>
        <div
          class="sdwac_coupon-flex sdwac_coupon-filter sdwac_coupon-bulk-discount"
          v-for="(condition, index) in conditions"
          :key="'condition' + index"
        >
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form sdevs-form">
              <label :for="'sdwac_coupon_rule_type_' + index">
                <strong>Condition Type</strong>
              </label>
              <select
                :id="'sdwac_coupon_rule_type_' + index"
                :name="'sdwac_coupon_rule_type_' + index"
                v-model="condition.type"
              >
                <option
                  v-for="(type, index) in types"
                  :key="'type-' + index"
                  :value="type.value"
                >
                  {{ type.label }}
                </option>
              </select>
            </div>
          </div>
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form sdevs-form">
              <label :for="'sdwac_coupon_rule_operator_' + index">
                <strong>count should be</strong>
              </label>
              <select
                :id="'sdwac_coupon_rule_operator_' + index"
                :name="'sdwac_coupon_rule_operator_' + index"
                v-model="condition.operator"
              >
                <option
                  v-for="(operator, index) in operators"
                  :key="'operator-' + index"
                  :value="operator.value"
                >
                  {{ operator.label }}
                </option>
              </select>
            </div>
          </div>
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form sdevs-form">
              <label :for="'sdwac_coupon_rule_item_' + index">
                <strong>item count</strong>
              </label>
              <input
                type="number"
                :id="'sdwac_coupon_rule_item_' + index"
                :name="'sdwac_coupon_rule_item_' + index"
                placeholder="1"
                min="1"
                v-model="condition.item_count"
              />
            </div>
          </div>
          <div class="sdwac_coupon-bulk-list">
            <div class="sdwac_coupon-form sdevs-form">
              <label :for="'sdwac_coupon_rule_calculate_' + index">
                <strong>calculate item count</strong>
              </label>
              <select
                :id="'sdwac_coupon_rule_calculate_' + index"
                :name="'sdwac_coupon_rule_calculate_' + index"
                v-model="condition.calculate"
              >
                <option
                  v-for="(calculate, index) in calculates"
                  :key="'calculate-' + index"
                  :value="calculate.value"
                >
                  {{ calculate.label }}
                </option>
              </select>
            </div>
          </div>
          <div class="sdwac_coupon-filter-close">
            <span
              @click="removeRule(index)"
              class="dashicons dashicons-no-alt"
            ></span>
          </div>
        </div>
        <div class="sdwac_coupon_buttons">
          <button type="button" @click="AddRules" class="button-primary">
            Add Condition
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "sdwac_rules",
  data() {
    return {
      loading: true,
      relation: "match_all",
      types: [
        {
          label: "Subtotal",
          value: "cart_subtotal",
        },
        {
          label: "Line Item Count",
          value: "cart_line_items_count",
        },
      ],
      operators: [
        {
          label: "Less than ( < )",
          value: "less_than",
        },
        {
          label: "Less than or equal ( <= )",
          value: "less_than_or_equal",
        },
        {
          label: "Greater than or equal ( >= )",
          value: "greater_than_or_equal",
        },
        {
          label: "greater_than ( > )",
          value: "greater_than",
        },
      ],
      calculates: [
        {
          label: "Count all items in cart",
          value: "from_cart",
        },
        {
          label: "Only count items chosen in the filters set for this rule",
          value: "from_filter",
        },
      ],
      conditions: [
        // {
        //   type: "cart_subtotal",
        //   operator: "less_than",
        //   item_count: null,
        //   calculate: "from_cart",
        // },
      ],
    };
  },
  created() {
    this.getRules();
  },
  methods: {
    AddRules() {
      this.conditions.push({
        type: "cart_subtotal",
        operator: "less_than",
        item_count: null,
        calculate: "from_cart",
      });
    },
    removeRule(index) {
      this.conditions.splice(index, 1);
    },
    getRules() {
      this.loading = false;
      let formData = {
        action: "sdwac_coupon_get_rules",
        post_id: sdwac_coupon_post.id,
      };
      let root = this;
      axios
        .post(sdwac_coupon_helper_obj.ajax_url, Qs.stringify(formData))
        .then((response) => {
          if (response.data != [] && response.data != "") {
            root.relation = response.data.relation;
            root.conditions =
              response.data.rules == null ? [] : response.data.rules;
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
