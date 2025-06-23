<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-3">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Academic Info</h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectCustom
              title="Sending Type"
              field="sending_type"
              :req="true"
              :datas="sending_types"
              val="id"
              val_title="name"
              col="12"
            />

            <template v-if="data.sending_type != 'any'">
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Session"
                field="academic_session_id"
                :req="true"
                :datas="$root.global.sessions ? $root.global.sessions : []"
                val="id"
                val_title="name"
                col="12"
              />
              <!------------ Single Input ------------>
              <Select
                title="Academic Level"
                field="academic_qualification_id"
                :req="true"
                :datas="
                  $root.global.qualifications ? $root.global.qualifications : []
                "
                col="12"
              />
            </template>

            <div
              v-if="
                data.sending_type == 'students' ||
                data.sending_type == 'applicants'
              "
              class="col-12 text-center"
            >
              <button
                type="button"
                :disabled="$root.tableSpinner ? true : false"
                @click="getStudents()"
                class="btn btn-sm btn-success my-3"
              >
                <span v-if="$root.tableSpinner">
                  <span class="spinner-border spinner-border-sm"></span>
                </span>
                <span v-else>
                  <i class="bx bx-search mx-0 fw-bold"></i>
                  Search Students
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-6">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            SMS Info

            <small class="float-end" style="font-size: 12px">
              Balacnce :
              <span class="text-danger">
                {{ $root.site.sms_balance | currency }}
              </span>
              Tk.
            </small>
          </h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectCustom
              title="SMS Template"
              field="sms_template_id"
              :req="true"
              :datas="extraData.sms_templates"
              val="id"
              val_title="name"
              col="12"
            />
            <!------------ Single Input ------------>
            <div
              v-if="
                data.sending_type == 'students' ||
                data.sending_type == 'applicants'
              "
              class="col-12"
            >
              <Label
                title="Student"
                :req="true"
                :condition="validation.hasError('data.student_ids')"
                :msg="validation.firstError('data.student_ids')"
              />
              <v-select
                v-model="data.student_ids"
                label="name"
                :reduce="(obj) => obj.id"
                :options="student_lists"
                placeholder="--Select Any--"
                :closeOnSelect="false"
                multiple
              ></v-select>
            </div>

            <div v-if="data.sending_type == 'any'" class="col-12">
              <Label
                title="Numbers"
                :req="true"
                :condition="validation.hasError('data.any_numbers')"
                :msg="validation.firstError('data.any_numbers')"
              />
              <textarea
                v-model="data.any_numbers"
                rows="7"
                class="form-control"
                placeholder="ex: 018*******1,018*******2,018*******3,018*******4"
                :class="
                  validation.hasError('data.any_numbers') ? 'form-invalid' : ''
                "
              ></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus
        btnTitle="Send"
        :save_and_edit="false"
        v-if="$root.site.sms_balance > 0"
      />
      <div v-else class="card border publish">
        <div class="card-body my-5">
          <p v-if="$root.site.title" class="text-center text-danger">
            Please recharge your SMS balance
          </p>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
// define model name
const model = "smsHistory";

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
      sending_types: [
        { id: "all", name: "All" },
        { id: "students", name: "College Students" },
        { id: "applicants", name: "Applicant Students" },
        { id: "any", name: "Any Number" },
      ],
      data: {
        academic_class_id: null,
        sending_type: "all",
        sms_template_id: null,
        student_ids: null,
        any_numbers: null,
      },
      students: [],
      extraData: { sms_templates: [] },
    };
  },

  computed: {
    student_lists() {
      let student = [];
      let roll = "";
      if (this.students.length > 0) {
        this.students.forEach((el) => {
          roll = el.college_roll ? `(${el.college_roll}) -` : "";
          if (this.data.sending_type == "applicants") {
            roll = el.admission_roll ? `(${el.admission_roll}) -` : "";
          }

          student.push({
            id: el.id,
            name: `${roll} ${el.name.substring(0, 8)} - (${el.mobile})`,
          });
        });
      }
      return student;
    },
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
    "data.sending_type": function (type) {
      if (type == "all") {
        this.data.student_ids = null;
        this.data.any_numbers = null;
      } else if (type == "students" || type == "applicants") {
        this.students = [];
        this.data.any_numbers = null;
      } else if (type == "any") {
        this.data.student_ids = null;
      }
    },
  },

  methods: {
    submit: function () {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          console.log(this.validation.allErrors());
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          if (confirm("Are you sure want to send sms?")) {
            this.store(this.model, this.data);
          }
        }
      });
    },

    getStudents() {
      let sending_type = this.data.sending_type;
      let params = {
        academic_class_id: this.data.academic_class_id,
        status: sending_type == "applicants" ? "approved" : "active",
        allData: true,
      };

      if (!params.academic_class_id) {
        this.notify("Please select search criteria", "error");
        return false;
      }

      this.$root.tableSpinner = true;
      let url = sending_type == "applicants" ? "/onlineAdmission" : "/student";
      axios
        .get(url, { params: params })
        .then((res) => (this.students = res.data))
        .finally((alw) => (this.$root.tableSpinner = false));
    },

    // Async Data
    asyncData() {
      this.setBreadcrumbs(this.model, "create", "Send SMS", addOrBack);
    },
  },

  mounted() {
    this.get_paginate_data("smsTemplate", { allData: true }, "sms_templates");
  },

  // ================== validation rule for form ==================
  validators: {
    "data.academic_session_id": function (value = null) {
      if (this.data.sending_type != "any") {
        return Validator.value(value).required("Session is required");
      }
      return Validator.value(value);
    },
    "data.academic_class_id": function (value = null) {
      if (this.data.sending_type != "any") {
        return Validator.value(value).required("Academic Class is required");
      }
      return Validator.value(value);
    },
    "data.academic_qualification_id": function (value = null) {
      if (this.data.sending_type != "any") {
        return Validator.value(value).required("Academic Level is required");
      }
      return Validator.value(value);
    },

    "data.sms_template_id": function (value = null) {
      return Validator.value(value).required("SMS Template is required");
    },
    "data.student_ids": function (value = null) {
      if (
        this.data.sending_type == "students" ||
        this.data.sending_type == "applicants"
      ) {
        return Validator.value(value).required("Student is required");
      }
      return Validator.value(value);
    },
    "data.any_numbers": function (value = null) {
      if (this.data.sending_type == "any") {
        return Validator.value(value).required("Numbers is required");
      }
      return Validator.value(value);
    },
  },
};
</script>