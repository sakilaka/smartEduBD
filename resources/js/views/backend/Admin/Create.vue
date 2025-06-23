<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectCustom
              title="Institution"
              field="institution_id"
              :datas="$root.global.institutions"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <Input title="Name" field="name" type="text" :req="true" />
            <!------------ Single Input ------------>
            <Input title="Email" field="email" type="email" :req="true" />
            <!------------ Single Input ------------>
            <Input
              title="Password"
              field="password"
              type="password"
              :req="!$route.params.id ? true : false"
            />
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label
                title="Role"
                :condition="validation.hasError('data.role_id')"
                :msg="validation.firstError('data.role_id')"
              />
              <v-select
                v-model="data.role_id"
                label="name"
                :reduce="(obj) => obj.id"
                :options="roles"
                placeholder="--Select Role--"
                :closeOnSelect="true"
              ></v-select>
            </div>

            <!------------ Single Input ------------>
            <Input title="Mobile" field="mobile" type="number" :req="false" />
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus />
    </div>
  </form>
</template>

<script>
// define model name
const model = "admin";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      submitType: "",
      data: {
        role_id: null,
        institution_id: null,
        status: "active",
      },
      roles: [],
    };
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  methods: {
    submit: function () {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data, this.submitType);
          }
        }
      });
    },

    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);

      this.get_data("role", "roles", { allData: true });
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.email": function (value = null) {
      if (!this.$route.params.id) {
        return Validator.value(value).required("Email is required").email();
      }
    },
    "data.role_id": function (value = null) {
      return Validator.value(value).required("Role is required");
    },
    "data.password": function (value = null) {
      if (!this.$route.params.id) {
        return Validator.value(value)
          .required("Password is required")
          .minLength(6);
      }
    },
    "data.mobile": function (value = null) {
      return Validator.value(value)
        .digit()
        .regex("01+[0-9+-]*$", "Must start with 01.")
        .minLength(11)
        .maxLength(11);
    },
  },
};
</script>