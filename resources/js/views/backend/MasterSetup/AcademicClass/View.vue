<template>
  <div class="card" v-if="!$root.spinner">
    <div class="card-body min-height">
      <ViewBaseTable :data="data" />
    </div>
  </div>
</template>

<script>
// define model name
const model = "academicClass";

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
    this.setBreadcrumbs(this.model, "view", "Academic Class", addOrBack);
    this.get_data(`${this.model}/${this.$route.params.id}`);
  },
};
</script>