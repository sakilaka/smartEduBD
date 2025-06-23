<template>
  <div class="container">
    <span>Are you sure want to {{ msg }}? </span>
    <div class="text-center mt-3">
      <span class="btn btn-xs" @click="$emit('close-toast')">Cancel</span>
      &nbsp;&nbsp;&nbsp;
      <span class="btn btn-info btn-xs px-2" @click="$emit('confirm')">OK</span>
    </div>
  </div>
</template>

<script>
export default {
  props: ["msg"],
};
</script>
