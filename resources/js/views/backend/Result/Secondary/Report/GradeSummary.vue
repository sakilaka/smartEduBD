<template>
  <div class="col-12" v-if="!$root.spinner">
    <SearchParams
      :data="data"
      :search_keyword="false"
      :validation="validation"
      @search-event="search"
      :category="2"
    />

    <div id="printArea">
      <div class="card border">
        <div class="card-header">
          <div class="row">
            <div class="col-4">
              <h6 class="mb-0 text-uppercase">Students Result</h6>
            </div>
            <div
              v-if="Object.keys(result.grade_summary).length > 0"
              class="col-8 print-btn"
            >
              <PrintDownload :excel="false" />
            </div>
          </div>
        </div>

        <div class="card-body">
          <InstitutionInfo
            title="Grade Summary"
            :data="result.result"
            v-if="Object.keys(result.grade_summary).length > 0"
          />
          <div v-else class="text-center py-2">No result found.</div>
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
import InstitutionInfo from "@views/backend/Result/elements/InstitutionInfo";
import SearchParams from "@views/backend/Result/elements/SearchParams";
import PrintDownload from "@views/backend/Result/elements/PrintDownload";

// define model name
const model = "secondaryResult";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { InstitutionInfo, SearchParams, PrintDownload },

  data() {
    return {
      model: model,
      search_filter: true,
      grades: ["A+", "A", "A-", "B", "C", "D", "F"],
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
      result: { result: {}, grade_summary: [], subject_summary: [] },
      exam_lists: [],
      subjects: [],
    };
  },

  methods: {
    search() {
      if (this.$root.submit) return false;

      this.$validate().then((res) => {
        if (res) {
          this.$root.submit = true;

          let params = {
            institution_id: this.data.institution_id,
            academic_session_id: this.data.academic_session_id,
            campus_id: this.data.campus_id,
            shift_id: this.data.shift_id,
            medium_id: this.data.medium_id,
            academic_class_id: this.data.academic_class_id,
            group_id: this.data.group_id,
            section_id: this.data.section_id,
            exam_id: this.data.exam_id,
          };

          axios
            .get("/secondaryResult-grade-summary", { params: params })
            .then((res) => {
              if (res.status == 200) {
                this.search_filter = false;
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
    this.setBreadcrumbs(
      this.model,
      "view",
      "Secondary Result Grade Summary",
      addOrBack
    );

    this.get_data("get-exam", "exam_lists", {
      allData: true,
      exam_type: "term",
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
  },
};
</script>