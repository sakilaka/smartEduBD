<template>
  <div class="user-box dropdown">
    <a
      class="d-flex align-items-center nav-link dropdown-toggle dropdown-toggle-nocaret"
      href="#"
      role="button"
      data-bs-toggle="dropdown"
      aria-expanded="false"
    >
      <img
        :src="profileImage ? profileImage : $root.userimage"
        class="user-img"
        alt="user avatar"
      />
      <div class="user-info ps-3">
        <p class="user-name mb-0">{{ $parent.logged_name }}</p>
        <p class="designattion mb-0">{{ $parent.role_name }}</p>
      </div>
    </a>
    <ul class="dropdown-menu dropdown-menu-end">
      <li v-if="$root.checkPermission('admin.show')">
        <router-link :to="{ name: 'admin.profile' }" class="dropdown-item">
          <i class="bx bx-user-circle"></i><span>Profile</span>
        </router-link>
      </li>
      <li v-if="$root.checkPermission('admin.index')">
        <router-link :to="{ name: 'admin.index' }" class="dropdown-item">
          <i class="bx bx-user"></i><span>Admin</span>
        </router-link>
      </li>
      <li v-if="$root.checkPermission('role.index')">
        <router-link :to="{ name: 'role.index' }" class="dropdown-item">
          <i class="bx bx-user-pin"></i><span>Role</span>
        </router-link>
      </li>
      <li v-if="$root.checkPermission('siteSetting.index')">
        <router-link :to="{ name: 'siteSetting.index' }" class="dropdown-item">
          <i class="bx bx-cog"></i><span>Site Setting</span>
        </router-link>
      </li>
      <li v-if="$root.checkPermission('menu.index')">
        <router-link :to="{ name: 'menu.index' }" class="dropdown-item">
          <i class="bx bx-food-menu"></i><span>Menu List</span>
        </router-link>
      </li>
      <li v-if="$root.checkPermission('activityLog.index')">
        <router-link :to="{ name: 'activityLog.index' }" class="dropdown-item">
          <i class="bx bx-bar-chart-alt-2"></i><span>Activity Log </span>
        </router-link>
      </li>
      <li v-if="$root.checkPermission('module.create')">
        <router-link :to="{ name: 'module.create' }" class="dropdown-item">
          <i class="bx bx-file"></i><span>Module Create</span>
        </router-link>
      </li>
      <li>
        <div class="dropdown-divider mb-0"></div>
      </li>
      <li>
        <a class="dropdown-item" @click="Logout()" href="javascript:;"
          ><i class="bx bx-log-out-circle"></i><span>Logout</span></a
        >
      </li>
    </ul>
  </div>
</template>

<script>
export default {
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
};
</script>