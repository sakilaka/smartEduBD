<template>
  <div v-if="!$root.spinner">
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
            :col="2"
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

    <!-- Exam Info -->
    <div id="printArea">
      <div class="card border">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">Students Result</h6>
            </div>
            <div class="col-6">
              <a
                class="btn btn-xs btn-success float-end action"
                @click="print('printArea', 'Result')"
                href="javascript:;"
              >
                <i class="bx bx-printer"></i> PRINT
              </a>
            </div>
          </div>
        </div>

        <div class="card-body p-3">
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
        </div>
      </div>

      <div class="row">
        <!-- Total Grade Summary -->
        <div class="col-xl-4 col-12">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Total Grade Summary</h6>
            </div>
            <div class="card-body p-3">
              <table
                v-if="result.grade_summary.length > 0"
                class="table table-bordered text-center"
              >
                <thead>
                  <tr>
                    <th>GRADE</th>
                    <th>Total Student</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(gSumm, gKey) in result.grade_summary"
                    :key="`gKey${gKey}`"
                  >
                    <th>{{ gSumm.letter_grade }}</th>
                    <th>{{ gSumm.grade_count }}</th>
                  </tr>
                </tbody>
              </table>

              <p v-else class="my-4 text-center">No data found</p>
            </div>
          </div>
        </div>

        <!-- Subject Wise Grade Summary -->
        <div class="col-xl-8 col-12">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Subject Wise Grade Summary</h6>
            </div>
            <div class="card-body p-3">
              <table
                v-if="result.grade_summary.length > 0"
                class="table table-bordered text-center"
              >
                <thead>
                  <tr>
                    <th :rowspan="2" class="align-middle" style="width: 300px">
                      Subject
                    </th>
                    <th colspan="7">GRADE WISE STUDENT COUNT</th>
                  </tr>
                  <tr>
                    <th
                      v-for="(grade, gkey) in grades"
                      :key="`gkey${gkey}`"
                      style="width: 100px"
                    >
                      {{ grade }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <template v-for="(sSumm, sKey) in result.subject_summary">
                    <template v-for="(sub, subKey) in sSumm">
                      <tr
                        v-if="subKey == 0"
                        :key="`sKey${sKey + subKey}${sub.subject_id}`"
                      >
                        <td
                          rowspan="2"
                          class="align-middle"
                          :key="`1subKey${subKey + subKey}`"
                        >
                          {{ sub.subject_name }}
                        </td>
                      </tr>
                      <tr
                        v-if="subKey == 0"
                        :key="`1sKey${sKey + subKey}${sub.subject_id}`"
                      >
                        <td
                          v-for="(grade, gkey) in grades"
                          :key="`gkey${gkey}`"
                          style="width: 100px"
                        >
                          <span
                            v-if="sSumm.find((e) => e.letter_grade == grade)"
                          >
                            {{
                              sSumm.find((e) => e.letter_grade == grade)
                                .grade_count
                            }}
                          </span>
                          <span class="text-muted" v-else> -- </span>
                        </td>
                      </tr>
                    </template>
                  </template>
                </tbody>
              </table>

              <p v-else class="my-4 text-center">No data found</p>
            </div>
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
      grades: ["A+", "A", "A-", "B", "C", "D", "F"],
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
        details: [],
      },
      result: { grade_summary: [], subject_summary: [] },
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
          };

          axios
            .get("/result-grade-summary", { params: params })
            .then((res) => {
              if (res.status == 200) {
                this.result = res.data;
              }
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
    this.setBreadcrumbs(this.model, "view", "Result Grade Summary", addOrBack);

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