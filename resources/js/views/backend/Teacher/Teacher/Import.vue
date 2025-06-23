<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
    enctype="multipart/form-data"
  >
    <div class="col-xl-9">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label
                title="Excel File Upload"
                :req="true"
                :condition="validation.hasError('data.excel_file')"
                :msg="validation.firstError('data.excel_file')"
              />
              <b-form-file
                v-model="data.excel_file"
                id="file-small file"
                size="sm"
                name="excel_file"
                drop-placeholder="Drop file here"
                :class="reqClass('excel_file')"
                accept=".csv"
              ></b-form-file>
            </div>
            <div class="col-1 text-center pt-4">
              <a :href="sample_file" download="" class="btn btn-xs btn-primary">
                <i
                  class="bx bx-download"
                  title="Download Sample Excel Format"
                ></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus :save_and_edit="false" />
    </div>
  </form>
</template>

<script>
// define model name
const model = "teacher";

import File from "./../../../../components/backend/elements/Form/File";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Teacher Import",
  icon: "left-arrow-alt",
};

export default {
  components: { File },

  data() {
    return {
      model: model,
      sample_file: `${this.$root.baseurl}/public/sample_xl/teachers_import.csv`,
      data: {
        excel_file: null,
      },
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
          var form = document.getElementById("form");
          var formData = new FormData(form);
          this.store(`${this.model}-import`, formData, "teacher.index");
        }
      });
    },

    // Async Data
    asyncData() {
      this.setBreadcrumbs(this.model, "create", "Teacher Import", addOrBack);
    },

    reqClass(field) {
      if (this.validation.hasError(field)) {
        return "form-invalid";
      } else if (this.data[field]) {
        return "form-valid";
      }
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
    "data.excel_file": function (value = null) {
      return Validator.value(value).required("Excel file is required");
    },
  },
};
</script>