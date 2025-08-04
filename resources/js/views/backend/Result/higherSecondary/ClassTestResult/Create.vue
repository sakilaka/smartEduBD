<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <form id="form" class="row">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Result Entry</h6>
        </div>
        <div class="card-body py-5">
          <div class="row g-2">
            <!------------ Single Input ------------>
            <SelectCustom
              title="Select Session"
              field="academic_session_id"
              :req="true"
              :datas="$root.global.sessions ? $root.global.sessions : []"
              val="id"
              val_title="name"
              :col="2"
            />
            <!------------ Single Input ------------>
            <Select
              title="Academic Level"
              field="academic_qualification_id"
              :req="true"
              :datas="
                $root.global.qualifications ? $root.global.qualifications : []
              "
              :col="2"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Department/Group"
              field="department_id"
              :req="true"
              :datas="departments_filter(data.academic_qualification_id)"
              val="id"
              val_title="name"
              :col="2"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Class"
              field="academic_class_id"
              :req="true"
              :datas="class_filter(data.academic_qualification_id)"
              val="id"
              val_title="name"
              :col="2"
            />
            <!------------ Single Input ------------>
            <Select
              title="Exam"
              field="exam_id"
              :req="true"
              :datas="extraData.exam_lists"
              :col="2"
            />

            <div class="col-2">
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
                placeholder="click to select date"
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
const model = "classTestResult";

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
        academic_class_id: null,
        department_id: null,
        academic_qualification_id: null,
        exam_id: null,
      },
      subject: {},
      extraData: { exam_lists: [], subjects: [] },
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
    this.setBreadcrumbs(this.model, "create", "Marks Entry", addOrBack);

    this.get_paginate_data(
      "get-exam",
      { allData: true, exam_type: "ct" },
      "exam_lists"
    );
  },

  // ================== validation rule for form ==================
  validators: {
    "data.academic_session_id": function (value = null) {
      return Validator.value(value).required("Session is required");
    },
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "data.department_id": function (value = null) {
      return Validator.value(value).required("Department is required");
    },
    "data.academic_qualification_id": function (value = null) {
      return Validator.value(value).required("Academic Level is required");
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