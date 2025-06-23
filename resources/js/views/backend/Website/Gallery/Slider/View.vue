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
const model = "slider";

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
      data: {},
    };
  },
  created() {
    this.get_data(`${this.model}/${this.$route.params.id}`);
    this.setBreadcrumbs(this.model, "view", null, addOrBack);
  },
};
</script>