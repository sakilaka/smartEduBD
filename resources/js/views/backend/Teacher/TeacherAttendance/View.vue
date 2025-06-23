<template>
  <div v-if="!$root.spinner">
    <div class="row mb-2">
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
          <div class="row">
            <div class="col-4">
              <h6 class="mb-0 text-uppercase">TEACHERS ATTENDANCE</h6>
            </div>
            <div class="col-8">
              <b>Date :</b> {{ data.date | formatDate }}
              <span class="float-end">
                Total:
                <span class="text-primary fw-bold">
                  {{ data.total_teacher }}</span
                >
                &nbsp; || &nbsp; Present:
                <span class="text-success fw-bold">
                  {{ data.total_present }}
                </span>
                &nbsp; || &nbsp; Absent:
                <span class="text-danger fw-bold">
                  {{
                    parseInt(data.total_teacher) - parseInt(data.total_present)
                  }}
                </span>
              </span>
            </div>
          </div>
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
                  <th>Teacher Name</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Department</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, key) in data.details" :key="key">
                  <td class="text-center">{{ key + 1 }}</td>
                  <td>
                    {{ row.teacher ? row.teacher.name : "" }}
                  </td>
                  <td>
                    {{ row.teacher ? row.teacher.mobile : "" }}
                  </td>
                  <td>
                    {{ row.teacher ? row.teacher.email : "" }}
                  </td>
                  <td>
                    <span v-if="row.teacher && row.teacher.department">
                      {{ row.teacher.department.name }}
                    </span>
                  </td>
                  <td class="text-center text-success">
                    <span :class="activeClass()" v-if="row.status == 'P'">
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
const model = "teacherAttendance";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Teacher Attendance",
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
  async created() {
    await this.get_data(this.model, this.$route.params.id, "data"); // get data
    this.setBreadcrumbs(this.model, "view", "Teacher Attendance", addOrBack); // Set Breadcrumbs
  },
};
</script>