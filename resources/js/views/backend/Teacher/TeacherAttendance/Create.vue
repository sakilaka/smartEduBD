<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <form v-on:submit.prevent="submit" id="form" class="row">
      <div class="card border">
        <div class="card-header">
          <div class="row">
            <div class="col-3">
              <h6 class="mb-0 text-uppercase">Teacher Attendance</h6>
            </div>

            <div class="col-3">
              <b-form-datepicker
                v-model="data.date"
                id="datepicker1"
                size="sm"
                :date-format-options="{
                  year: 'numeric',
                  month: 'short',
                  day: '2-digit',
                  weekday: 'short',
                }"
                placeholder="Select Date"
              ></b-form-datepicker>
            </div>

            <div class="col-6 text-end">
              <span>
                <b>Total Teacher : {{ totalStatics.total }}</b>
              </span>
              &nbsp; | &nbsp;
              <span class="text-success">
                <b>Present : {{ totalStatics.present }}</b>
              </span>
              &nbsp; | &nbsp;
              <span class="text-danger">
                <b>Absent : {{ totalStatics.absent }}</b>
              </span>
            </div>
          </div>
        </div>
        <div class="card-body p-3">
          <div class="row table-fixed">
            <table
              v-if="!$root.tableSpinner"
              class="table table-sm table-bordered table-striped mb-0"
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
              <tbody v-if="teachers && Object.keys(teachers).length > 0">
                <tr v-for="(item, key) in teachers" :key="key">
                  <td class="text-center">{{ key + 1 }}</td>
                  <td>
                    {{ item.teacher ? item.teacher.name : item.name }}
                  </td>
                  <td>
                    {{ item.teacher ? item.teacher.mobile : item.mobile }}
                  </td>
                  <td>
                    {{ item.teacher ? item.teacher.email : item.email }}
                  </td>
                  <td>
                    <span v-if="item.teacher && item.teacher.department">
                      {{ item.teacher.department.name }}
                    </span>
                    <span v-else-if="item.department">
                      {{ item.department.name }}
                    </span>
                  </td>
                  <td class="p-0">
                    <div
                      class="d-flex justify-content-center align-items-center"
                      style="height: 37px"
                    >
                      <button
                        type="button"
                        class="btn_choose_sent bg_btn_chose_1"
                      >
                        <input
                          type="radio"
                          :name="`status${key}`"
                          value="A"
                          checked
                          v-model="item.status"
                          :class="item.status == 'A' ? 'absent-radio' : ''"
                        />A
                      </button>
                      <button
                        type="button"
                        class="btn_choose_sent bg_btn_chose_2"
                      >
                        <input
                          type="radio"
                          :name="`status${key}`"
                          value="P"
                          v-model="item.status"
                          :class="item.status == 'P' ? 'present-radio' : ''"
                        />P
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>

              <tbody v-else>
                <tr>
                  <td colspan="5" class="text-center my-5">No data found.</td>
                </tr>
              </tbody>
            </table>

            <!-- Table Spinner -->
            <TableSpinner />
          </div>
        </div>
      </div>

      <!-- SUBMIT BUTTON-->
      <div
        v-if="teachers && Object.keys(teachers).length > 0"
        class="col-12 d-flex justify-content-center"
      >
        <div class="col-xl-3 text-center my-3 mb-5">
          <button
            type="submit"
            :disabled="$root.submit ? true : false"
            class="btn btn-success btn-md px-3"
          >
            <span v-if="$root.submit">
              <span class="spinner-border spinner-border-sm"></span>
              <span>processing...</span>
            </span>
            <span v-else> <i class="bx bx-save"></i> Save</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import TableSpinner from "./../../../../components/backend/elements/Table/TableSpinner.vue";

// define model name
const model = "teacherAttendance";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Teacher Attendance",
  icon: "left-arrow-alt",
};

let date = new Date().toISOString().slice(0, 10);

export default {
  components: { TableSpinner },
  data() {
    return {
      model: model,
      data: {
        date: date,
        details: [],
      },
      extraData: { teachers_list: [] },
    };
  },

  computed: {
    teachers() {
      if (Object.keys(this.extraData.teachers_list).length > 0) {
        return this.extraData.teachers_list.map((e) => {
          let obj = e;
          obj.status = "P";
          return obj;
        });
      }
      return this.data.details;
    },

    attendance_data() {
      return this.teachers.map((e) => {
        return {
          admin_id: e.admin_id ? e.admin_id : e.id,
          in_time: null,
          out_time: null,
          status: e.status,
        };
      });
    },

    totalStatics() {
      let present = 0;
      let absent = 0;
      let total = this.attendance_data.length;
      if (this.attendance_data) {
        present = this.attendance_data.filter(function (el) {
          return el.status == "P";
        }).length;

        absent = this.attendance_data.length - present;
      }
      return {
        present: present,
        absent: absent,
        total: total,
      };
    },
  },
  methods: {
    submit: function () {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          this.data.total_teacher = this.totalStatics.total;
          this.data.total_present = this.totalStatics.present;
          this.data.details = this.attendance_data;

          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data, this.submitType);
          }
        }
      });
    },
  },
  created() {
    let page = "create";
    if (this.$route.params.id) {
      page = "edit";
      this.get_data(this.model, this.$route.params.id, "data");
    } else {
      this.get_paginate_data("teacher", { allData: true }, "teachers_list");
    }
    this.setBreadcrumbs(this.model, page, "Teacher Attendance", addOrBack);
  },

  // ================== validation rule for form ==================
  validators: {
    "data.date": function (value = null) {
      return Validator.value(value).required("Session is required");
    },
  },
};
</script>

<style scoped>
.btn_choose_sent input {
  -webkit-appearance: none;
  display: block;
  margin: 10px;
  width: 15px;
  height: 15px;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: hsla(0, 0%, 100%, 0.15) 0 1px 1px,
    inset hsla(0, 0%, 0%, 0.5) 0 0 0 1px;
  background-color: hsla(0, 0%, 0%, 0.1);
  background-repeat: no-repeat;
  -webkit-transition: background-position 0.15s cubic-bezier(0.8, 0, 1, 1),
    -webkit-transform 0.25s cubic-bezier(0.8, 0, 1, 1);
  outline: none;
}

.present-radio {
  background-image: -webkit-radial-gradient(
    #2eff01 0%,
    #2eff01 15%,
    #2eff01 28%,
    #2eff01 70%
  );
}
.absent-radio {
  background-image: -webkit-radial-gradient(
    #fa6868 0%,
    #fa6868 15%,
    #fa6868 28%,
    #fa6868 70%
  );
}
.btn_choose_sent input:checked {
  -webkit-transition: background-position 0.2s 0.15s cubic-bezier(0, 0, 0.2, 1),
    -webkit-transform 0.25s cubic-bezier(0, 0, 0.2, 1);
}
.btn_choose_sent input:active {
  -webkit-transform: scale(1.5);
  -webkit-transition: -webkit-transform 0.1s cubic-bezier(0, 0, 0.2, 1);
}

/* The up/down direction logic */

.btn_choose_sent input,
.btn_choose_sent input:active {
  background-position: 0 24px;
}
.btn_choose_sent input:checked {
  background-position: 0 0;
}
.btn_choose_sent input:checked ~ input,
.btn_choose_sent input:checked ~ input:active {
  background-position: 0 -24px;
}

.btn_choose_sent {
  background: #ef2d56;
  color: #f0f0f0;
  box-shadow: 0 10px 20px rgb(125 147 178 / 30%);
  border: none;
  border-radius: 3px;
  font-size: 13px;
  font-weight: bold;
  padding: 3px 11px 3px 30px;
  text-align: center;
  display: inline-block;
  text-decoration: none;
  margin-right: 15px;
  transition: all 0.3s;
  height: auto;
  cursor: pointer;
  position: relative;
  outline: none;
}

.btn_choose_sent input {
  position: absolute;
  left: 0;
  right: 0;
  top: -5px;
}

.btn_choose_sent input:after {
  position: absolute;
  content: "";
  width: 15rem;
  left: 0;
  right: 0;
}

.bg_btn_chose_1 {
  background-color: #a5b5be !important;
}

.bg_btn_chose_2 {
  background-color: #21a700 !important;
}
.btn-disable {
  background: #cacaca;
}
</style>