<template>
  <div :class="'col-xl-' + (col ? col : 3)" class="col-12">
    <Label
      :title="title"
      :req="req"
      :condition="$parent.validation.hasError('data.' + field)"
      :msg="$parent.validation.firstError('data.' + field)"
    />

    <div class="w-100 my-1"></div>
    <div class="">
      <input
        type="radio"
        :name="field"
        v-model="$parent.data[field]"
        :value="value1"
        :id="field + '0'"
      />
      {{ statusTitle1 }} &nbsp; &nbsp;
      <input
        type="radio"
        :name="field"
        v-model="$parent.data[field]"
        :value="value2"
        :id="field + '1'"
      />
      {{ statusTitle2 }}
    </div>
  </div>
</template>

<script>
export default {
  name: "Radio",

  props: [
    "title",
    "field",
    "statusTitle1",
    "statusTitle2",
    "value1",
    "value2",
    "col",
    "req",
  ],
};
</script>