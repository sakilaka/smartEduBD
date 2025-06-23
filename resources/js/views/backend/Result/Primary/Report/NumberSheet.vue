<template>
  <div class="col-12" v-if="!$root.spinner">
    <SearchParams
      title="Search Students"
      :data="data"
      :subject="true"
      :search_keyword="false"
      :validation="validation"
      @search-event="search"
    />

    <div class="card border">
      <div class="card-header">
        <div class="row">
          <div class="col-4">
            <h6 class="mb-0 text-uppercase">Students Number Sheet</h6>
          </div>
          <div class="col-8 print-btn">
            <PrintDownload
              :excel="false"
              v-if="result.result && Object.keys(result.result).length > 0"
            />
          </div>
        </div>
      </div>

      <div id="printArea">
        <div class="card-body">
          <InstitutionInfo
            title="Number Sheet"
            :data="result.result"
            :subject="subject"
            v-if="result.result && Object.keys(result.result).length > 0"
          />
          <div v-else class="text-center py-2">No result found.</div>

          <table class="table table-bordered table-hover table-striped mb-0">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Student Name</th>
                <th class="text-center">Roll Number</th>
                <th class="text-center">CT/CA</th>
                <th class="text-center">CQ</th>
                <th class="text-center">MCQ</th>
              </tr>
            </thead>
            <tbody
              v-if="result.students && Object.keys(result.students).length > 0"
            >
              <tr v-for="(std, key) in result.students" :key="key">
                <th class="text-center">{{ key + 1 }}</th>
                <td>{{ std.name_en }}</td>
                <td class="text-center">{{ std?.profile?.roll_number }}</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
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
import InstitutionInfo from "@views/backend/Result/elements/InstitutionInfo";
import SearchParams from "@views/backend/Result/elements/SearchParams";
import PrintDownload from "@views/backend/Result/elements/PrintDownload";

// define model name
const model = "primaryResult";

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
      result: { result: {}, students: [] },
      exam_lists: [],

      subject_id: null,
      subjects: [],
      subject: {},
    };
  },

  watch: {
    "data.academic_class_id": function (id) {
      let params = {
        institution_id: this.data.institution_id,
        medium_id: this.data.medium_id,
        academic_class_id: this.data.academic_class_id,
      };
      this.get_data("primary-classwise-subjects", "subjects", params, false);
    },
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
            .get("/primaryResult-number-sheet", { params: params })
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

    selectSubject(id) {
      let find = this.subjects.find((e) => e.subject_id == id);
      this.subject = find && find.subject ? find.subject : {};
    },

    reqClass() {},
  },

  created() {
    this.setBreadcrumbs(
      this.model,
      "view",
      "Primary Result Number Sheet",
      addOrBack
    );

    this.get_data("get-exam", "exam_lists", {
      allData: true,
      exam_type: "term",
      institution_category_id: 1,
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