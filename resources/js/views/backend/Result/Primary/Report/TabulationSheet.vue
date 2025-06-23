<template>
  <div class="col-12" v-if="!$root.spinner">
    <SearchParams
      :data="data"
      :search_keyword="false"
      :validation="validation"
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
        <div
          v-for="(subjects, subChunk) in result.subject_chunks"
          :key="`sub${subChunk}`"
          class="card-body page-break"
          style="font-size: 13px"
          :class="`tabulation_pdf_${subChunk}`"
          :ref="`tabulation_pdf_${subChunk}`"
        >
          <InstitutionInfo :data="result.result" title="Tabulation Sheet" />

          <div class="table-responsive tabulation printArea table-fixed">
            <table
              class="table table-bordered table-hover table-striped align-middle"
              style="font-size: 12px"
            >
              <thead>
                <tr>
                  <th
                    rowspan="2"
                    class="align-middle text-center"
                    style="width: 20px"
                  >
                    Roll
                  </th>
                  <th
                    rowspan="2"
                    class="align-middle text-left"
                    style="width: 100px"
                  >
                    Student Name
                  </th>
                  <template v-if="Object.keys(subjects).length > 0">
                    <th
                      colspan="6"
                      v-for="(sub, subKey) in subjects"
                      :key="`sub${subKey}`"
                      class="text-center"
                    >
                      {{ sub.subject ? sub.subject.name_en : "" }}
                    </th>
                  </template>
                  <th
                    rowspan="2"
                    class="align-middle text-center"
                    style="width: 10px"
                  >
                    T.M.
                  </th>
                  <th
                    rowspan="2"
                    class="align-middle text-center"
                    style="width: 10px"
                  >
                    GPA
                  </th>
                  <th
                    rowspan="2"
                    class="align-middle text-center"
                    style="width: 10px"
                  >
                    G
                  </th>
                </tr>
                <template v-if="Object.keys(subjects).length > 0">
                  <tr class="text-center">
                    <template v-for="(sub, sbKey) in subjects">
                      <th :key="`1marks_heading${sbKey}`" style="width: 10px">
                        CT
                      </th>
                      <th :key="`2marks_heading${sbKey}`" style="width: 10px">
                        CQ
                      </th>
                      <th :key="`3marks_heading${sbKey}`" style="width: 10px">
                        MCQ
                      </th>
                      <th :key="`5marks_heading${sbKey}`" style="width: 10px">
                        CON.
                      </th>
                      <th :key="`6marks_heading${sbKey}`" style="width: 10px">
                        TM
                      </th>
                      <th :key="`7marks_heading${sbKey}`" style="width: 10px">
                        G
                      </th>
                    </template>
                  </tr>
                </template>
              </thead>

              <tbody v-if="Object.keys(result.result_sheet).length > 0">
                <tr v-for="(std, key) in result.result_sheet" :key="key">
                  <td class="text-center">{{ std.roll_number }}</td>
                  <td>{{ std.name_en }}</td>

                  <template
                    v-if="
                      std.subject_chunks[subChunk] &&
                      Object.keys(std.subject_chunks[subChunk]).length > 0
                    "
                  >
                    <template
                      v-for="(mark, rmKey) in std.subject_chunks[subChunk]"
                    >
                      <td
                        class="text-center"
                        :key="`1marks${rmKey}`"
                        style="width: 10px"
                      >
                        {{ mark.ct_mark | formatNumber }}
                      </td>
                      <td
                        class="text-center"
                        :key="`2marks${rmKey}`"
                        style="width: 10px"
                      >
                        {{ mark.cq_mark | formatNumber }}
                      </td>
                      <td
                        class="text-center"
                        :key="`3marks${rmKey}`"
                        style="width: 10px"
                      >
                        {{ mark.mcq_mark | formatNumber }}
                      </td>
                      <td
                        class="text-center"
                        :key="`5marks${rmKey}`"
                        style="width: 10px"
                      >
                        {{ mark.obtained_mark | formatNumber }}
                      </td>
                      <td
                        class="text-center"
                        :key="`6marks${rmKey}`"
                        style="width: 10px"
                      >
                        {{ mark.total_mark | formatNumber }}
                      </td>
                      <td
                        class="text-center"
                        :key="`7marks${rmKey}`"
                        style="width: 10px"
                      >
                        {{ mark.letter_grade }}
                      </td>
                    </template>
                  </template>

                  <td class="text-center" style="width: 10px">
                    {{ std.total_mark | formatNumber }}
                  </td>
                  <td class="text-center" style="width: 10px">
                    {{ std.gpa | formatNumber }}
                  </td>
                  <td class="text-center" style="width: 10px">
                    {{ std.letter_grade }}
                  </td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr>
                  <td colspan="9" class="text-center">No data Found</td>
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
import jsPDF from "jspdf";
import html2canvas from "html2canvas";

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
      result: {
        result: {},
        subjects: [],
        subject_chunks: [],
        result_sheet: [],
      },
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
            .get("/primaryResult-tabulation-sheet", { params: params })
            .then((res) => {
              this.search_filter = false;
              this.result = res.data;
            })
            .catch((err) => console.log(err))
            .finally((alw) => (this.$root.submit = false));
        }
      });
    },

    downloadPDF() {
      if (this.$root.submit) return false;

      this.$root.submit = true;
      $(".table-responsive").removeClass("table-fixed");
      for (let index = 0; index < this.result.subject_chunks.length; index++) {
        const chunkElement = document.querySelector(`.tabulation_pdf_${index}`);

        if (chunkElement) {
          const pdf = new jsPDF("landscape", "mm", "a4");

          html2canvas(chunkElement, {
            scale: 2,
            useCORS: true,
          }).then((canvas) => {
            const imgData = canvas.toDataURL("image/png");
            const imgWidth = 297;
            const pageHeight = 210;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            let heightLeft = imgHeight;
            let position = 0;

            // Add the first page with the image
            pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            // If the content exceeds one page, add additional pages
            while (heightLeft > 0) {
              position = heightLeft - imgHeight;
              pdf.addPage();
              pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
              heightLeft -= pageHeight;
            }

            // Save the generated PDF
            pdf.save(`tabulation_sheet_${index + 1}.pdf`);

            if (index === this.result.subject_chunks.length - 1) {
              setTimeout(() => {
                this.$root.submit = false;
                $(".table-responsive").addClass("table-fixed");
              }, 500);
            }
          });
        }
      }
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
      "Primary Tabulation Sheet",
      addOrBack
    ); // Set Breadcrumbs

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

<style scoped>
.tabulation table {
  border-collapse: collapse;
}
.tabulation table td {
  padding: 0.2rem 0.2rem;
  font-size: 12px;
  margin: 0;
}
.tabulation table th {
  padding: 0.2rem 0.2rem;
  font-size: 11px;
  margin: 0;
}

@media print {
  .page-break {
    page-break-inside: avoid;
    page-break-after: always;
  }
  th,
  td {
    vertical-align: middle;
    text-align: center;
  }
}
</style>