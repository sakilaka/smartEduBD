<template>
  <div :class="'col-xl-' + (col ? col : 6)">
    <Label :title="label" :req="required" :condition="error" :msg="error" />

    <slot name="field">
      <input
        :id="id"
        :type="type"
        :name="name"
        :value="value"
        @input="handleInput"
        class="form-control form-control-sm"
        :class="[inputClass, error ? 'form-invalid' : '']"
        :placeholder="placeholder ? placeholder : label"
        autocomplete="off"
        :readonly="readonly"
      />
    </slot>
  </div>
</template>

<script>
export default {
  name: "InputField",
  props: {
    value: [String, Number, Boolean],
    label: { type: String },
    type: { type: String, default: "text" },
    required: { type: Boolean, default: false },
    id: { type: String },
    col: { type: String, default: "12" },
    inputClass: { type: String },
    error: { type: String, default: "" },
    serverError: { type: Array },
    placeholder: { type: String, default: "" },
    name: { type: String, default: "" },
    readonly: { type: Boolean, default: false },
  },

  methods: {
    handleInput(ev) {
      this.$emit("input", ev.target.value);
    },
  },
};
</script> 
