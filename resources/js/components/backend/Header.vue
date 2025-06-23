<template>
  <header>
    <div class="topbar d-flex align-items-center">
      <nav class="navbar navbar-expand">
        <div class="topbar-logo-header">
          <div class="">
            <router-link v-if="$root.site" :to="{ name: 'admin.dashboard' }">
              <img
                :src="institution_logo ? institution_logo : $root.site.logo"
                class="logo-icon"
              />
            </router-link>
          </div>
          <div class="">
            <h4 class="logo-text">
              <router-link v-if="$root.site" :to="{ name: 'admin.dashboard' }">
                {{ institution_name ? institution_name : $root.site.title }}
              </router-link>
            </h4>
          </div>
        </div>
        <div class="mobile-toggle-menu"><i class="bx bx-menu"></i></div>

        <!-- Search -->
        <!-- <Search /> -->
        <!-- Search -->

        <!-- Notification -->
        <Notification />
        <!-- Notification -->

        <!-- Header User -->
        <HeaderUser />
        <!-- Header User -->
      </nav>
    </div>
  </header>
</template>

<script>
import Search from "./Search.vue";
import Notification from "./Notification.vue";
import HeaderUser from "./HeaderUser.vue";

export default {
  components: { HeaderUser, Notification, Search },
  data() {
    return {
      role_name: "",
      logged_id: "",
      logged_name: "",
      institution_name: "",
      institution_logo: "",
    };
  },
  computed: {
    profileImage() {
      return profile.state.image;
    },
  },
  methods: {
    Logout() {
      this.$root.spinner = true;
      Admin.logout();
    },
  },
  created() {
    this.logged_id = Admin.id();
    this.role_name = Admin.info() ? Admin.info().role_name : "";
    this.logged_name = Admin.info() ? Admin.info().name : "";
    this.institution_name = Admin.info() ? Admin.info().institution_name : "";
    this.institution_logo = Admin.info() ? Admin.info().institution_logo : "";
  },
};
</script>