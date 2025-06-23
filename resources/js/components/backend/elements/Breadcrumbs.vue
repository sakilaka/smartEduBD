<template>
  <div
    v-if="count > 0"
    class="page-breadcrumb d-sm-flex align-items-center mb-3"
  >
    <div class="breadcrumb-title pe-3 text-capitalize">
      {{ breadcrumbs[count - 1] ? breadcrumbs[count - 1]["title"] : "" }}
    </div>
    <div class="ps-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 p-0">
          <li class="breadcrumb-item">
            <router-link :to="{ name: 'admin.dashboard' }">
              <i class="bx bx-home-alt"></i>
            </router-link>
          </li>
          <li
            class="breadcrumb-item text-capitalize"
            v-for="(menu, index1) in breadcrumbs"
            :key="index1"
          >
            <router-link
              :to="{
                name: menu.route,
                params: { slug: menu.slug ? menu.slug : '' },
              }"
            >
              {{ menu.title }}
            </router-link>
          </li>
        </ol>
      </nav>
    </div>
    <div class="ms-auto">
      <AddOrBackButton />
    </div>
  </div>
</template>

<script>
import AddOrBackButton from "./AddOrBackButton.vue";
export default {
  components: { AddOrBackButton },
  data() {
    return {
      showMenus: false,
    };
  },
  methods: {
    removeMenu(index) {
      breadcrumbs.dispatch("removeMenu", index);
    },
  },
  computed: {
    breadcrumbs() {
      this.showMenus = false;
      return breadcrumbs.state.breadcrumbLevels;
    },
    addOrBack() {
      return breadcrumbs.state.addOrBack;
    },
    count() {
      if (breadcrumbs.state.breadcrumbLevels) {
        return Object.keys(breadcrumbs.state.breadcrumbLevels).length;
      }
      return 0;
    },
    menuTags() {
      return breadcrumbs.state.menuTags;
    },
  },
};
</script>