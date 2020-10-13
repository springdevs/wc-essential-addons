<template>
  <div class="sdwac_coupon_tags_component" v-click-outside="hide" @click="openMulti">
    <ul class="sdwac_coupon_tags">
      <li style="cursor: pointer;">
        <span v-if="multiData.searchable" class="sdwac_coupon_select_search">
          <i class="dashicons dashicons-search"></i>
          <input @input="search" v-model="text" :placeholder="multiData.placeholder" type="text" />
        </span>
        <span v-else>
          <span v-if="selectOptions.length === 0">{{ multiData.placeholder }}</span>
        </span>
      </li>
      <li v-for="(selectOption, index) in selectOptions" :key="'option-'+index">
        <a @click="removeItem(selectOption, index)" href="javascript:void(0);">
          <span>{{ selectOption.label }}</span>
          <i class="dashicons dashicons-no-alt"></i>
        </a>
      </li>
    </ul>
    <ul v-if="autoLists" class="hidden-items">
      <li
        v-for="(option, index) in options"
        :key="index"
        @click="selectItem( option, index)"
      >{{ option.label }}</li>
    </ul>
  </div>
</template>

<script>
import ClickOutside from "vue-click-outside";
export default {
  name: "customSelect",
  props: ["multiData", "multiName", "defaultOption"],
  data() {
    return {
      text: null,
      options: [],
      selectOptions: [],
      selectValues: [],
      autoLists: false,
    };
  },
  created() {
    this.options = this.multiData.options;
    this.selectOptions = this.defaultOption;
  },
  methods: {
    selectItem(obj, index) {
      this.selectOptions.push(obj);
      this.selectValues.push(obj.value);
      this.options.splice(index, 1);
      this.$emit("selectOptions", {
        selectOption: this.selectOptions,
        name: this.multiName,
      });
    },
    removeItem(obj, index) {
      this.options.push(obj);
      this.selectOptions.splice(index, 1);
    },
    openMulti() {
      this.autoLists = true;
    },
    hide() {
      this.autoLists = false;
    },
    search(e) {
      if (this.text.length < 3) {
        return;
      }
      e.preventDefault();
      let formData = {
        action: this.multiData.search_action,
        option: this.selectOptions,
        queryData: this.text,
      };
      let root = this;
      axios
        .post(sdwac_coupon_helper_obj.ajax_url, Qs.stringify(formData))
        .then((response) => {
          if (Array.isArray(response.data)) {
            root.options = response.data;
          } else {
            if (response.data.label !== undefined) {
              root.options.push(response.data);
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
  directives: {
    ClickOutside,
  },
};
</script>
<!--

1. options
2. searchable
3. search placeholder
4. ajax_action

 -->
