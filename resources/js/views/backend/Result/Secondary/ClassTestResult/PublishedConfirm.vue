<template>
  <div class="container">
    <label class="text-white">Published Date</label>
    <input
      type="date"
      class="form-control mt-2"
      v-model="published_date"
      @click.stop
    />

    <div class="text-center mt-3">
      <span class="btn btn-xs" @click="$emit('close-toast')">Cancel</span>
      &nbsp;&nbsp;&nbsp;
      <span class="btn btn-info btn-xs px-2" @click.stop @click="confirm"
        >OK</span
      >
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      published_date: new Date().toISOString().slice(0, 10),
    };
  },
  methods: {
    confirm() {
      if (!this.published_date) {
        alert("Please select date");
        return false;
      }
      this.$emit("confirm", this.published_date);
      this.$emit("close-toast");
    },
  },
};
</script>