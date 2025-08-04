<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <form v-if="data.status != 'published'" id="form" class="row">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Search Students</h6>
        </div>
        <div class="card-body p-3">
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
              :disable="disable_master_data"
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
              :disable="disable_master_data"
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
              :disable="disable_master_data"
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
              :disable="disable_master_data"
            />
            <!------------ Single Input ------------>
            <Select
              title="Exam"
              field="exam_id"
              :req="true"
              :datas="extraData.exam_lists"
              :col="2"
              :disable="disable_master_data"
            />
            <!------------ Single Input ------------>
            <div class="col-2">
              <Label
                title="Exam Date"
                :req="true"
                :condition="validation.hasError('data.exam_date')"
                :msg="validation.firstError('data.exam_date')"
              />
              <date-picker
                disabled
                v-model="data.exam_date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="click to select date"
              ></date-picker>
            </div>
            <div class="w-100"></div>
            <!------------ Single Input ------------>
            <div class="col-2">
              <Label
                title="Select Subject"
                :req="true"
                :condition="validation.hasError('subject_id')"
                :msg="validation.firstError('subject_id')"
              />
              <select
                @change="selectSubject($event.target.value)"
                v-model="subject_id"
                class="form-select form-select-sm"
                :class="reqClass('subject_id')"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(subject, key) in extraData.subjects"
                  :key="key"
                  v-bind:value="subject.subject_id"
                >
                  {{ subject.subject ? subject.subject.name_en : "" }}
                </option>
              </select>
            </div>
            <!------------ Button ------------>
            <div class="col-1 pt-3">
              <button
                type="button"
                @click="getStudents()"
                class="btn btn-sm btn-success"
                :disabled="$root.tableSpinner ? true : false"
              >
                <span v-if="$root.tableSpinner">
                  <span class="spinner-border spinner-border-sm"></span>
                </span>
                <span v-else> <i class="bx bx-search mx-0 fw-bold"></i></span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Student List -->
      <!-- Student List -->
      <div class="card border">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">Marks Entry</h6>
            </div>
            <div class="col-6">
              <span class="float-end" v-if="$root.tableSpinner">
                <span class="spinner-border spinner-border-sm"></span>
              </span>
              <span
                class="btn btn-xs btn-primary float-end"
                @click="getStudents()"
                v-else
              >
                <i class="bx bx-refresh"></i>
                Refresh
              </span>
            </div>
          </div>
        </div>
        <div class="card-body p-3">
          <div class="row g-3 table-fixed">
            <table class="table table-bordered table-hover table-striped mb-0">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th>Software ID</th>
                  <th>Student Name</th>
                  <th>College Roll</th>
                  <th class="text-center">
                    Mark <small>({{ subject.ct_mark }})</small>
                  </th>
                  <th class="text-center"></th>
                </tr>
              </thead>
              <tbody v-if="Object.keys(data.details).length > 0 && subject_id">
                <tr v-for="(student, key) in data.details" :key="key">
                  <td class="text-center">{{ key + 1 }}</td>
                  <td>
                    {{
                      student.student
                        ? student.student.student_id
                        : student.student_id
                    }}
                  </td>
                  <td>
                    {{ student.student ? student.student.name : student.name }}
                  </td>
                  <td>
                    {{
                      student.student
                        ? student.student.college_roll
                        : student.college_roll
                    }}
                  </td>
                  <td class="align-center">
                    <input
                      v-if="student.marks"
                      v-tab-pressed="(event) => handleTabPressed(key, event)"
                      v-on:keyup.enter="marksSubmit(key)"
                      @keyup="marksEntry(key)"
                      type="text"
                      v-model="student.marks[0].mark"
                      class="form-control form-control-sm text-center"
                      style="width: 80px"
                    />
                  </td>
                  <td class="text-center">
                    <span v-if="$root.submit && spinner_key == key">
                      <span class="spinner-border spinner-border-sm"></span>
                    </span>
                    <i
                      v-else-if="
                        parseInt(mark_submit_icon_key) == parseInt(key)
                      "
                      @click="marksSubmit(key)"
                      class="bx bxs-check-square btn btn-xs text-white btn-success"
                    ></i>
                  </td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr>
                  <td colspan="20" class="text-center">No data Found</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </form>

    <h4 class="text-center my-4" v-else>:) You are not authorized person</h4>
  </div>
</template>

<script>
// define model name
const model = "classTestResult";

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
      disable_master_data: true,
      spinner_key: "",
      mark_submit_icon_key: "",
      subject_id: null,
      data: {
        academic_session_id: null,
        academic_class_id: null,
        department_id: null,
        academic_qualification_id: null,
        exam_id: null,
        details: [],
      },
      subject: {},
      extraData: { exam_lists: [], subjects: [] },
    };
  },

  directives: {
    "tab-pressed": {
      bind(el, binding, vnode) {
        const handler = (event) => {
          binding.value(event); // Pass the event object explicitly
        };
        el.addEventListener("keydown", handler);
      },
      unbind(el, binding, vnode) {
        const handler = (event) => {
          binding.value(event); // Pass the event object explicitly
        };
        el.removeEventListener("keydown", handler);
      },
    },
  },

  methods: {
    handleTabPressed(key, event) {
      if (event.keyCode === 9) {
        setTimeout(() => {
          this.marksSubmit(key);
        }, 400);
      }
    },

    marksEntry(key) {
      this.mark_submit_icon_key = key;

      let newData = this.data.details[key];
      let marks = newData.marks[0];

      if (parseFloat(marks.mark) > parseFloat(this.subject.ct_mark)) {
        marks.mark = "";
        this.notify(
          `Please given mark less then ${this.subject.ct_mark}`,
          "error"
        );
      }
    },

    marksSubmit(key) {
      this.mark_submit_icon_key = key;

      if (this.$root.submit) {
        return false;
      }

      this.spinner_key = key;
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          let resultData = {
            details: this.data.details[key],
            subject_id: this.subject_id,
            subject: this.subject,
          };
          this.update(this.model, resultData, this.data.id, null, "no");
        }
      });
    },

    getSubjects() {
      let params = {
        academic_qualification_id: this.data.academic_qualification_id,
        department_id: this.data.department_id,
        academic_class_id: this.data.academic_class_id,
      };
      this.get_paginate_data("classwise-subjects", params, "subjects");
    },

    selectSubject(id) {
      let subject = this.extraData.subjects.find((e) => e.subject_id == id);
      this.subject = subject ? subject : {};
      this.getStudents();
    },

    getStudents() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        if (error > 0) return false;

        let selectedStudent =
          parseInt(this.subject.main_subject) ||
          parseInt(this.subject.fourth_subject)
            ? 1
            : 0;

        let params = {
          result_id: this.data.id,
          academic_session_id: this.data.academic_session_id,
          academic_class_id: this.data.academic_class_id,
          department_id: this.data.department_id,
          academic_qualification_id: this.data.academic_qualification_id,
          subject_id: this.subject_id,
          exam_id: this.data.exam_id,
          selected_student: selectedStudent,
        };
        this.$root.tableSpinner = true;
        axios
          .get("/students-for-class-test-marks-entry", { params: params })
          .then((res) => {
            this.data.details = res.data;
          })
          .catch((err) => {
            console.log(err);
            if (err.response.data && err.response.data.message) {
              this.notify(err.response.data.message, "warning");
            }
            this.data.details = [];
          })
          .finally((alw) => (this.$root.tableSpinner = false));
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

  async created() {
    let page = "create";
    if (this.$route.params.id) {
      page = "edit";
    }
    this.setBreadcrumbs(this.model, page, "Result", addOrBack);

    this.get_paginate_data(
      "get-exam",
      { allData: true, exam_type: "ct" },
      "exam_lists"
    );
  },

  async mounted() {
    if (this.$route.params.id) {
      this.$root.spinner = true;
      let url = `${this.model}/${this.$route.params.id}`;
      const res = await this.callApi("get", url);
      if (res.status == 200) {
        this.data = res.data;
        this.getSubjects();
      }
      this.$root.spinner = false;
    }
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
    subject_id: function (value = null) {
      return Validator.value(value).required("Subject is required");
    },
  },
};
</script>

<style scoped>
.align-center input {
  margin: auto;
}
.btn-disable {
  background: #cacaca;
}
</style>