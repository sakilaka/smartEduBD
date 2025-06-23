<template>
  <div class="col-12" v-if="!$root.spinner">
    <div class="card">
      <div class="card-body" style="background: #efefef">
        <div class="row mb-3">
          <div class="col-12">
            <a
              href="javascript:void(0)"
              class="btn btn-info btn-xs float-end"
              @click="downloadMarksheet(data.id)"
            >
              <div v-if="$root.submit" class="loading">processing..</div>
              <span v-else>
                <i class="bx bx-download"></i> Download Marksheet</span
              >
            </a>
            <a
              class="btn btn-xs btn-success float-end me-4"
              @click="print('printArea', 'Result')"
              href="javascript:;"
            >
              <i class="bx bx-printer"></i> PRINT
            </a>
          </div>
        </div>

        <div class="row d-flex justify-content-center">
          <div class="col-12 col-lg-8 bg-white p-4">
            <div id="printArea">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th style="width: 20%">Institution</th>
                    <td style="width: 30%">
                      {{
                        data.result.institution
                          ? data.result.institution.name
                          : ""
                      }}
                    </td>
                    <th style="width: 20%">Academic Session</th>
                    <td>
                      {{
                        data.result.academic_session
                          ? data.result.academic_session.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Campus</th>
                    <td>
                      {{ data.result.campus ? data.result.campus.name : "" }}
                    </td>
                    <th>Shift</th>
                    <td>
                      {{ data.result.shift ? data.result.shift.name : "" }}
                    </td>
                  </tr>
                  <tr>
                    <th>Medium</th>
                    <td>
                      {{ data.result.medium ? data.result.medium.name : "" }}
                    </td>
                    <th>Class</th>
                    <td>
                      {{
                        data.result.academic_class
                          ? data.result.academic_class.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Group</th>
                    <td>
                      {{ data.result.group ? data.result.group.name : "" }}
                    </td>
                    <th>Section</th>
                    <td>
                      {{ data.result.section ? data.result.section.name : "" }}
                    </td>
                  </tr>
                  <tr>
                    <th>Exam</th>
                    <td colspan="3">
                      {{ data.result.exam ? data.result.exam.name : "" }}
                    </td>
                  </tr>
                  <tr>
                    <th>Student Name</th>
                    <td>
                      {{ data.student ? data.student.name_en : "" }}
                      ({{ data.student ? data.student.roll_number : "" }})
                    </td>
                    <th>Software ID</th>
                    <td>
                      {{ data.student ? data.student.software_id : "" }}
                    </td>
                  </tr>
                </thead>
              </table>

              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>SI. No.</th>
                    <th>Name of Subjects</th>
                    <th class="text-center">CT Mark</th>
                    <th class="text-center">CQ Mark</th>
                    <th class="text-center">MCQ Mark</th>
                    <th class="text-center">Obtained Marks</th>
                    <th class="text-center">Total Marks</th>
                    <th class="text-center">Letter Grade</th>
                    <th class="text-center">Grade Point</th>
                    <th class="text-center">GPA</th>
                    <th class="text-center">GRADE</th>
                  </tr>
                </thead>

                <tbody v-if="Object.keys(data.marks).length > 0">
                  <tr v-for="(mark, key) in data.marks" :key="key">
                    <td>{{ key + 1 }}</td>
                    <td>{{ mark.subject.name_en }}</td>
                    <td class="text-center">{{ Number(mark.ct_mark) }}</td>
                    <td class="text-center">{{ Number(mark.cq_mark) }}</td>
                    <td class="text-center">{{ Number(mark.mcq_mark) }}</td>
                    <td class="text-center">
                      {{ Number(mark.obtained_mark) }}
                    </td>
                    <td class="text-center">{{ Number(mark.total_mark) }}</td>
                    <td class="text-center">{{ mark.letter_grade }}</td>
                    <td class="text-center">{{ mark.gpa }}</td>
                    <td
                      class="align-middle text-center"
                      :rowspan="data.marks.length"
                      v-if="key == 0"
                    >
                      {{ data.gpa }}
                    </td>
                    <td
                      class="align-middle text-center"
                      :rowspan="data.marks.length"
                      v-if="key == 0"
                    >
                      {{ data.letter_grade }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// define model name
const model = "primaryResult";

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
      data: { result: {}, marks: [] },
    };
  },

  methods: {
    downloadMarksheet(id) {
      this.$root.submit = true;
      let url = `${this.$root.baseurl}/admin/primary-download-marksheet/${id}`;
      window.location.href = url;
      setTimeout(() => (this.$root.submit = false), 2500);
    },
  },

  created() {
    this.setBreadcrumbs(this.model, "view", "Primary Marksheet", addOrBack);
    this.get_data(`${this.model}-marksheet/${this.$route.params.id}`);
  },
};
</script>

<style scoped>
.table > :not(caption) > * > * {
  padding: 0.2rem 0.5rem;
}
.table tr td,
.table tr th {
  font-size: 13px;
}
</style>