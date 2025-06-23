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
            <Input title="Name" field="name" :req="true" />
            <Input title="Amount" field="amount" type="number" :req="true" />
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label
                title="Duration"
                :req="true"
                :condition="validation.hasError('data.duration')"
                :msg="validation.firstError('data.duration')"
              />
              <select
                v-model="data.duration"
                class="form-select form-select-sm"
                :class="reqClass('data.duration')"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(value, key, index) in 24"
                  :key="index"
                  v-bind:value="key"
                >
                  {{ value }} month
                </option>
              </select>
            </div>
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
const model = "package";

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
      data: { status: "active", duration: null },
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

    reqClass(field) {
      if (this.validation.hasError(field)) {
        return "form-invalid";
      } else if (this.data[field]) {
        return "form-valid";
      }
    },

    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.amount": function (value = null) {
      return Validator.value(value).required("Amount is required").digit();
    },
    "data.duration": function (value = null) {
      return Validator.value(value).required("Duration is required");
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
  },
};
</script>