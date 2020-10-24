<template>
  <div>
    <div v-if="loading" class="spinner is-active"></div>
    <div v-else>
      <div>
        <input type="hidden" name="custompftLength" :value="faqs.length" />
        <input type="hidden" name="custompft_nonce" :value="nonce" />
        <div class="card faq-label-card" v-for="(faq, index) in faqs" :key="index">
          <strong @click="(faq.show) ? faq.show = false : faq.show = true">{{ faq.question }}</strong>
          <transition name="fade">
            <div class="custompft-form-items" :class="{hide: faq.show}">
              <div class="custompft-form-item">
                <div class="custompft-form">
                  <label :for="'custompft_que_'+index">
                    <strong>Question</strong>
                  </label>
                  <input
                    type="text"
                    :id="'custompft_que_'+index"
                    :name="'custompft_que_'+index"
                    v-model="faq.question"
                    placeholder="Write Question"
                  />
                </div>
              </div>
              <div class="custompft-form-item">
                <div class="custompft-form">
                  <label :for="'custompft_ans_'+index">
                    <strong>Answer</strong>
                  </label>
                  <textarea
                    :name="'custompft_ans_'+index"
                    :id="'custompft_ans_'+index"
                    v-model="faq.answer"
                    placeholder="Write Answer"
                  ></textarea>
                </div>
              </div>
              <div class="custompft-close" v-if="faqs">
                <span @click="closeSection(index)" class="dashicons dashicons-no-alt"></span>
              </div>
            </div>
          </transition>
        </div>
        <br />
        <div class="custompft_buttons">
          <button @click="cloneSection()" type="button" class="button-primary">Add</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "productadmin",
  props: ["nonce"],
  data() {
    return {
      loading: false,
      faqs: [],
    };
  },
  created() {
    this.getData();
  },
  methods: {
    cloneSection() {
      this.faqs.push({
        question: "Untitled",
        answer: null,
        show: false,
      });
    },
    closeSection(index) {
      this.faqs.splice(index, 1);
    },
    getData() {
      this.loading = true;
      let formData = {
        action: "custompft_get_data",
        post_id: custompft_post.id,
      };
      let root = this;
      axios
        .post(custompft_helper_obj.ajax_url, Qs.stringify(formData))
        .then((response) => {
          if (response.data != "" || response.data != []) {
            root.faqs = response.data;
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

<style>
.hide {
  display: none;
}
</style>