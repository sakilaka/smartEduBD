<template>
  <div v-if="!$root.spinner" class="card">
    <div class="card-body min-height">
      <!--View Base Table Start-->
      <ViewBaseTable :data="data" />
      <!--View Base Table End-->
    </div>
  </div>
</template>

<script>
// define model name
const model = "notification";

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
  methods: {
    // Async Data
    asyncData() {
      this.get_data(`${this.model}/${this.$route.params.id}`);
      this.setBreadcrumbs(this.model, "view", null, addOrBack);
    },
  },
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },
};
</script>