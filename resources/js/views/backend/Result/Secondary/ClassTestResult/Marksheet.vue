<template>
  <div class="col-12" v-if="!$root.spinner">
    <div class="card">
      <div class="card-body" style="background: #efefef">
        <div class="row mb-3">
          <div class="col-12">
            <!-- <a
              href="javascript:void(0)"
              class="btn btn-info btn-xs float-end"
              @click="downloadMarksheet(data.id)"
            >
              <div v-if="$root.submit" class="loading">processing..</div>
              <span v-else>
                <i class="bx bx-download"></i> Download Marksheet</span
              >
            </a> -->
            <a
              class="btn btn-xs btn-success float-end me-4"
              @click="print('printArea', 'Class Test Result')"
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
                <tbody>
                  <tr>
                    <th style="width: 20%">Software ID</th>
                    <td style="width: 30%">
                      {{ data.student ? data.student.software_id : "" }}
                    </td>
                    <th>Session</th>
                    <td style="width: 30%">
                      {{
                        data.class_test_result.academic_session
                          ? data.class_test_result.academic_session.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th style="width: 15%">Name of Student</th>
                    <td>{{ data.student ? data.student.name_en : "" }}</td>
                    <th>Campus</th>
                    <td>
                      {{
                        data.class_test_result.campus
                          ? data.class_test_result.campus.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Roll Number</th>
                    <td>{{ data.student ? data.student.roll_number : "" }}</td>
                    <th>Shift</th>
                    <td>
                      {{
                        data.class_test_result.shift
                          ? data.class_test_result.shift.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Medium</th>
                    <td>
                      {{
                        data.class_test_result.medium
                          ? data.class_test_result.medium.name
                          : ""
                      }}
                    </td>
                    <th>Class</th>
                    <td>
                      {{
                        data.class_test_result.academic_class
                          ? data.class_test_result.academic_class.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Group</th>
                    <td>
                      {{
                        data.class_test_result.group
                          ? data.class_test_result.group.name
                          : ""
                      }}
                    </td>
                    <th>Section</th>
                    <td>
                      {{
                        data.class_test_result.section_class
                          ? data.class_test_result.section.name
                          : ""
                      }}
                    </td>
                  </tr>
                  <tr>
                    <th>Exam</th>
                    <td colspan="3">
                      {{
                        data.class_test_result.exam
                          ? data.class_test_result.exam.name
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
                    <th class="text-center">Exam Mark</th>
                    <th class="text-center">Pass Mark</th>
                    <th class="text-center">Mark</th>
                    <th class="text-center">Status</th>
                  </tr>
                </thead>

                <tbody v-if="Object.keys(data.marks).length > 0">
                  <tr v-for="(mark, key) in data.marks" :key="key">
                    <td class="text-center">{{ key + 1 }}</td>
                    <td>{{ mark.subject ? mark.subject.name_en : "" }}</td>
                    <td class="text-center">{{ mark.exam_mark }}</td>
                    <td class="text-center">{{ mark.pass_mark }}</td>
                    <td class="text-center">{{ mark.mark }}</td>
                    <td class="text-center">
                      <span
                        :class="
                          mark.result_status == 'PASSED'
                            ? activeClass()
                            : errorClass()
                        "
                      >
                        <b>
                          <i class="bx bxs-circle me-1"></i>
                          {{ mark.result_status }}
                        </b>
                      </span>
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
const model = "secondaryClassTestResult";

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
      "Secondary Class Test Marksheet",
      addOrBack
    ); // Set Breadcrumbs
    this.get_data(
      `secondaryClassTestResult-marksheet/${this.$route.params.id}`
    );
  },
};
</script>

<style scoped>
.ms-bg-banner {
  background-image: url("./");
}
</style>