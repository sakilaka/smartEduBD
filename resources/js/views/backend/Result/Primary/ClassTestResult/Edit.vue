<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <form v-if="data.status != 'published'" id="form" class="row">
      <SearchParams
        title="Search Students"
        :data="data"
        :subject="true"
        :exam_date="true"
        :search_keyword="false"
        :validation="validation"
        :disable="disable_master_data"
        @search-event="getStudents"
      />

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
          <div class="row g-3 table-fixed printArea">
            <table class="table table-bordered table-hover table-striped mb-0">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th>Software ID</th>
                  <th>Student Name</th>
                  <th class="text-center">Roll Number</th>
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
                    {{ student.student ? student.student.software_id : "" }}
                  </td>
                  <td>
                    {{ student.student ? student.student.name_en : "" }}
                  </td>
                  <td class="text-center">
                    {{ student.student ? student.student.roll_number : "" }}
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
import SearchParams from "@views/backend/Result/elements/SearchParams";

// define model name
const model = "primaryClassTestResult";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { SearchParams },

  data() {
    return {
      search_filter: true,
      model: model,
      disable_master_data: true,
      spinner_key: "",
      mark_submit_icon_key: "",
      subject_id: null,
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
        details: [],
      },
      subject: {},
      exam_lists: [],
      subjects: [],
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
        institution_id: this.data.institution_id,
        medium_id: this.data.medium_id,
        academic_class_id: this.data.academic_class_id,
      };
      this.get_data("primary-classwise-subjects", "subjects", params);
    },

    selectSubject(id) {
      let subject = this.subjects.find((e) => e.subject_id == id);
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
          institution_id: this.data.institution_id,
          academic_session_id: this.data.academic_session_id,
          campus_id: this.data.campus_id,
          shift_id: this.data.shift_id,
          medium_id: this.data.medium_id,
          academic_class_id: this.data.academic_class_id,
          group_id: this.data.group_id,
          section_id: this.data.section_id,
          subject_id: this.subject_id,
          exam_id: this.data.exam_id,
          selected_student: selectedStudent,
        };
        this.$root.tableSpinner = true;
        axios
          .get("/primary-students-for-class-test-marks-entry", {
            params: params,
          })
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
    this.setBreadcrumbs(
      this.model,
      page,
      "Primary Class Test Result",
      addOrBack
    );

    this.get_data("get-exam", "exam_lists", {
      allData: true,
      exam_type: "ct",
      institution_category_id: 1,
    });
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