<template>
  <div v-if="!$root.spinner" class="row">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-body">
          <div class="d-flex flex-column align-items-center text-center">
            <img
              :src="data.profile ? data.profile : $root.userimage"
              alt="Admin"
              class="rounded-circle p-1 bg-primary"
              width="110"
            />
            <div class="mt-3">
              <h4>{{ data.name }}</h4>
              <p class="text-secondary mb-1">
                {{ data.role ? data.role.name : "" }}
              </p>
            </div>
          </div>
          <hr class="my-4" />
          <ul class="list-group list-group-flush">
            <li
              class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
            >
              <h6 class="mb-0"><i class="bx bx-envelope"></i>Email</h6>
              <span class="text-secondary">{{ data.email }}</span>
            </li>
            <li
              class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
            >
              <h6 class="mb-0"><i class="bx bx-mobile"></i>Mobile</h6>
              <span class="text-secondary">{{ data.mobile }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <ul class="nav nav-tabs nav-success" role="tablist">
            <li class="nav-item" role="presentation">
              <a
                class="nav-link active"
                data-bs-toggle="tab"
                href="#profile"
                role="tab"
                aria-selected="false"
                @click="password_option = false"
              >
                <div class="d-flex align-items-center">
                  <div class="tab-icon">
                    <i class="bx bx-user-pin font-18 me-1"></i>
                  </div>
                  <div class="tab-title">Profile Info</div>
                </div>
              </a>
            </li>
            <li class="nav-item" role="presentation">
              <a
                class="nav-link"
                data-bs-toggle="tab"
                href="#changePass"
                role="tab"
                aria-selected="true"
                @click="password_option = true"
              >
                <div class="d-flex align-items-center">
                  <div class="tab-icon">
                    <i class="bx bx-lock font-18 me-1"></i>
                  </div>
                  <div class="tab-title">Change Password</div>
                </div>
              </a>
            </li>
          </ul>
          <div class="tab-content py-3">
            <div class="tab-pane fade show active" id="profile" role="tabpanel">
              <form
                v-on:submit.prevent="updateInformation"
                enctype="multipart/form-data"
                id="updateInfoForm"
                class="row border rounded border-default m-1 p-2"
              >
                <File
                  title="Profile"
                  field="profile"
                  mime="img"
                  :req="false"
                  fileClassName="file1"
                />
                <Input title="Name" field="name" type="text" :req="true" />
                <Input title="Mobile" field="mobile" type="number" />
                <Input title="Email" field="email" :req="true" />
                <!------------ Single Input ------------>
                <div class="w-100"></div>
                <div class="row col-12 col-xl-4 justify-content-center p-3">
                  <Button title="Update Information" process="" />
                </div>
              </form>
            </div>

            <div class="tab-pane fade" id="changePass" role="tabpanel">
              <form
                v-on:submit.prevent="changePassword"
                class="row border rounded border-default m-1 p-2"
              >
                <!------------ Single Input ------------>
                <div class="mb-2 col-12 col-xl-6">
                  <Label
                    title="Old Password"
                    :req="true"
                    :condition="validation.hasError('change_pass.old_password')"
                    :msg="validation.firstError('change_pass.old_password')"
                  />
                  <input
                    v-model="change_pass.old_password"
                    type="password"
                    class="form-control form-control-sm"
                    placeholder="Old Password"
                    :class="reqClass('change_pass.old_password')"
                  />
                </div>
                <div class="w-100"></div>
                <slot v-if="pass_verify">
                  <!------------ Single Input ------------>
                  <div class="mb-2 col-12 col-xl-6" v-if="pass_verify">
                    <Label
                      title="New Password"
                      :req="true"
                      :condition="
                        validation.hasError('change_pass.new_password')
                      "
                      :msg="validation.firstError('change_pass.new_password')"
                    />
                    <input
                      v-model="change_pass.new_password"
                      type="password"
                      class="form-control form-control-sm"
                      placeholder="New Password"
                      :class="reqClass('change_pass.new_password')"
                    />
                  </div>
                  <!------------ Single Input ------------>
                  <div class="mb-2 col-12 col-xl-6" v-if="pass_verify">
                    <Label
                      title="New Password"
                      :req="true"
                      :condition="
                        validation.hasError('change_pass.confirm_password')
                      "
                      :msg="
                        validation.firstError('change_pass.confirm_password')
                      "
                    />
                    <input
                      v-model="change_pass.confirm_password"
                      type="password"
                      class="form-control form-control-sm"
                      placeholder="Confirm Password"
                      :class="reqClass('change_pass.confirm_password')"
                    />
                  </div>
                </slot>
                <!------------ Single Input ------------>
                <div class="w-100"></div>
                <div class="row col-12 col-xl-4 justify-content-center p-3">
                  <Button title="Change Password" process="" />
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import File from "./../../../components/backend/elements/Form/File";

// ===============Promise===============
import Promise from "bluebird";
// define model name
const model = "admin";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File },
  data() {
    return {
      model: model,
      role_name: "",
      images: { profile: "" },
      data: {},
      password_option: false,
      pass_verify: false,
      change_pass: {},
    };
  },
  methods: {
    updateInformation() {
      var id = this.$route.params.id;
      if (this.$route.name == "admin.profile") {
        id = Admin.id();
      }

      const error = this.validation.countErrors();
      this.$validate().then((res) => {
        if (res) {
          this.$root.submit = true;
          var form = document.getElementById("updateInfoForm");
          var formData = new FormData(form);
          formData.append("_method", "put");

          axios
            .post("/admin/" + id, formData)
            .then((res) => {
              this.notify(res.data.message, "success");
              this.get_data(`${this.model}/${id}`, "data", {
                route_name: this.$route.name,
              });

              setTimeout(() => {
                profile.dispatch("setProfile", this.data.profile);
              }, 400);
            })
            .catch((error) => console.log(error))
            .then((alw) => setTimeout(() => (this.$root.submit = false), 200));
        }
      });
    },

    reqClass(field) {
      if (this.validation.hasError(field)) {
        return "form-invalid";
      } else if (this.data[field]) {
        return "form-valid";
      }
    },
    changePassword() {
      const error = this.validation.countErrors();
      this.$validate().then((res) => {
        if (res) {
          this.$root.submit = true;
          axios
            .post("/change-password", this.change_pass)
            .then((res) => {
              this.notify(res.data.message, "success");
              Admin.logout();
            })
            .catch((error) => console.log(error))
            .then((alw) => setTimeout(() => (this.$root.submit = false), 200));
        }
      });
    },
  },
  created() {
    let id = this.$route.params.id;
    if (this.$route.name == "admin.profile") {
      id = Admin.id();
    }
    this.role_name = Admin.info() ? Admin.info().role_name : "";
    this.change_pass.id = id;

    this.get_data(`${this.model}/${id}`, "data", {
      route_name: this.$route.name,
    });
    this.setBreadcrumbs(this.model, "view", null, addOrBack);
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name": function (value = null) {
      if (!this.password_option) {
        return Validator.value(value).required("Name is required");
      }
    },
    "data.email": function (value = null) {
      if (!this.password_option) {
        return Validator.value(value).required("Email is required").email();
      }
    },
    "data.mobile": function (value = null) {
      if (!this.password_option) {
        return Validator.value(value)
          .digit()
          .regex("01+[0-9+-]*$", "Must start with 01.")
          .minLength(11)
          .maxLength(11);
      }
    },
    "change_pass.old_password": function (value = null) {
      var app = this;
      if (this.password_option) {
        return Validator.value(value)
          .required("Old password is required")
          .minLength(6)
          .custom(function () {
            if (!Validator.isEmpty(value)) {
              app.$root.submit = true;
              axios.post("/check-old-password", app.change_pass).then((res) => {
                if (res.data) {
                  app.pass_verify = true;
                } else {
                  app.pass_verify = false;
                  return "Old password do not match our records!!";
                }
              });
              return Promise.delay(2000).then(function () {
                if (!app.pass_verify) {
                  return "Old password do not match our records!!";
                }
                app.$root.submit = false;
              });
            }
          });
      }
    },
    "change_pass.new_password": function (value = null) {
      if (this.password_option && this.pass_verify) {
        return Validator.value(value)
          .required("New password is required")
          .minLength(6);
      }
    },
    "change_pass.confirm_password, change_pass.new_password": function (
      confirm_password = null,
      new_password = null
    ) {
      if (this.password_option && this.pass_verify) {
        return Validator.value(confirm_password)
          .required("Password confirmation is required")
          .match(new_password);
      }
    },
  },
};
</script>