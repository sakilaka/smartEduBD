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
            <!------------ Single Input ------------>
            <SelectCustom
              title="Institution Category"
              field="institution_category_id"
              :req="true"
              :datas="$root.global.institution_categories"
              col="4"
              val="id"
              val_title="name"
            />
            <Input
              title="Sorting"
              field="sorting"
              type="number"
              :req="true"
              :col="2"
            />
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
const model = "academicClass";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Academic Class",
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      submitType: "",
      data: { institution_category_id: null },
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

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      } else {
        this.get_sorting("MasterSetup-AcademicClass");
      }
      this.setBreadcrumbs(this.model, page, "Academic Class", addOrBack);
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.institution_category_id": function (value = null) {
      return Validator.value(value).required(
        "Institution Category is required"
      );
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).required("Sorting is required");
    },
  },
};
</script>