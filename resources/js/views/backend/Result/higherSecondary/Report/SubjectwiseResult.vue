<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <div class="card border">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">Search Result</h6>
      </div>
      <div class="card-body py-3">
        <div class="row">
          <!------------ Single Input ------------>
          <SelectCustom
            title="Session"
            field="academic_session_id"
            :req="true"
            :datas="$root.global.sessions ? $root.global.sessions : []"
            val="id"
            val_title="name"
            :col="1"
            class="pe-0"
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
            class="pe-0"
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
            class="pe-0"
          />
          <!------------ Single Input ------------>
          <SelectCustom
            title="Class"
            field="academic_class_id"
            :req="true"
            :datas="class_filter(data.academic_qualification_id)"
            val="id"
            val_title="name"
            :col="1"
            class="pe-0"
          />
          <!------------ Single Input ------------>
          <Select
            title="Exam"
            field="exam_id"
            :req="true"
            :datas="extraData.exam_lists"
            :col="2"
            class="pe-0"
          />
          <!------------ Single Input ------------>
          <div class="col-1 pe-0">
            <Label
              title="Subject"
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

          <!------------ Single Input ------------>
          <div class="col-1 pe-0 pt-1">
            <select
              v-model="field_name"
              class="form-control form-control-sm mt-3"
            >
              <option value>--Select One--</option>
              <template v-for="(fname, fKey) in fields_name">
                <option :value="fKey" :key="fKey">{{ fname }}</option>
              </template>
            </select>
          </div>
          <div class="col-1 px-0 pt-1">
            <input
              type="text"
              v-model="search_keyword"
              class="form-control form-control-sm mt-3"
              placeholder="Type your text"
            />
          </div>
          <!------------ Button ------------>
          <div class="col-1 px-0 pt-1">
            <button
              type="button"
              :disabled="$root.submit ? true : false"
              @click="search()"
              class="btn btn-sm btn-success mt-3"
            >
              <span v-if="$root.submit">
                <span class="spinner-border spinner-border-sm"></span>
              </span>
              <span v-else><i class="bx bx-search mx-0 fw-bold"></i></span>
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
            <h6 class="mb-0 text-uppercase">Students Result</h6>
          </div>
          <div class="col-6">
            <a class="btn btn-xs btn-info float-end" href="javascript:;">
              <download-excel
                v-if="data.details"
                :data="data.details"
                :fields="json_fields"
                :header="excel_header"
                name="result.xls"
                style="cursor: pointer"
              >
                <i class="bx bx-list-ul"></i> EXCEL
              </download-excel>
            </a>
            <a
              class="btn btn-xs btn-success float-end me-3"
              @click="print('printArea', 'Result')"
              href="javascript:;"
            >
              <i class="bx bx-printer"></i> PRINT
            </a>
          </div>
        </div>
      </div>

      <div class="card-body p-3" id="printArea">
        <div class="row g-3 mb-3">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="width: 20%">Session</th>
                <td style="width: 30%">
                  {{ data.academic_session ? data.academic_session.name : "" }}
                </td>
                <th style="width: 20%">Academic Level</th>
                <td>
                  {{ data.qualification ? data.qualification.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Department/Group</th>
                <td>
                  {{ data.department ? data.department.name : "" }}
                </td>
                <th>Class</th>
                <td>
                  {{ data.academic_class ? data.academic_class.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Exam</th>
                <td>
                  {{ data.exam ? data.exam.name : "" }}
                </td>
                <th>Subject Name</th>
                <td>
                  {{ subject.subject ? subject.subject.name_en : "" }}
                </td>
              </tr>
            </thead>
          </table>
        </div>

        <div class="row g-3 table-fixed">
          <table class="table table-bordered table-hover table-striped mb-0">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Software ID</th>
                <th>Student Name</th>
                <th>College Roll</th>
                <th class="text-center">
                  CT <small>({{ subject.ct_mark }})</small>
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
                  PRAC
                  <small>({{ subject.practical_mark }})</small>
                </th>
                <th class="text-center">Obtained Mark</th>
                <th class="text-center">Total Mark</th>
                <th class="text-center">GRADE</th>
                <th class="text-center">GPA</th>
                <th class="text-center" style="width: 10%">Status</th>
              </tr>
            </thead>
            <tbody v-if="data.details && Object.keys(data.details).length > 0">
              <tr v-for="(std, key) in data.details" :key="key">
                <th class="text-center">{{ key + 1 }}</th>
                <td>{{ std.student_id }}</td>
                <td>{{ std.name }}</td>
                <td>{{ std.college_roll }}</td>
                <td class="text-center">{{ std.ct_mark }}</td>
                <td class="text-center">{{ std.cq_mark }}</td>
                <td class="text-center">{{ std.mcq_mark }}</td>
                <td class="text-center">{{ std.practical_mark }}</td>
                <td class="text-center">{{ std.obtained_mark }}</td>
                <td class="text-center">{{ std.total_mark }}</td>
                <td class="text-center">{{ std.letter_grade }}</td>
                <td class="text-center">{{ std.gpa }}</td>
                <td class="text-center">
                  <span v-if="std.letter_grade != 'F'" :class="activeClass()">
                    <b><i class="bx bxs-circle me-1"></i>PASSED</b>
                  </span>
                  <span v-else :class="errorClass()">
                    <b><i class="bx bxs-circle me-1"></i> FAILED</b>
                  </span>
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
  </div>
</template>

<script>
// define model name
const model = "result";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

//json fields for export excel
const json_fields = {
  "Software ID": "student_id",
  "College Roll": "college_roll",
  Name: "name",
  "CT Mark": "ct_mark",
  "CQ Mark": "cq_mark",
  "MCQ Mark": "mcq_mark",
  "Practical Mark": "practical_mark",
  "Obtained Mark": "obtained_mark",
  "Total Mark": "total_mark",
  gpa: "gpa",
  "Letter Grade": "letter_grade",
};

export default {
  data() {
    return {
      model: model,
      excel_header: [],
      json_fields: json_fields,
      fields_name: {
        student_id: "Software ID",
        name: "Name",
        mobile: "Mobile",
        college_roll: "College Roll",
      },
      field_name: "college_roll",
      search_keyword: "",
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

  watch: {
    "data.academic_class_id": function (id) {
      let params = {
        academic_qualification_id: this.data.academic_qualification_id,
        department_id: this.data.department_id,
        academic_class_id: this.data.academic_class_id,
      };
      this.get_paginate_data("classwise-subjects", params, "subjects");
    },
  },

  methods: {
    search() {
      if (this.$root.submit) return false;

      this.$validate().then((res) => {
        if (res) {
          this.$root.submit = true;

          let params = {
            academic_session_id: this.data.academic_session_id,
            academic_class_id: this.data.academic_class_id,
            department_id: this.data.department_id,
            academic_qualification_id: this.data.academic_qualification_id,
            exam_id: this.data.exam_id,
            subject_id: this.subject_id,
            field_name: this.field_name,
            search_keyword: this.search_keyword,
          };

          axios
            .get("/subjectwise-result", { params: params })
            .then((res) => {
              if (res.status == 200) {
                this.data = res.data.result;

                if (res.data.excel_header) {
                  this.excel_header = res.data.excel_header;

                  let subjectName = this.subject.subject
                    ? this.subject.subject.name_en
                    : "";
                  this.excel_header.push(`Subject Name: ${subjectName}`);
                }
              }
            })
            .catch((err) => console.log(err))
            .finally((alw) => (this.$root.submit = false));
        }
      });
    },

    selectSubject(id) {
      let subject = this.extraData.subjects.find((e) => e.subject_id == id);
      this.subject = subject ? subject : {};
    },

    activeClass() {
      return "badge rounded-pill text-success bg-light-success p-1 text-uppercase px-3 w-100";
    },
    errorClass() {
      return "badge rounded-pill text-danger bg-light-danger p-1 text-uppercase px-3 w-100";
    },
    reqClass(field) {
      if (this.validation.hasError(field)) {
        return "form-invalid";
      } else if (this.subject_id) {
        return "form-valid";
      }
    },
  },

  created() {
    this.setBreadcrumbs(this.model, "view", "Subject Wise Result", addOrBack);

    this.get_paginate_data(
      "get-exam",
      { allData: true, exam_type: "term" },
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
    subject_id: function (value = null) {
      return Validator.value(value).required("Exam is required");
    },
  },
};
</script>