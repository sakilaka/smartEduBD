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
                    <th class="text-center align-middle">SL. No.</th>
                    <th style="width: 240px" class="align-middle">
                      Name of Subjects
                    </th>
                    <th class="text-center align-middle">CT Mark</th>
                    <th class="text-center align-middle">CQ Mark</th>
                    <th class="text-center align-middle">MCQ Mark</th>
                    <th class="text-center align-middle">Practical Mark</th>
                    <th class="text-center align-middle">Obtained Marks</th>
                    <th class="text-center align-middle">Total Marks</th>
                    <th class="text-center align-middle">Letter Grade</th>
                    <th class="text-center align-middle">Grade Point</th>
                    <th class="text-center align-middle">GPA</th>
                    <th class="text-center align-middle">GRADE</th>
                  </tr>
                </thead>

                <tbody v-if="Object.keys(data.marks).length > 0">
                  <template v-for="(mark, key) in data.marks">
                    <tr :key="key">
                      <th class="text-center">{{ mark.sorting }}</th>
                      <td>
                        <span v-if="mark.additional_subject">
                          <b>4th Subject Name:</b>
                        </span>
                        {{ mark.subject.name_en }}
                      </td>
                      <td class="text-center">{{ Number(mark.ct_mark) }}</td>
                      <td class="text-center">{{ Number(mark.cq_mark) }}</td>
                      <td class="text-center">{{ Number(mark.mcq_mark) }}</td>
                      <td class="text-center">
                        {{ Number(mark.practical_mark) }}
                      </td>
                      <td class="text-center">
                        {{ Number(mark.obtained_mark) }}
                      </td>
                      <td
                        :rowspan="mark.combined_subject ? 2 : ''"
                        class="text-center"
                        style="vertical-align: middle"
                      >
                        <span v-if="parseInt(mark.combined_mark) > 0">
                          {{ Number(mark.combined_mark * 2) | currency }}
                        </span>
                        <span v-else>{{ Number(mark.total_mark) }} </span>
                      </td>
                      <td
                        class="text-center"
                        style="vertical-align: middle"
                        :rowspan="mark.combined_subject ? 2 : ''"
                      >
                        <span v-if="mark.combined_letter_grade">
                          {{ mark.combined_letter_grade }}
                        </span>
                        <span v-else>{{ mark.letter_grade }}</span>
                      </td>
                      <td
                        class="text-center"
                        style="vertical-align: middle"
                        :rowspan="mark.combined_subject ? 2 : ''"
                      >
                        <span v-if="parseInt(mark.combined_gpa) > 0">
                          {{ mark.combined_gpa }}
                        </span>
                        <span v-else>{{ mark.gpa }}</span>
                      </td>
                      <td
                        class="align-middle text-center"
                        :rowspan="totalLength"
                        v-if="key == 0"
                      >
                        {{ data.gpa }}
                      </td>
                      <td
                        class="align-middle text-center"
                        :rowspan="totalLength"
                        v-if="key == 0"
                      >
                        {{ data.letter_grade }}
                      </td>
                    </tr>
                    <tr v-if="mark.combined_subject" :key="`${key}abc`">
                      <th class="text-center">
                        {{ mark.combined_subject.sorting }}
                      </th>
                      <td>{{ mark.combined_subject.subject.name_en }}</td>
                      <td class="text-center">
                        {{ mark.combined_subject.ct_mark }}
                      </td>
                      <td class="text-center">
                        {{ mark.combined_subject.cq_mark }}
                      </td>
                      <td class="text-center">
                        {{ mark.combined_subject.mcq_mark }}
                      </td>
                      <td class="text-center">
                        {{ mark.combined_subject.practical_mark }}
                      </td>
                      <td class="text-center">
                        {{ mark.combined_subject.obtained_mark }}
                      </td>
                    </tr>
                  </template>
                  <tr>
                    <th class="text-center">
                      {{ totalLength + 1 }}
                    </th>
                    <td>Grand Total with 4th subject</td>
                    <td colspan="5"></td>
                    <td class="text-center">{{ data.total_mark }}</td>
                    <td colspan="4"></td>
                  </tr>
                  <tr>
                    <th class="text-center">
                      {{ totalLength + 2 }}
                    </th>
                    <td>Grand Total without 4th subject.</td>
                    <td colspan="5"></td>
                    <td class="text-center">
                      {{ data.total_mark_without_additional }}
                    </td>
                    <td colspan="4"></td>
                  </tr>
                </tbody>
              </table>

              <div class="row fw-bold pt-1">
                <!-- <div class="col-6">
                  Merit position in Class :
                  {{ data.merit_position_in_class }}
                </div> -->
                <!-- <div v-if="data.letter_grade != 'F'" class="col-6">
                  Merit position in Section :
                  {{ data.merit_position_in_department }}
                </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// define model name
const model = "secondaryResult";

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

  computed: {
    totalLength() {
      if (this.data.marks.length > 0) {
        const lastElement = this.data.marks[this.data.marks.length - 1];
        return lastElement?.sorting;
      }
    },
  },

  methods: {
    downloadMarksheet(id) {
      this.$root.submit = true;
      let url = `${this.$root.baseurl}/admin/secondary-download-marksheet/${id}`;
      window.location.href = url;
      setTimeout(() => (this.$root.submit = false), 2500);
    },
  },

  created() {
    this.setBreadcrumbs(this.model, "view", "Secondary Marksheet", addOrBack);
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