<template>
  <div :class="'col-xl-' + (col ? col : 6)" class="col-12">
    <Label
      :title="title"
      :req="req"
      :condition="$parent.validation.hasError('data.' + field)"
      :msg="$parent.validation.firstError('data.' + field)"
    />

    <input
      class="form-control form-control-sm"
      v-model="$parent.data[field]"
      :type="type ? type : 'text'"
      :name="field"
      :placeholder="title"
      :disabled="disable ? true : false"
      :readonly="readOnly ? true : false"
      :class="reqClass()"
      autocomplete=""
    />
  </div>
</template>

<script>
export default {
  props: ["title", "field", "type", "col", "req", "disable", "readOnly"],

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