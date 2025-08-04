<template>
  <div class="row">
    <div class="card border">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">Search Result</h6>
      </div>
      <div class="card-body py-3">
        <div class="row">
          <!------------ Single Input ------------>
          <div class="col-1 pe-0">
            <Label title="Type" :req="true" />
            <select
              v-model="search_data.type"
              class="form-select form-select-sm"
            >
              <option value>ALL</option>
              <option value="merit">Merit</option>
              <option value="unmerit">Unmerit</option>
            </select>
          </div>

          <!------------ Single Input ------------>
          <div class="col-1 pe-0 pt-1">
            <select
              v-model="search_data.field_name"
              class="form-control form-control-sm mt-3"
            >
              <option value>--Select One--</option>
              <template v-for="(fname, fKey) in fields_name">
                <option :value="fKey" :key="fKey">{{ fname }}</option>
              </template>
            </select>
          </div>
          <div class="col-2 px-0 pt-1">
            <input
              type="text"
              v-model="search_data.value"
              class="form-control form-control-sm mt-3"
              placeholder="Type your text"
            />
          </div>
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

    <!-- Student List -->
    <!-- Student List -->
    <div v-if="!$root.spinner" class="card border">
      <div class="card-header">
        <div class="row">
          <div class="col-6">
            <h6 class="mb-0 text-uppercase">Students Result</h6>
          </div>
          <div class="col-6">
            <a class="btn btn-xs btn-info float-end" href="javascript:;">
              <download-excel
                v-if="data.details"
                :data="data.details"
                :fields="json_fields"
                :header="excel_header"
                name="result.xls"
                style="cursor: pointer"
              >
                <i class="bx bx-list-ul"></i> EXCEL
              </download-excel>
            </a>
            <a
              class="btn btn-xs btn-success float-end me-3"
              @click="print('printArea', 'Result')"
              href="javascript:;"
            >
              <i class="bx bx-printer"></i> PRINT
            </a>
          </div>
        </div>
      </div>

      <div class="card-body p-3" id="printArea">
        <div class="row g-3 mb-3">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th style="width: 20%">Session</th>
                <td style="width: 30%">
                  {{ data.academic_session ? data.academic_session.name : "" }}
                </td>
                <th style="width: 20%">Academic Level</th>
                <td>
                  {{ data.qualification ? data.qualification.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Department/Group</th>
                <td>
                  {{ data.department ? data.department.name : "" }}
                </td>
                <th>Class</th>
                <td>
                  {{ data.academic_class ? data.academic_class.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Exam</th>
                <td>
                  {{ data.exam ? data.exam.name : "" }}
                </td>
              </tr>
            </thead>
          </table>
        </div>

        <div class="row g-3 table-fixed">
          <table class="table table-bordered table-hover table-striped mb-0">
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Software ID</th>
                <th>Student Name</th>
                <th>College Roll</th>
                <template v-if="search_data.type == 'unmerit'">
                  <th class="text-center">Total Failed</th>
                  <th>Failed Subjects</th>
                </template>
                <th class="text-center">Total Mark</th>
                <th class="text-center" style="width: 10%">Status</th>
              </tr>
            </thead>
            <tbody v-if="data.details && Object.keys(data.details).length > 0">
              <tr v-for="(std, key) in data.details" :key="key">
                <th class="text-center">{{ key + 1 }}</th>
                <td>
                  <router-link
                    :to="{
                      name: 'classTestResult.marksheet',
                      params: { id: std.id },
                    }"
                    target="_blank"
                  >
                    {{ std.student_id }}
                  </router-link>
                </td>
                <td>{{ std.name }}</td>
                <td>{{ std.college_roll }}</td>
                <template v-if="search_data.type == 'unmerit'">
                  <td class="text-center">
                    <span v-if="std.marks && Object.keys(std.marks).length > 0">
                      {{ Object.keys(std.marks).length }}
                    </span>
                  </td>
                  <td>
                    <ul
                      class="p-2"
                      v-if="std.marks && Object.keys(std.marks).length > 0"
                    >
                      <li
                        class="p-0"
                        v-for="(sub, markKey) in std.marks"
                        :key="'markKey' + markKey"
                      >
                        {{ sub.subject ? sub.subject.name_en : "" }}
                      </li>
                    </ul>
                  </td>
                </template>
                <td class="text-center">{{ std.total_mark }}</td>
                <td class="text-center">
                  <span
                    :class="
                      std.result_status == 'PASSED'
                        ? activeClass()
                        : errorClass()
                    "
                  >
                    <b>
                      <i class="bx bxs-circle me-1"></i>
                      {{ std.result_status }}
                    </b>
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
  </div>
</template>

<script>
// define model name
const model = "classTestResult";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Class Test Result",
  icon: "left-arrow-alt",
};

//json fields for export excel
const json_fields = {
  "Software ID": "student_id",
  "College Roll": "college_roll",
  Name: "name",
  "Total Mark": "total_mark",
  gpa: "gpa",
  "Letter Grade": "letter_grade",
  "Total Failed": {
    callback: (std) => {
      let failed = 0;
      if (std.marks && Object.keys(std.marks).length > 0) {
        failed = Object.keys(std.marks).length;
      }
      return `${failed} `;
    },
  },
  "Failed Subjects": {
    callback: (std) => {
      let subjects = "";
      if (std.marks && Object.keys(std.marks).length > 0) {
        std.marks.forEach((sub) => {
          let subjectName = sub.subject ? sub.subject.name_en : "";
          subjects += subjectName + ", ";
        });
      }
      return `${subjects}`;
    },
  },
};

export default {
  data() {
    return {
      model: model,
      excel_header: [],
      json_fields: json_fields,
      fields_name: {
        student_id: "Software ID",
        name: "Name",
        mobile: "Mobile",
        college_roll: "College Roll",
      },

      search_data: {
        result_view: true,
        type: "",
        field_name: "college_roll",
        value: "",
      },

      data: {},
    };
  },

  methods: {
    search() {
      this.$root.spinner = true;
      axios
        .get(`${this.model}/${this.$route.params.id}`, {
          params: this.search_data,
        })
        .then((res) => {
          if (res.status == 200) {
            this.data = res.data.result;

            if (res.data.excel_header) {
              this.excel_header = res.data.excel_header;
            }
          }
        })
        .catch((err) => console.log(err))
        .finally((alw) => (this.$root.spinner = false));
    },
    activeClass() {
      return "badge rounded-pill text-success bg-light-success p-1 text-uppercase px-3 w-100";
    },
    errorClass() {
      return "badge rounded-pill text-danger bg-light-danger p-1 text-uppercase px-3 w-100";
    },
  },
  created() {
    this.setBreadcrumbs(this.model, "view", "Class Test Result", addOrBack);
    this.search();
  },
};
</script>