<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    enctype="multipart/form-data"
  >
    <div class="row g-3">
      <div class="col-4">
        <div class="card">
          <div class="card-body">
            <!------------ Single Input ------------>
            <SelectCustom
              v-show="!$root.institution_id"
              title="Institution"
              field="institution_id"
              :req="true"
              :datas="$root.global.institutions"
              col="12"
              class="mb-3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Institution Category"
              field="institution_category_id"
              :datas="$root.global.institution_categories"
              :req="true"
              col="12"
              class="mb-3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Session"
              field="academic_session_id"
              :datas="sessions_filter(data.institution_category_id)"
              :req="true"
              col="12"
              class="mb-3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Campus"
              field="campus_id"
              :req="true"
              :datas="campuses_filter(data.institution_id)"
              col="12"
              class="mb-3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Shift"
              field="shift_id"
              :req="true"
              :datas="shift_filter(data.institution_id)"
              col="12"
              class="mb-3"
              val="shift_id"
              val_title="shift_name"
            />
          </div>
        </div>
      </div>
      <div class="col-4">
        <div class="card">
          <div class="card-body">
            <!------------ Single Input ------------>
            <SelectCustom
              title="Medium"
              field="medium_id"
              :req="true"
              :datas="medium_filter(data.institution_id)"
              col="12"
              class="mb-3"
              val="medium_id"
              val_title="medium_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Academic Class"
              field="academic_class_id"
              :req="true"
              :datas="class_filter(data.institution_id)"
              col="12"
              class="mb-3"
              val="academic_class_id"
              val_title="class_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Group"
              field="group_id"
              :req="true"
              :datas="group_filter(data.institution_id)"
              col="12"
              class="mb-3"
              val="group_id"
              val_title="group_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Section"
              field="section_id"
              :req="true"
              :datas="section_filter(data.institution_id)"
              col="12"
              class="mb-3"
              val="section_id"
              val_title="section_name"
            />
          </div>
        </div>
      </div>
      <div class="col-4">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-10">
                <Label
                  title="Excel (.csv)"
                  :req="true"
                  :condition="validation.hasError('data.excel')"
                  :msg="validation.firstError('data.excel')"
                />
                <b-form-file
                  id="file-small file-student"
                  size="sm"
                  v-model="data.excel"
                  name="excel"
                  :class="reqClass('data.excel')"
                  drop-placeholder="Drop file here"
                ></b-form-file>
              </div>
              <div class="col-1 text-center pt-4">
                <a
                  :href="sample_file"
                  download=""
                  class="btn btn-xs btn-primary"
                >
                  <i
                    class="bx bx-download"
                    title="Download Sample Excel Format"
                  ></i>
                </a>
              </div>
            </div>

            <div class="my-5 text-center">
              <button
                type="submit"
                :disabled="
                  validation.countErrors() > 0 || $root.submit ? true : false
                "
                class="btn btn-sm btn-success"
              >
                <span v-if="$root.submit">
                  <span class="spinner-border spinner-border-sm"></span>
                  processing...
                </span>
                <span v-else> <i class="bx bx-save"></i> Submit</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
// define model name
const model = "student";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

// set breadcrumb
const breadcrumb = [
  { route: "student.index", title: "Student" },
  { route: "student.create", title: "Student Import" },
];

export default {
  data() {
    return {
      model: model,
      sample_file: `${this.$root.baseurl}/sample_xl/student_import.csv`,
      customBreadcrumb: { breadcrumb: breadcrumb, addOrBack: addOrBack },
      disabled: false,
      data: {
        institution_id: null,
        institution_category_id: null,
        academic_session_id: null,
        campus_id: null,
        medium_id: null,
        group_id: null,
        section_id: null,
        shift_id: null,
        academic_class_id: null,
        excel: null,
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

        if (res) {
          var form = document.getElementById("form");
          var formData = new FormData(form);
          this.store("student-import", formData, "student.index");
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
  },
  created() {
    breadcrumbs.dispatch("setBreadcrumbs", this.customBreadcrumb);
  },

  mounted() {
    this.data.institution_id = this.$root.institution_id;
  },

  // ================== validation rule for form ==================
  validators: {
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "data.institution_category_id": function (value = null) {
      return Validator.value(value).required(
        "Institution category is required"
      );
    },
    "data.academic_session_id": function (value = null) {
      return Validator.value(value).required("Academic session is required");
    },
    "data.campus_id": function (value = null) {
      return Validator.value(value).required("Campus is required");
    },
    "data.medium_id": function (value = null) {
      return Validator.value(value).required("Medium is required");
    },
    "data.group_id": function (value = null) {
      return Validator.value(value).required("Group is required");
    },
    "data.section_id": function (value = null) {
      return Validator.value(value).required("Section is required");
    },
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "data.shift_id": function (value = null) {
      return Validator.value(value).required("Shift is required");
    },
    "data.excel": function (value = null) {
      return Validator.value(value).required("Excel is required");
    },
  },
};
</script>