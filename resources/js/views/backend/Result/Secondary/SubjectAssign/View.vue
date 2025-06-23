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
const model = "secondarySubjectAssign";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Subject Assign",
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
    await this.get_data(this.model, this.$route.params.id, "data"); // get data
    this.setBreadcrumbs(this.model, "view", "Subject Assign", addOrBack); // Set Breadcrumbs
  },
};
</script>