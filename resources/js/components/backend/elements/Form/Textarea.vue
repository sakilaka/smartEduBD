<template>
  <div :class="'col-xl-' + (col ? col : 6)" class="col-12">
    <Label
      :title="title"
      :req="req"
      :condition="$parent.validation.hasError('data.' + field)"
      :msg="$parent.validation.firstError('data.' + field)"
    />
    <textarea
      :type="type"
      v-model="$parent.data[field]"
      :name="field"
      class="form-control form-control-sm"
      :placeholder="placeholder"
      :style="!fixheight ? 'height: 31px' : ''"
      :rows="rows ? rows : ''"
    >
    </textarea>
  </div>
</template>

<script>
export default {
  props: [
    "title",
    "placeholder",
    "field",
    "type",
    "col",
    "req",
    "rows",
    "fixheight",
  ],

  methods: {
    reqClass() {
      if (this.$parent.validation.hasError("data." + this.field) && this.req) {
        return "form-invalid";
      } else if (this.$parent.data[this.field]) {
        return "form-valid";
      }
    },
  },
};
</script>