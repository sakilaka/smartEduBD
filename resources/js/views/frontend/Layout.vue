<template>
  <div class="container">
    <router-view></router-view>

    <div
      v-if="$root.spinner"
      class="row d-flex justify-content-center position-absolute top-50 start-50 translate-middle"
      style="height: 350px"
    >
      <spinner />
    </div>
  </div>
</template>

<script>
export default {};
