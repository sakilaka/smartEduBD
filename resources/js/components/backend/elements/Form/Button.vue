<template>
  <button
    v-if="btnTitle != 'Save & Edit'"
    type="submit"
    :disabled="$root.submit ? true : false"
    class="btn btn-sm px-3"
    :class="!$route.params.id ? 'btn-primary' : 'btn-success'"
  >
    <span v-if="$root.submit">
      <span class="spinner-border spinner-border-sm"></span>
      <span v-if="process">{{ process }}...</span>
      <span v-else>processing...</span>
    </span>
    <span v-else> <i class="bx bx-save"></i>{{ btnTitle }}</span>
  </button>

  <button
    v-else
    type="button"
    :disabled="$root.submit ? true : false"
    class="btn btn-success btn-sm px-3"
    @click="$parent.submit('edit')"
  >
    <span v-if="$root.submit">
      <span class="spinner-border spinner-border-sm"></span>processing...
    </span>
    <span v-else> <i class="bx bx-check-circle"></i>{{ btnTitle }}</span>
  </button>
</template>

<script>
export default {
  props: ["title", "process"],
  data() {
    return {
      type: "",
      btnTitle: this.title,
    };
  },
  mounted() {
    let split = this.$route.name.split(".");
    if (split.length == 2) {
      if (this.title == "Submit" && split[1] == "edit") {
        this.btnTitle = "Update";
      }
    }
  },
};
</script>