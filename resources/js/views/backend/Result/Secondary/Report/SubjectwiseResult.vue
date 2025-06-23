<template>
  <div v-if="!$root.spinner" class="col-12">
    <SearchParams
      :data="data"
      :subject="true"
      :validation="validation"
      @search-event="search"
      :category="2"
    />

    <!-- Student List -->
    <div class="card border">
      <div class="card-header">
        <div class="row">
          <div class="col-4">
            <h6 class="mb-0 text-uppercase">Students Result</h6>
          </div>
          <div v-if="Object.keys(data.details).length > 0" class="col-8">
            <PrintDownload />
          </div>
        </div>
      </div>

      <div v-if="Object.keys(data.details).length > 0" id="printArea">
        <div class="card-body">
          <institution-info :data="data">
            <tr>
              <th>Subject Name</th>
              <td colspan="5">
                {{ subject.subject ? subject.subject.name_en : "" }}
              </td>
            </tr>
          </institution-info>

          <div class="table-fixed printArea">
            <table class="table table-bordered table-hover table-striped mb-0">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th>Software ID</th>
                  <th>Student Name</th>
                  <th class="text-center">Roll Number</th>
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
                    Practical
                    <small>({{ subject.practical_mark }})</small>
                  </th>
                  <th class="text-center">Obtained Mark</th>
                  <th class="text-center">Total Mark</th>
                  <th class="text-center">GRADE</th>
                  <th class="text-center">GPA</th>
                  <th class="text-center" style="width: 10%">Status</th>
                </tr>
              </thead>
              <tbody
                v-if="data.details && Object.keys(data.details).length > 0"
              >
                <tr v-for="(std, key) in data.details" :key="key">
                  <th class="text-center">{{ key + 1 }}</th>
                  <td>
                    <router-link
                      :to="{
                        name: 'secondaryResult.marksheet',
                        params: { id: std.id },
                      }"
                      target="_blank"
                    >
                      {{ std.software_id }}
                    </router-link>
                  </td>
                  <td>{{ std.name_en }}</td>
                  <td class="text-center">{{ std.roll_number }}</td>
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

//json fields for export excel
const json_fields = {
  "Software ID": "software_id",
  "Roll Number": "roll_number",
  Name: "name_en",
  "CT Mark": "ct_mark",
  "CQ Mark": "cq_mark",
  "MCQ Mark": "mcq_mark",
  "Obtained Mark": "obtained_mark",
  "Total Mark": "total_mark",
  gpa: "gpa",
  "Letter Grade": "letter_grade",
};

export default {
  components: { InstitutionInfo, SearchParams, PrintDownload },

  data() {
    return {
      model: model,
      search_filter: true,
      excel_header: [],
      json_fields: json_fields,
      fields_name: {
        software_id: "Software ID",
        name_en: "Name",
        roll_number: "Roll Number",
      },
      field_name: "roll_number",
      search_keyword: "",
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

  watch: {
    "data.academic_class_id": function (id) {
      let params = {
        institution_id: this.data.institution_id,
        medium_id: this.data.medium_id,
        academic_class_id: this.data.academic_class_id,
      };
      this.get_data("secondary-classwise-subjects", "subjects", params, false);
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
            subject_id: this.subject_id,
            exam_id: this.data.exam_id,
            type: this.type,
            field_name: this.field_name,
            search_keyword: this.search_keyword,
          };

          axios
            .get("/secondaryResult-subjectwise", { params: params })
            .then((res) => {
              if (res.status == 200) {
                this.search_filter = false;
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
      let subject = this.subjects.find((e) => e.subject_id == id);
      this.subject = subject ? subject : {};
      this.data.details = [];
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
    this.setBreadcrumbs(
      this.model,
      "view",
      "Secondary Subject Wise Result",
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
    subject_id: function (value = null) {
      return Validator.value(value).required("Exam is required");
    },
  },
};
</script>