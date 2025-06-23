<template>
  <div class="col-12" v-if="!$root.spinner">
    <SearchParams
      :data="data"
      :search_keyword="false"
      :validation="validation"
      :category="2"
      @search-event="search"
    />

    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col-4">
            <h6 class="mb-0 text-uppercase">Tabulation Sheet</h6>
          </div>
          <div v-if="Object.keys(result.result_sheet).length > 0" class="col-8">
            <PrintDownload :excel="false" />
          </div>
        </div>
      </div>

      <div v-if="Object.keys(result.result_sheet).length > 0" id="printArea">
        <div class="card-body">
          <InstitutionInfo :data="result.result" />

          <div class="table-responsive">
            <table
              class="table table-bordered table-hover table-striped align-middle"
              style="font-size: 13px"
            >
              <thead>
                <tr>
                  <th rowspan="2" class="align-middle text-center">SL.</th>
                  <th rowspan="2" class="align-middle text-center">
                    Software ID
                  </th>
                  <th rowspan="2" class="align-middle text-center">
                    Roll Number
                  </th>
                  <th rowspan="2" class="align-middle">Student Name</th>
                  <template v-if="Object.keys(result.subjects).length > 0">
                    <th
                      colspan="6"
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
                  <td class="text-center">{{ std.software_id }}</td>
                  <td class="text-center">{{ std.roll_number }}</td>
                  <td>{{ std.name_en }}</td>

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

      <span v-else class="text-center py-5">No result found.</span>
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
      fields_name: {
        software_id: "Software ID",
        name_en: "Name",
        roll_number: "Roll Number",
      },
      subject_id: null,
      field_name: "roll_number",
      search_keyword: "",
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
      result: { result: {}, subjects: [], result_sheet: [] },
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
            field_name: this.field_name,
            search_keyword: this.search_keyword,
          };

          axios
            .get("/secondaryResult-tabulation-sheet", { params: params })
            .then((res) => {
              this.search_filter = false;
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

    this.get_data("get-exam", "exam_lists", {
      allData: true,
      exam_type: "term",
      institution_category_id: 2,
    });
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