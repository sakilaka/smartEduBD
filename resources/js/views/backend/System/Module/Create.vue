<template>
  <div v-if="!$root.spinner" class="col-xl-5 mx-auto">
    <div class="card">
      <div class="card-body">
        <div class="border border-success p-4 rounded">
          <!------------ Single Input ------------>
          <div
            class="row col-12"
            :class="{
              'has-error': validation.hasError('data.model_name'),
              'has-success': data.model_name,
            }"
          >
            <label class="col-4">
              Model Name :
              <span class="text-danger">*</span>
            </label>
            <div class="col-8">
              <input
                type="text"
                v-model="data.model_name"
                class="form-control form-control-sm"
                placeholder="Model Name"
              />
              <span class="text-danger">{{
                validation.firstError("data.model_name")
              }}</span>
            </div>

            <div class="w-100 mt-2"></div>
            <label class="col-4">Only Model :</label>
            <div class="col-7">
              <input type="checkbox" value="1" v-model="data.only_model" />
            </div>
          </div>

          <!-------------- button -------------->
          <div class="col-xl-3 mx-auto mt-4">
            <button
              @click="submit"
              type="submit"
              :disabled="
                validation.countErrors() > 0 || disabled ? true : false
              "
              class="btn btn-sm btn-success"
            >
              <span v-if="disabled">
                <span class="spinner-border spinner-border-sm"></span>
                checking..
              </span>
              <span v-else>Submit</span>
            </button>
          </div>
          <!-------------- button -------------->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// ===============Promise===============
import Promise from "bluebird";
// define model name
const model = "module";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

// set breadcrumb
const breadcrumb = [
  { route: "module.index", title: "Module" },
  { route: "module.create", title: "Module Create" },
];

export default {
  data() {
    return {
      model: model,
      customBreadcrumb: { breadcrumb: breadcrumb, addOrBack: addOrBack },
      disabled: false,
      model_exist: false,
      data: {},
      files: {},
    };
  },
  methods: {
    submit: function () {
      const error = this.validation.countErrors();
      this.$validate().then((res) => {
        if (res) {
          this.$root.spinner = true;
          axios
            .post("/module/create", this.data)
            .then((res) => {
              this.notify("Module Create Successfully", "success");
              this.$router.push({
                name: this.model + ".index",
                params: { model: this.data.model_name },
              });
            })
            .catch((error) =>
              this.notify(
                "Something went wrong, but Some file are crated, please check",
                "error"
              )
            )
            .then((alw) => setTimeout(() => (this.$root.spinner = false), 200));
        }
      });
    },
  },
  created() {
    breadcrumbs.dispatch("setBreadcrumbs", this.customBreadcrumb);
  },

  // ================== validation rule for form ==================
  validators: {
    "data.model_name": function (value = null) {
      var app = this;
      return Validator.value(value)
        .required("Model Name is required")
        .minLength(3)
        .regex("^[a-zA-Z_]*$", "Must only contain alphabetic characters.")
        .regex("(?=.*?[A-Z])", "at least one uppercase letter required")
        .regex("(?=.*?[a-z])", "at least one lowercase letter required")
        .custom(function () {
          if (!Validator.isEmpty(value)) {
            app.disabled = true;
            axios
              .get("/module/check-model", {
                params: { model_name: app.data.model_name },
              })
              .then((res) => {
                if (res.data) {
                  app.model_exist = true;
                } else {
                  app.model_exist = false;
                }
              });
            return Promise.delay(1500).then(function () {
              if (app.model_exist) {
                return "Model name already exists";
              }
              app.disabled = false;
            });
          }
        });
    },
  },
};
</script>