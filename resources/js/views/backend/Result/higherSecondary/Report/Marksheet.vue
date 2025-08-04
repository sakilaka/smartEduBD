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
          <div class="col-12 col-lg-9 bg-white p-3">
            <div id="printArea">
              <div
                class="col-12 py-1 mb-2"
                style="border-bottom: 2px solid #ccc"
              >
                <div class="row">
                  <div class="col-3">
                    <img
                      v-if="$root.site"
                      :src="$root.site.logo"
                      class="logo"
                      style="height: 50px"
                    />
                  </div>
                  <div class="col-9">
                    <h3 class="document-type display-9">
                      {{ $root.site ? $root.site.title : "" }}
                    </h3>
                  </div>
                </div>
              </div>
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th style="width: 20%">Software ID</th>
                    <td style="width: 30%">
                      {{ data.student ? data.student.student_id : "" }}
                    </td>
                    <th>Session</th>
                    <td style="width: 30%">
                      {{
                        data.result.academic_session
                          ? data.result.academic_session.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th style="width: 15%">Name of Student</th>
                    <td>{{ data.student ? data.student.name : "" }}</td>
                    <th>Academic Level</th>
                    <td>
                      {{
                        data.result.qualification
                          ? data.result.qualification.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>College Roll</th>
                    <td>{{ data.student ? data.student.college_roll : "" }}</td>
                    <th>Department/Group</th>
                    <td>
                      {{
                        data.result.department
                          ? data.result.department.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Exam</th>
                    <td>
                      {{ data.result.exam ? data.result.exam.name : "" }}
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
                </tbody>
              </table>

              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th class="text-center">SI. No.</th>
                    <th>Name of Subjects</th>
                    <th class="text-center">CT Mark</th>
                    <th class="text-center">CQ Mark</th>
                    <th class="text-center">MCQ Mark</th>
                    <th class="text-center">Practical Mark</th>
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
                    <td class="text-center">{{ key + 1 }}</td>
                    <td>{{ mark.subject.name_en }}</td>
                    <td class="text-center">{{ mark.ct_mark }}</td>
                    <td class="text-center">{{ mark.cq_mark }}</td>
                    <td class="text-center">{{ mark.mcq_mark }}</td>
                    <td class="text-center">{{ mark.practical_mark }}</td>
                    <td class="text-center">{{ mark.obtained_mark }}</td>
                    <td class="text-center">{{ mark.total_mark }}</td>
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

              <!-- <div class="row fw-bold py-3">
                <div class="col-6">
                  Merit position in Class :
                  {{ data.merit_position_in_class }}
                </div>
                <div class="col-6">
                  Merit position in Dept/Group :
                  {{ data.merit_position_in_department }}
                </div>
              </div> -->
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
      data: { result: {}, marks: [] },
    };
  },

  methods: {
    downloadMarksheet(id) {
      this.$root.submit = true;
      let url = `${this.$root.baseurl}/admin/download-marksheet/${id}`;
      window.location.href = url;
      setTimeout(() => (this.$root.submit = false), 2500);
    },
  },

  async created() {
    this.setBreadcrumbs(this.model, "view", "Marksheet", addOrBack); // Set Breadcrumbs
    await this.get_data("marksheet", this.$route.params.id, "data"); // get data
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