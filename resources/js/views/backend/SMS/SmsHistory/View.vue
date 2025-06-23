<template>
  <div class="card" v-if="!$root.spinner">
    <div class="card-body min-height">
      <!--View Base Table Start-->
      <ViewBaseTable :data="data" />
      <!--View Base Table End-->
    </div>
  </div>
</template>

<script>
// define model name
const model = "smsHistory";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      data: [],
    };
  },
  async created() {
    this.get_data(`${this.model}/${this.$route.params.id}`); // get data
    this.setBreadcrumbs(this.model, "view", null, addOrBack); // Set Breadcrumbs
  },
};
</script>