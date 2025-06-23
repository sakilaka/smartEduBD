<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <form id="form" class="row">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Result Entry</h6>
        </div>
        <div class="card-body py-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectCustom
              title="Session"
              field="academic_session_id"
              :datas="sessions_filter(2)"
              :req="true"
              col="3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              v-show="!$root.institution_id"
              title="Institution"
              field="institution_id"
              :req="true"
              :datas="$root.global.institutions"
              col="3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Campus"
              field="campus_id"
              :req="true"
              :datas="campuses_filter(data.institution_id)"
              col="3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Shift"
              field="shift_id"
              :req="true"
              :datas="shift_filter(data.institution_id)"
              col="3"
              val="shift_id"
              val_title="shift_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Medium"
              field="medium_id"
              :req="true"
              :datas="medium_filter(data.institution_id)"
              col="3"
              val="medium_id"
              val_title="medium_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Academic Class"
              field="academic_class_id"
              :req="true"
              :datas="category_classes_filter(data.institution_id, 2)"
              col="3"
              val="academic_class_id"
              val_title="class_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Group"
              field="group_id"
              :req="true"
              :datas="group_filter(data.institution_id)"
              col="3"
              val="group_id"
              val_title="group_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Section"
              field="section_id"
              :req="true"
              :datas="section_filter(data.institution_id)"
              col="3"
              val="section_id"
              val_title="section_name"
            />
            <div class="w-100 m-0"></div>
            <!------------ Single Input ------------>
            <SelectCustom
              title="Exam"
              field="exam_id"
              :req="true"
              :datas="exam_lists"
              :col="3"
              val="id"
              val_title="name"
            />
            <div class="col-3">
              <Label
                title="Exam Date"
                :req="true"
                :condition="validation.hasError('data.exam_date')"
                :msg="validation.firstError('data.exam_date')"
              />
              <date-picker
                v-model="data.exam_date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select date"
              ></date-picker>
            </div>

            <!------------ Button ------------>
            <div class="col-12 text-center py-5">
              <button
                type="button"
                :disabled="$root.submit ? true : false"
                @click="submit()"
                class="btn btn-sm btn-success px-5"
              >
                <span v-if="$root.submit">
                  <span class="spinner-border spinner-border-sm"></span>
                </span>
                <span v-else>Submit & Next</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
// define model name
const model = "secondaryClassTestResult";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Marks Entry",
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      data: {
        academic_session_id: null,
        institution_id: null,
        campus_id: null,
        shift_id: null,
        medium_id: null,
        academic_class_id: null,
        group_id: null,
        section_id: null,
        exam_id: null,
      },
      exam_lists: [],
    };
  },

  methods: {
    submit() {
      if (this.$root.submit) {
        return false;
      }

      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          this.store(this.model, this.data, "edit");
        }
      });
    },
  },

  created() {
    this.setBreadcrumbs(
      this.model,
      "create",
      "Secondary Class Test Marks Entry",
      addOrBack
    );

    this.get_data("get-exam", "exam_lists", {
      allData: true,
      exam_type: "ct",
      institution_category_id: 2,
    });
  },

  mounted() {
    if (this.$root.institution_id) {
      this.data.institution_id = this.$root.institution_id;
    }
  },

  // ================== validation rule for form ==================
  validators: {
    "data.academic_session_id": function (value = null) {
      return Validator.value(value).required("Session is required");
    },
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "data.campus_id": function (value = null) {
      return Validator.value(value).required("Campus is required");
    },
    "data.shift_id": function (value = null) {
      return Validator.value(value).required("Shift is required");
    },
    "data.medium_id": function (value = null) {
      return Validator.value(value).required("Medium is required");
    },
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "data.group_id": function (value = null) {
      return Validator.value(value).required("Group is required");
    },
    "data.section_id": function (value = null) {
      return Validator.value(value).required("Section is required");
    },
    "data.exam_id": function (value = null) {
      return Validator.value(value).required("Exam is required");
    },
    "data.exam_date": function (value = null) {
      return Validator.value(value).required("Exam date is required");
    },
  },
};
</script>