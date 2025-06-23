<template>
  <footer v-if="$root.site" class="page-footer px-5">
    <p class="mb-0 float-start">
      Copyright &copy; {{ year }}
      <a target="_blank" :href="$root.site.web">
        <b>{{ $root.site.title }} </b> </a
      >. All right reserved.
    </p>
    <p class="mb-0 float-end">
      <b>Developed by:</b>
      <a target="_blank" :href="$root.site.developed_by_url">
        {{ $root.site.developed_by }}
      </a>
    </p>
  </footer>
</template>

<script>
export default {
  data() {
    return {
      year: new Date().getFullYear(),
    };
  },
};
</script>