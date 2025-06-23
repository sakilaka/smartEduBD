<template>
  <div class="row col-12">
    <div class="img-holder">
      <div class="bg"></div>
      <div class="info-holder">
        <h3 class="text-center">{{ $root.app_name }}</h3>
        <img :src="left_image" alt="" />
      </div>
    </div>
    <div class="form-holder">
      <div class="form-content">
        <div class="form-items">
          <div class="website-logo-inside">
            <a href="/">
              <div
                class="logo"
                :style="'background-image:url(' + login_logo + ')'"
              >
                <img
                  style="height: 115px"
                  class="logo-size"
                  :src="login_logo"
                  alt=""
                />
              </div>
            </a>
          </div>
          <h3>Admin Login</h3>
          <p>Welcome Back!!</p>
          <form
            v-if="!$root.spinner"
            v-on:submit.prevent="submit"
            method="post"
          >
            <input
              class="form-control"
              type="email"
              placeholder="E-mail Address"
              v-model="data.email"
              :class="reqClass('email')"
              autofocus
            />
            <div class="text-danger">
              {{ validation.firstError("data.email") }}
            </div>
            <div v-if="errors.email" class="text-danger">
              {{ errors.email[0] }}
            </div>

            <input
              class="form-control mt-3"
              type="password"
              placeholder="Password"
              v-model="data.password"
              :class="reqClass('password')"
            />
            <div class="text-danger">
              {{ validation.firstError("data.password") }}
            </div>
            <div v-if="errors.password" class="text-danger">
              {{ errors.password[0] }}
            </div>

            <div class="form-button full-width">
              <button
                :disabled="$root.submit ? true : false"
                type="submit"
                class="ibtn btn-forget"
              >
                <span v-if="$root.submit">processing... </span>
                <span v-else> Login </span>
              </button>
            </div>
          </form>
        </div>

        <!-- SUCCESS MESSAGE -->
        <div class="form-sent">
          <div class="website-logo-inside">
            <a href="/">
              <div
                class="logo"
                :style="'background-image:url(' + login_logo + ')'"
              >
                <img
                  style="height: 115px"
                  class="logo-size"
                  :src="login_logo"
                  alt=""
                />
              </div>
            </a>
          </div>
          <div class="tick-holder">
            <div class="tick-icon"></div>
          </div>
          <h3>Login Successfully</h3>
          <p>Please wait...</p>
        </div>
        <!-- SUCCESS MESSAGE /-->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      login_logo: `${this.$root.baseurl}/images/login.png`,
      left_image: `${this.$root.baseurl}/images/loginbg.svg`,
      data: {
        email: "",
        password: "",
      },
      errors: {},
    };
  },
  methods: {
    submit() {
      this.$validate().then((res) => {
        if (res) {
          this.$root.submit = true;
          axios
            .post("/admin/system-admin", this.data)
            .then((res) => {
              if (res.status == 200) {
                $(".form-items", ".form-content").addClass("hide-it");
                $(".form-sent", ".form-content").addClass("show-it");

                this.notify(res.data.message, "success");
                Admin.login(res.data.data);

                setTimeout(() => {
                  window.location = `${this.$root.baseurl}/admin/dashboard`;
                }, 400);
              }
            })
            .catch((err) => {
              console.log(err.response);
              this.$root.submit = false;
              this.errors = this.catchErrors(err);
            });
        }
      });
    },

    reqClass(field) {
      if (this.validation.hasError("data." + field)) {
        return "form-invalid";
      } else if (this.data[field]) {
        return "form-valid";
      }
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
