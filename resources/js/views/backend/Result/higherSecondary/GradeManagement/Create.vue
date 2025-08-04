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
            <Input title="From Mark" field="from_mark" :req="true" />
            <Input title="To Mark" field="to_mark" :req="true" />
            <Input title="Letter Grade" field="grade" :req="true" />
            <Input title="GPA" field="gpa" :req="true" />
            <Input title="From GPA" field="from_gpa" :req="true" />
            <Input title="To GPA" field="to_gpa" :req="true" />
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
const model = "secondaryGradeManagement";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Grade Management",
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      submitType: "",
      data: {},
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
      this.setBreadcrumbs(
        this.model,
        page,
        "Secondary Grade Management",
        addOrBack
      );
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
    "data.from_mark": function (value = null) {
      return Validator.value(value).required("From Mark is required");
    },
    "data.to_mark": function (value = null) {
      return Validator.value(value).required("To Mark is required");
    },
    "data.grade": function (value = null) {
      return Validator.value(value).required("GRADE is required");
    },
    "data.gpa": function (value = null) {
      return Validator.value(value).required("GPA is required");
    },
  },
};
</script>