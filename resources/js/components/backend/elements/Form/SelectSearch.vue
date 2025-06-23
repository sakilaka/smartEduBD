<template>
  <div :class="'position-relative  col-6 pe-0 col-xl-' + (col ? col : 2)">
    <select
      v-model="$parent.search_data[field]"
      :name="field"
      class="form-select form-select-sm"
      @change="$parent.search()"
      :class="reqClass()"
    >
      <option value="">--{{ title }}--</option>

      <template v-if="loop_type == 'pluck'">
        <option
          v-for="(value, name, index) in datas"
          :key="index"
          v-bind:value="name"
        >
          {{ value }}
        </option>
      </template>

      <template v-else>
        <option
          v-for="(value, index) in datas"
          :key="index"
          v-bind:value="value[val]"
        >
          {{ value[val_title] }}
        </option>
      </template>
    </select>

    <span
      v-if="req"
      class="text-danger position-absolute"
      style="top: -5px; left: 8px"
      >*</span
    >
  </div>
</template>

<script>
export default {
  props: [
    "title",
    "field",
    "col",
    "req",
    "datas",
    "val",
    "val_title",
    "loop_type",
    "req",
  ],

  methods: {
    reqClass() {
      if (
        this.$parent.validation &&
        this.$parent.validation.hasError("search_data." + this.field) &&
        this.req
      ) {
        return "form-invalid";
      } else if (this.$parent.search_data[this.field]) {
        return "form-valid";
      }
    },
  },
};
</script>