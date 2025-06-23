<template>
  <div class="col-xl-12">
    <form v-on:submit.prevent="submit" id="form" class="row">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Search Students For Attendance</h6>
        </div>
        <div class="card-body pt-2 pb-3">
          <div class="row g-3">
            <!-- Single Input -->
            <div class="col-2">
              <Label
                title="Date"
                :req="true"
                :condition="validation.hasError('data.date')"
                :msg="validation.firstError('data.date')"
              />
              <date-picker
                v-model="data.date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <SelectCustom
              v-show="!$root.institution_id"
              title="Institution"
              field="institution_id"
              :req="true"
              :datas="$root.global.institutions"
              col="2"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Campus"
              field="campus_id"
              :req="true"
              :datas="campuses_filter(data.institution_id)"
              col="2"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Shift"
              field="shift_id"
              :req="true"
              :datas="shift_filter(data.institution_id)"
              col="2"
              val="shift_id"
              val_title="shift_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Medium"
              field="medium_id"
              :req="true"
              :datas="medium_filter(data.institution_id)"
              col="2"
              val="medium_id"
              val_title="medium_name"
            /><!------------ Single Input ------------>
            <SelectCustom
              title="Class"
              field="academic_class_id"
              :req="true"
              :datas="class_filter(data.institution_id)"
              col="1"
              val="academic_class_id"
              val_title="class_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Group"
              field="group_id"
              :req="true"
              :datas="group_filter(data.institution_id)"
              col="1"
              val="group_id"
              val_title="group_name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Section"
              field="section_id"
              :req="true"
              :datas="section_filter(data.institution_id)"
              col="1"
              val="section_id"
              val_title="section_name"
            />

            <!------------ Button ------------>
            <div class="col-1 pt-3">
              <button
                type="button"
                :disabled="$root.spinner ? true : false"
                @click="search()"
                class="btn btn-sm btn-success"
              >
                <span v-if="$root.spinner">
                  <span class="spinner-border spinner-border-sm"></span>
                </span>
                <span v-else> <i class="bx bx-search mx-0 fw-bold"></i></span>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Student List -->
      <!-- Student List -->
      <div class="card border" v-if="!$root.spinner">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">Students</h6>
            </div>

            <div class="col-6 text-end">
              <span>
                <b>Total Student : {{ totalStatics.total }}</b>
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
          <div class="row g-3 table-fixed">
            <table
              v-if="students && Object.keys(students).length > 0"
              class="table table-bordered table-hover table-striped mb-0"
            >
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Profile</th>
                  <th>Software ID</th>
                  <th>Roll Number</th>
                  <th>Student Name</th>
                  <th>Guardian Name</th>
                  <th>Guardian Mobile</th>
                  <th class="text-center">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, key) in students" :key="key">
                  <td class="text-center">{{ key + 1 }}</td>
                  <td class="text-center p-0">
                    <img
                      v-if="item.student && item.student.profile"
                      :src="item.student.profile.profile"
                      height="37px"
                    />
                    <img
                      v-else-if="item.profile.profile"
                      :src="item.profile.profile"
                      height="37px"
                    />
                  </td>
                  <td>{{ item.software_id }}</td>
                  <td>
                    {{
                      item.student
                        ? item.student.profile.roll_number
                        : item.profile.roll_number
                    }}
                  </td>
                  <td>
                    {{ item.student ? item.student.name_en : item.name_en }}
                  </td>
                  <td>
                    <span v-if="item.student && item.student.guardian">
                      {{ item.student.guardian.name_en }}
                    </span>
                    <span v-else-if="item.guardian">
                      {{ item.guardian.name_en }}
                    </span>
                  </td>
                  <td>
                    <span v-if="item.student && item.student.guardian">
                      {{ item.student.guardian.mobile }}
                    </span>
                    <span v-else-if="item.guardian">
                      {{ item.guardian.mobile }}
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
                          :id="`status${key}`"
                          :name="`status${key}`"
                          value="A"
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
                          :id="`status${key}`"
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
            </table>

            <span class="text-center my-5" v-else> No Students Found </span>
          </div>
        </div>
      </div>

      <!-- SUBMIT BUTTON-->
      <div
        v-if="students && Object.keys(students).length > 0"
        class="col-12 d-flex justify-content-center"
      >
        <div v-if="!$root.spinner" class="col-xl-3 text-center my-3 mb-5">
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
// define model name
const model = "attendance";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Attendance",
  icon: "left-arrow-alt",
};

let date = new Date().toISOString().slice(0, 10);

export default {
  data() {
    return {
      model: model,
      data: {
        date: date,
        institution_id: null,
        campus_id: null,
        medium_id: null,
        group_id: null,
        section_id: null,
        shift_id: null,
        academic_class_id: null,
        details: [],
      },
      students_list: [],
    };
  },

  computed: {
    students() {
      if (Object.keys(this.students_list).length > 0) {
        return this.students_list.map((e) => {
          let obj = e;
          obj.status = "P";
          return obj;
        });
      }
      return this.data.details;
    },

    attendance_data() {
      return this.students.map((e) => {
        return {
          id: e.id ? e.id : null,
          software_id: e.software_id,
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
          this.data.details = this.attendance_data;
          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data, this.submitType);
          }
        }
      });
    },

    // Search Students
    search() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        if (error > 0) return false;
        this.asyncData();
      });
    },

    asyncData() {
      let params = {
        allData: true,
        institution_id: this.data.institution_id,
        campus_id: this.data.campus_id,
        medium_id: this.data.medium_id,
        group_id: this.data.group_id,
        section_id: this.data.section_id,
        shift_id: this.data.shift_id,
        academic_class_id: this.data.academic_class_id,
      };
      this.get_data("student", "students_list", params);
    },
  },
  created() {
    let page = "create";
    if (this.$route.params.id) {
      page = "edit";
      this.get_data(`${this.model}/${this.$route.params.id}`);
    }
    this.setBreadcrumbs(this.model, page, null, addOrBack);
  },

  mounted() {
    if (this.$root.institution_id) {
      this.data.institution_id = this.$root.institution_id;
    }
  },

  // ================== validation rule for form ==================
  validators: {
    "data.date": function (value = null) {
      return Validator.value(value).required("Date is required");
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