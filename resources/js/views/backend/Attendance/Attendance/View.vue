<template>
  <div v-if="!$root.spinner">
    <div class="row d-flex justify-content-end">
      <div class="col-1">
        <a
          class="btn btn-sm btn-success"
          @click="printReport()"
          href="javascript:;"
        >
          <i class="bx bx-printer"></i> PRINT
        </a>
      </div>
    </div>
    <div class="row" id="printArea">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">ATTENDANCE INFO</h6>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-striped table-hover">
            <tbody>
              <tr>
                <th style="width: 15%">Date</th>
                <td style="width: 35%">{{ data.date }}</td>
                <th style="width: 15%"></th>
                <td style="width: 35%"></td>
              </tr>
              <tr>
                <th>Shift</th>
                <td>{{ data.shift.name }}</td>
                <th>Campus</th>
                <td>{{ data.campus.name }}</td>
              </tr>
              <tr>
                <th>Group</th>
                <td>{{ data.group.name }}</td>
                <th>Section</th>
                <td>{{ data.section.name }}</td>
              </tr>
              <tr>
                <th>Academic Class</th>
                <td>{{ data.academic_class.name }}</td>
                <th></th>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            STUDENTS ATTENDANCE

            <span class="float-end">
              Total:
              <span class="text-primary fw-bold">
                {{ data.total_student }}</span
              >
              &nbsp; || &nbsp; Present:
              <span class="text-success fw-bold">
                {{ data.present_student_count }}
              </span>
              &nbsp; || &nbsp; Absent:
              <span class="text-danger fw-bold">
                {{
                  parseInt(data.total_student) -
                  parseInt(data.present_student_count)
                }}
              </span>
            </span>
          </h6>
        </div>
        <div class="card-body">
          <div
            class="row g-3 report-table"
            style="height: 300px; overflow-y: scroll"
          >
            <table
              v-if="data.details && Object.keys(data.details).length > 0"
              class="table table-bordered table-striped table-hover"
            >
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th>Software ID</th>
                  <th>Roll Number</th>
                  <th>Student Name</th>
                  <th>Guardian Name</th>
                  <th>Guardian Mobile</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, key) in data.details" :key="key">
                  <td class="text-center">{{ key + 1 }}</td>
                  <td>{{ item.software_id }}</td>
                  <td>
                    {{
                      item.student && item.student.profile
                        ? item.student.profile.roll_number
                        : ""
                    }}
                  </td>
                  <td>
                    {{ item.student ? item.student.name_en : "" }}
                  </td>
                  <td>
                    {{
                      item.student && item.student.guardian
                        ? item.student.guardian.name_en
                        : ""
                    }}
                  </td>
                  <td>
                    {{
                      item.student && item.student.guardian
                        ? item.student.guardian.mobile
                        : ""
                    }}
                  </td>
                  <td class="text-center text-success">
                    <span :class="activeClass()" v-if="item.status == 'P'">
                      <i class="bx bxs-circle me-1"></i>PRESENT
                    </span>
                    <span :class="errorClass()" v-else>
                      <i class="bx bxs-circle me-1"></i>ABSENT
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
</template>

<script>
// define model name
const model = "attendance";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Attendance",
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      data: [],
    };
  },
  methods: {
    activeClass() {
      return "badge rounded-pill text-success bg-light-success p-1 text-uppercase px-3 w-100";
    },
    errorClass() {
      return "badge rounded-pill text-danger bg-light-danger p-1 text-uppercase px-3 w-100";
    },

    printReport() {
      $(".report-table").removeAttr("style");
      this.print("printArea", "attendance");
    },
  },
  created() {
    this.setBreadcrumbs(this.model, "view", "Attendance", addOrBack);
    this.get_data(`${this.model}/${this.$route.params.id}`);
  },
};
</script>