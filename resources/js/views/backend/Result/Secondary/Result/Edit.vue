<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <form v-if="data.status != 'published'" id="form" class="row">
      <SearchParams
        title="Search Students"
        :data="data"
        :category="2"
        :subject="true"
        :certificate_bg="true"
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
            <div class="col-4">
              <h6 class="mb-0 text-uppercase">Marks Entry</h6>
            </div>

            <div class="col-4">
              <div>
                <b>Convert Mark:</b>
                <input v-model="convert_mark" type="radio" value="1" />
                Yes &nbsp; &nbsp;
                <input v-model="convert_mark" type="radio" value="0" />
                No
              </div>
            </div>

            <div class="col-4 text-end">
              <button
                type="button"
                v-if="
                  $root.checkPermission('secondaryResult.syncResult') &&
                  this.subject_id
                "
                @click="syncResult($route.params.id, 'subject')"
                title="Sync"
                class="btn btn-info btn-xs me-3"
                :disabled="sync_spinner"
              >
                <span
                  v-if="sync_spinner"
                  class="spinner-border spinner-border-sm"
                ></span>
                <span v-else>
                  <i class="bx bx-sync me-0"></i>
                  SYNC STUDENT MARKS
                </span>
              </button>
              <button
                type="button"
                v-if="
                  $root.checkPermission('secondaryResult.syncResult') &&
                  convert_mark
                "
                @click="syncResult($route.params.id)"
                title="Sync"
                class="btn btn-success btn-xs me-3"
                :disabled="sync_spinner"
              >
                <span
                  v-if="sync_spinner"
                  class="spinner-border spinner-border-sm"
                ></span>
                <span v-else>
                  <i class="bx bx-sync me-0"></i>
                  SYNC RESULT
                </span>
              </button>

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
                  <th>Roll Number</th>
                  <th class="text-center">
                    CT/CA <small>({{ subject.ct_mark }})</small>
                  </th>
                  <th class="text-center">
                    CQ
                    <small>({{ subject.cq_mark }})</small>
                  </th>
                  <th class="text-center">
                    MCQ
                    <small>({{ subject.mcq_mark }})</small>
                  </th>
                  <th class="text-center">
                    Practical
                    <small>({{ subject.practical_mark }})</small>
                  </th>
                  <th class="text-center">OBTAINED</th>
                  <th class="text-center">TOTAL</th>
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
                      @keyup="marksCalculate(key, 'ct_mark')"
                      type="text"
                      v-model="student.marks[0].ct_mark"
                      class="form-control form-control-sm text-center"
                      style="width: 80px"
                      :disabled="class_test_marks_added ? true : false"
                      :readonly="
                        parseInt(subject.ct_mark) && convert_mark ? false : true
                      "
                    />
                  </td>
                  <td class="align-center">
                    <input
                      v-tab-pressed="(event) => handleTabPressed(key, event)"
                      v-on:keyup.enter="marksSubmit(key)"
                      @keyup="marksCalculate(key, 'cq_mark')"
                      type="text"
                      v-model="student.marks[0].cq_mark"
                      class="form-control form-control-sm text-center"
                      style="width: 80px"
                      :readonly="
                        parseInt(subject.cq_mark) && convert_mark ? false : true
                      "
                    />
                  </td>
                  <td class="align-center">
                    <input
                      v-tab-pressed="(event) => handleTabPressed(key, event)"
                      v-on:keyup.enter="marksSubmit(key)"
                      @keyup="marksCalculate(key, 'mcq_mark')"
                      type="text"
                      v-model="student.marks[0].mcq_mark"
                      class="form-control form-control-sm text-center"
                      style="width: 80px"
                      :readonly="
                        parseInt(subject.mcq_mark) && convert_mark
                          ? false
                          : true
                      "
                    />
                  </td>
                  <td class="align-center">
                    <input
                      v-tab-pressed="(event) => handleTabPressed(key, event)"
                      v-on:keyup.enter="marksSubmit(key)"
                      @keyup="marksCalculate(key, 'practical_mark')"
                      type="text"
                      v-model="student.marks[0].practical_mark"
                      class="form-control form-control-sm text-center"
                      style="width: 80px"
                      :readonly="
                        parseInt(subject.practical_mark) && convert_mark
                          ? false
                          : true
                      "
                    />
                  </td>
                  <td class="align-center">
                    <input
                      type="text"
                      v-model="student.marks[0].obtained_mark"
                      class="form-control form-control-sm text-center"
                      style="width: 80px"
                      readonly
                    />
                  </td>
                  <td class="align-center">
                    <input
                      type="text"
                      v-model="student.marks[0].total_mark"
                      class="form-control form-control-sm text-center"
                      style="width: 80px"
                      readonly
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
const model = "secondaryResult";

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
      class_test_marks_added: true,
      disable_master_data: true,
      sync_spinner: false,
      spinner_key: "",
      model: model,
      convert_mark: "",
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
        certificate_bg: null,
        details: [],
      },
      subject: {},
      exam_lists: [],
      subjects: [],
      letter_grades: [],
    };
  },

  directives: {
    "tab-pressed": {
      bind(el, binding, vnode) {
        const handler = (event) => {
          binding.value(event);
        };
        el.addEventListener("keydown", handler);
      },
      unbind(el, binding, vnode) {
        const handler = (event) => {
          binding.value(event);
        };
        el.removeEventListener("keydown", handler);
      },
    },
  },

  methods: {
    syncResult(id, subject = null) {
      if (this.sync_spinner) return false;
      if (!this.convert_mark) {
        this.notify("Please select the convert mark", "error");
        return false;
      }

      if (confirm("Are you sure want to sync result")) {
        this.sync_spinner = true;
        let convert = this.convert_mark;
        axios
          .get(`${this.model}-sync/${id}/${convert}`, {
            params: {
              subject_id: subject ? this.subject_id : "",
              certificate_bg: this.data.certificate_bg,
            },
          })
          .then((res) => {
            this.notify(res.data.message, "success");
          })
          .finally((alw) => (this.sync_spinner = false));
      }
    },

    handleTabPressed(key, event) {
      if (event.keyCode === 9) {
        setTimeout(() => {
          this.marksSubmit(key);
        }, 400);
      }
    },

    marksSubmit(key) {
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
      this.get_data("secondary-classwise-subjects", "subjects", params);
    },

    selectSubject(id) {
      let subject = this.subjects.find((e) => e.subject_id == id);
      this.subject = subject ? subject : {};
      this.data.details = [];
      this.getStudents();
    },

    marksCalculate(key, field = null) {
      this.mark_submit_icon_key = key;

      let newData = this.data.details[key];
      let mark = newData.marks[0];

      if (parseFloat(mark[field]) > parseFloat(this.subject[field])) {
        mark[field] = "";
        this.notify(
          `Please given mark less then ${this.subject[field]}`,
          "error"
        );
      }

      let totalExamMark =
        parseFloat(Number(this.subject.cq_mark)) +
        parseFloat(Number(this.subject.mcq_mark)) +
        parseFloat(Number(this.subject.practical_mark));

      let obtained =
        parseFloat(Number(mark.cq_mark)) +
        parseFloat(Number(mark.mcq_mark)) +
        parseFloat(Number(mark.practical_mark));

      let convertMark = parseFloat(this.subject.converted_mark);

      if (parseInt(this.convert_mark)) {
        obtained = (parseFloat(obtained) / totalExamMark) * convertMark;
      }

      let total_mark = obtained;
      if (this.convert_mark) {
        total_mark = parseFloat(obtained) + parseFloat(Number(mark.ct_mark));
      }

      let gradeGpa = this.letterGradeGpa(obtained, mark);

      mark["obtained_mark"] = obtained.toFixed(2);
      mark["total_mark"] = total_mark.toFixed(2);
      mark["letter_grade"] = gradeGpa["grade"];
      mark["gpa"] = gradeGpa["gpa"];

      this.$set(this.data.details, key, newData);
    },

    letterGradeGpa(total_mark, mark) {
      let gradeGpa = {
        grade: "F",
        gpa: 0,
      };

      // check failed or not exam type wise
      let ctPass = parseFloat(this.subject.ct_pass_mark);
      let ctM = parseFloat(mark.ct_mark ? mark.ct_mark : 0);
      let cqPass = parseFloat(this.subject.cq_pass_mark);
      let cqM = parseFloat(mark.cq_mark ? mark.cq_mark : 0);
      let mcqPass = parseFloat(this.subject.mcq_pass_mark);
      let mcqM = parseFloat(mark.mcq_mark ? mark.mcq_mark : 0);

      if (parseInt(ctPass) && parseFloat(ctM) < parseFloat(ctPass)) {
        return gradeGpa;
      } else if (parseInt(cqPass) && parseFloat(cqM) < parseFloat(cqPass)) {
        return gradeGpa;
      } else if (parseInt(mcqPass) && parseFloat(mcqM) < parseFloat(mcqPass)) {
        return gradeGpa;
      }

      // define letter grade
      if (this.letter_grades) {
        let find = this.letter_grades.find(
          (e) =>
            parseFloat(e.from_mark) <= total_mark &&
            parseFloat(e.to_mark) >= parseFloat(total_mark)
        );
        if (find) {
          gradeGpa.grade = find.grade;
          gradeGpa.gpa = find.gpa;
        }
      }

      return gradeGpa;
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
          .get("/secondary-students-for-marks-entry", { params: params })
          .then((res) => {
            this.data.details = res.data.details;
            this.class_test_marks_added = res.data.class_test_marks_added;
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
    this.setBreadcrumbs(this.model, page, "Secondary Result", addOrBack);

    this.get_data("get-exam", "exam_lists", {
      allData: true,
      exam_type: "term",
      institution_category_id: 2,
    });
    this.get_data("secondaryGradeManagement", "letter_grades", {
      allData: true,
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
    "data.certificate_bg": function (value = null) {
      return Validator.value(value).required("Marksheet Type is required");
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