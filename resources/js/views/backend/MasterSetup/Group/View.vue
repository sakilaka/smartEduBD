<template>
  <div class="card" v-if="!$root.spinner">
    <div class="card-body min-height">
      <ViewBaseTable :data="data" />
    </div>
  </div>
</template>

<script>
// define model name
const model = "group";

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
      data: {}
    };
  },
  created() {
    this.get_data(`${this.model}/${this.$route.params.id}`); 
    this.setBreadcrumbs(this.model, "view", null, addOrBack);
  }
};
</script>