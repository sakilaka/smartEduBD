<template>
  <div>
    <router-link
      v-if="routes.view && $root.checkPermission(routes.view)"
      :to="{
        name: routes.view,
        params: { id: data.id },
      }"
      title="View"
      class="btn btn-outline-success btn-xs"
    >
      <i class="bx bxs-show"></i>
    </router-link>

    <router-link
      v-if="routes.edit && $root.checkPermission(routes.edit)"
      :to="{
        name: routes.edit,
        params: { id: data.id },
      }"
      title="Edit"
      class="btn btn-outline-primary btn-xs"
    >
      <i class="bx bx-edit me-0"></i>
    </router-link>

    <button
      v-if="routes.destroy && $root.checkPermission(routes.destroy)"
      @click="notify('Delete', 'confirm', item.slug ? item.slug : item.id)"
      title="Delete"
      class="btn btn-outline-danger btn-xs"
    >
      <i class="bx bx-trash me-0"></i>
    </button>
  </div>
</template>

<script>
export default {
  props: ["routes", "data"],

  methods: {
    callBack(id) {
      this.$parent.destroy(id);
    },
  },
};
</script>