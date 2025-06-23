<template>
  <main class="form-signin">
    <form v-if="!$root.spinner" v-on:submit.prevent="submit" class="mt-4">
      <slot v-if="!$root.spinner">
        <div class="text-center">
          <img
            class="mb-4"
            src="https://getbootstrap.com/docs/5.0/assets/brand/bootstrap-social-logo.png"
            alt=""
            width="72"
            height="57"
          />
        </div>
        <h1 class="h3 mb-3 fw-normal text-center">Please sign in</h1>
        <label for="inputEmail" class="visually-hidden">Email address</label>
        <input
          type="email"
          id="inputEmail"
          class="form-control"
          placeholder="Email address"
          required
          autofocus
          v-model="data.email"
        />
        <label for="inputPassword" class="visually-hidden">Password</label>
        <input
          type="password"
          id="inputPassword"
          class="form-control"
          placeholder="Password"
          required
          v-model="data.password"
        />

        <button class="w-100 btn btn-lg btn-primary" type="submit">
          Sign in
        </button>
      </slot>
      <div v-if="$root.spinner" class="login mt-3">
        <spinner />
      </div>
    </form>
  </main>
</template>


<script>
export default {
  data() {
    return {
      data: {
        email: "oshitsd@gmail.com",
        password: "oshitsd",
      },
    };
  },
  methods: {
    submit() {
      const error = this.validation.countErrors();
      this.$validate().then((res) => {
        if (res) {
          this.$root.spinner = true;
          axios
            .post("/login", this.data)
            .then((res) => {
              if (res.status == 200 && res.data.id) {
                User.login(res.data);
                this.notify(res.data.message, "success");
                window.location = this.$root.baseurl + "/user/dashboard";
              } else {
                this.$root.spinner = false;
                this.notify(res.data.message, "error");
              }
            })
            .catch((error) => {
              this.$root.spinner = false;
              this.notify("Something went wrong, please try again", "error");
            });
        }
      });
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.email": function (value = null) {
      return Validator.value(value).required("Email is required");
    },
    "data.password": function (value = null) {
      return Validator.value(value)
        .required("Password is required")
        .minLength(6);
    },
  },
};
</script>


<style>
.form-signin {
  width: 100%;
  max-width: 330px;
  padding: 15px;
  margin: auto;
}
.form-signin .checkbox {
  font-weight: 400;
}
.form-signin .form-control {
  position: relative;
  box-sizing: border-box;
  height: auto;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>