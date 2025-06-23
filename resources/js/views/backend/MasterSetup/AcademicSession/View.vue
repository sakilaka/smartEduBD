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
const model = "academicSession";

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
  created() {
    this.setBreadcrumbs(this.model, "view", "Academic Session", addOrBack);
    this.get_data(`${this.model}/${this.$route.params.id}`);
  },
};
</script>