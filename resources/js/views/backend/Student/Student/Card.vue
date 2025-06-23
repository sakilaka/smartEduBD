<template>
  <div :class="className">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">
          <i class="bx bx-chevrons-right"></i> {{ title }}
        </h6>
      </div>
      <div class="card-body">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    className: { type: String, default: "col-xl-4 col-12" },
    title: { type: String, default: "" },
  },
};
</script>