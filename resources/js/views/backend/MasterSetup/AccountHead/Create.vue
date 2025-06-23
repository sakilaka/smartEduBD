<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-6 mx-auto">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3 mb-5">
            <Input title="Name (en)" field="name_en" :req="true" :col="6" />
            <Input title="Name (bn)" field="name_bn" :col="6" />
            <!------------ Single Input ------------>
            <div class="col-12">
              <Label
                title="Account Head Type"
                :req="true"
                :condition="validation.hasError('data.type')"
                :msg="validation.firstError('data.type')"
              />
              <input
                v-model="data.type"
                class="form-check-input"
                type="radio"
                value="school"
              />
              School/College &nbsp; &nbsp;
              <input
                v-model="data.type"
                class="form-check-input"
                type="radio"
                value="tuition"
              />
              Tuition &nbsp; &nbsp;
              <input
                v-model="data.type"
                class="form-check-input"
                type="radio"
                value="admission"
              />
              Admisison &nbsp; &nbsp;
              <input
                v-model="data.type"
                class="form-check-input"
                type="radio"
                value="hostel"
              />
              Hostel &nbsp; &nbsp;
              <input
                v-model="data.type"
                class="form-check-input"
                type="radio"
                value="certificate"
              />
              Certificate
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
const model = "accountHead";

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
      data: { status: "active" },
    };
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

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(this.model, page, "Account Head", addOrBack);
    },
  },
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name_en": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.type": function (value = null) {
      return Validator.value(value).required("Type is required");
    },
  },
};
</script>