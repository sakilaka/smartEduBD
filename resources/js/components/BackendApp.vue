<template>
  <div>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  methods: {
    loginCheck() {
      axios
        .get("/login-check")
        .then((res) => {
          if (res.status == 200 && res.data.data) {
            Admin.login(res.data.data);
          } else {
            if (!Boolean(Admin.loggedIn())) {
              Admin.logout();
            }
          }
        })
        .catch((error) => console.log(error));
    },
  },

  created() {
    this.loginCheck();
  },

  beforeCreate() {
    if (!Admin.id()) {
      Admin.logout();
    }
  },
};
</script>
