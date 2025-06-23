<template>
  <div
    v-if="$root.spinner"
    class="card-body d-flex align-items-center justify-content-center"
  >
    <span
      style="height: 1.2rem; width: 1.2rem; font-size: 11px"
      class="spinner-border text-success"
    ></span>
    <span class="processing"> &nbsp;processing...</span>
  </div>
</template>
