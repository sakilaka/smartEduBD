<template>
  <label class=""
    >{{ title }}
    <span v-if="req && !condition">*</span>

    <i v-if="condition" class="bx bx-error"></i>
    <div class="validate-msg">{{ msg }} <i class="bx bxs-down-arrow"></i></div>
  </label>
</template>


<script>
export default {
  props: ["condition", "msg", "title", "req"],

  updated() {
    $(".bx-error")
      .hover(function () {
        $(this).siblings(".validate-msg").show();
      })
      .mouseleave(function () {
        $(this).siblings(".validate-msg").hide();
      });
  },
};
</script>