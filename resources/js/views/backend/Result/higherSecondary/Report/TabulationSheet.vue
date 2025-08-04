<template>
  <div class="col-12" v-if="!$root.spinner">
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
            :col="2"
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

    <div class="card">
      <div class="card-body">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">Tabulation Sheet</h6>
            </div>
            <div class="col-6">
              <a
                class="btn btn-xs btn-success float-end"
                @click="print('printArea', 'TabulationSheet')"
                href="javascript:;"
              >
                <i class="bx bx-printer"></i> PRINT
              </a>
            </div>
          </div>
        </div>

        <div id="printArea">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="width: 20%">Session</th>
                <td style="width: 30%">{{ result.academic_session }}</td>
                <th style="width: 20%">Academic Level</th>
                <td>{{ result.academic_level }}</td>
              </tr>
              <tr>
                <th>Department/Group</th>
                <td>{{ result.department }}</td>
                <th>Class</th>
                <td>{{ result.academic_class }}</td>
              </tr>
              <tr>
                <th>Exam</th>
                <td colspan="3">{{ result.exam }}</td>
              </tr>
            </thead>
          </table>

          <div class="table-responsive">
            <table
              class="table table-bordered table-hover table-striped align-middle"
              style="font-size: 13px"
            >
              <thead>
                <tr>
                  <th rowspan="2" class="align-middle text-center">SL.</th>
                  <th rowspan="2" class="align-middle">Software ID</th>
                  <th rowspan="2" class="align-middle">College Roll</th>
                  <th rowspan="2" class="align-middle">Student Name</th>
                  <th rowspan="2" class="align-middle">Mobile</th>
                  <template v-if="Object.keys(result.subjects).length > 0">
                    <th
                      colspan="7"
                      v-for="(sub, sbKey) in result.subjects"
                      :key="`sub${sbKey}`"
                      class="text-center"
                    >
                      {{ sub.subject ? sub.subject.name_en : "" }}
                    </th>
                  </template>
                  <th rowspan="2" class="align-middle text-center">Total</th>
                  <th rowspan="2" class="align-middle text-center">GPA</th>
                  <th rowspan="2" class="align-middle text-center">Grade</th>
                </tr>
                <template v-if="Object.keys(result.subjects).length > 0">
                  <tr class="text-center">
                    <template v-for="(sub, sbKey) in result.subjects">
                      <th :key="`1marks_heading${sbKey}`">CT</th>
                      <th :key="`2marks_heading${sbKey}`">CQ</th>
                      <th :key="`3marks_heading${sbKey}`">MCQ</th>
                      <th :key="`4marks_heading${sbKey}`">PRAC.</th>
                      <th :key="`5marks_heading${sbKey}`">CONVERT</th>
                      <th :key="`6marks_heading${sbKey}`">TOTAL</th>
                      <th :key="`7marks_heading${sbKey}`">GRADE</th>
                    </template>
                  </tr>
                </template>
              </thead>

              <tbody v-if="Object.keys(result.result_sheet).length > 0">
                <tr v-for="(std, key) in result.result_sheet" :key="key">
                  <td class="text-center">{{ key + 1 }}</td>
                  <td>{{ std.software_id }}</td>
                  <td>{{ std.college_roll }}</td>
                  <td>{{ std.name }}</td>
                  <td>{{ std.mobile }}</td>

                  <template v-if="Object.keys(std.subjects).length > 0">
                    <template v-for="(mark, rmKey) in std.subjects">
                      <td class="text-center" :key="`1marks${rmKey}`">
                        {{ mark.ct_mark }}
                      </td>
                      <td class="text-center" :key="`2marks${rmKey}`">
                        {{ mark.cq_mark }}
                      </td>
                      <td class="text-center" :key="`3marks${rmKey}`">
                        {{ mark.mcq_mark }}
                      </td>
                      <td class="text-center" :key="`4marks${rmKey}`">
                        {{ mark.practical_mark }}
                      </td>
                      <td class="text-center" :key="`5marks${rmKey}`">
                        {{ mark.obtained_mark }}
                      </td>
                      <td class="text-center" :key="`6marks${rmKey}`">
                        {{ mark.total_mark }}
                      </td>
                      <td class="text-center" :key="`7marks${rmKey}`">
                        {{ mark.letter_grade }}
                      </td>
                    </template>
                  </template>

                  <td class="text-center">{{ std.total_mark }}</td>
                  <td class="text-center">{{ std.gpa }}</td>
                  <td class="text-center">{{ std.letter_grade }}</td>
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

export default {
  data() {
    return {
      model: model,
      fields_name: {
        student_id: "Software ID",
        name: "Name",
        mobile: "Mobile",
        college_roll: "College Roll",
      },
      subject_id: null,
      field_name: "college_roll",
      search_keyword: "",
      data: {
        academic_session_id: null,
        academic_class_id: null,
        department_id: null,
        academic_qualification_id: null,
        exam_id: null,
        // academic_session_id: 8,
        // academic_class_id: 1,
        // department_id: 21,
        // academic_qualification_id: 1,
        // exam_id: 3,
      },
      result: { subjects: [], result_sheet: [] },
      extraData: { exam_lists: [], subjects: [] },
    };
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
            field_name: this.field_name,
            search_keyword: this.search_keyword,
          };

          axios
            .get("/tabulation-sheet", { params: params })
            .then((res) => {
              this.result = res.data;
            })
            .catch((err) => console.log(err))
            .finally((alw) => (this.$root.submit = false));
        }
      });
    },

    activeClass() {
      return "badge rounded-pill text-success bg-light-success p-1 text-uppercase px-3 w-100";
    },
    errorClass() {
      return "badge rounded-pill text-danger bg-light-danger p-1 text-uppercase px-3 w-100";
    },
  },

  created() {
    this.setBreadcrumbs(this.model, "view", "Tabulation Sheet", addOrBack); // Set Breadcrumbs

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
  },
};
</script>