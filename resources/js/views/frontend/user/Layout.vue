<template>
  <div class="row pt-3">
    <div class="col-3 h-100 bg-light">
      <h4>Left Side</h4>
      <ul>
        <li>
          <router-link :to="{ name: 'user.dashboard' }">
            Dashboard
          </router-link>
        </li>
        <li>
          <router-link :to="{ name: 'user.profile' }"> Profile </router-link>
        </li>
        <li><a href="#" @click="logout()"> Logout </a></li>
      </ul>
    </div>
    <div class="col-9">
      <router-view></router-view>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    logout() {
      User.logout();
    },
  },
  beforeCreate() {
    if (!User.id()) {
      User.logout();
    }
  },
};
</script>