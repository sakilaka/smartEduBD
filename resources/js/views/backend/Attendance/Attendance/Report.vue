<template>
  <div class="col-xl-12">
    <div class="card border">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">Search Attendance</h6>
      </div>
      <div class="card-body p-3">
        <form v-on:submit.prevent="search" class="col-12 row">
          <!------------ Single Input ------------>
          <SelectSearch
            v-if="!$root.institution_id"
            title="All Institution"
            field="institution_id"
            :req="true"
            :datas="$root.global.institutions"
            val="id"
            val_title="name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Campus"
            field="campus_id"
            :req="true"
            :datas="campuses_filter(search_data.institution_id)"
            val="id"
            val_title="name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Shift"
            field="shift_id"
            :req="true"
            :datas="shift_filter(search_data.institution_id, 'search_data')"
            val="shift_id"
            val_title="shift_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Medium"
            field="medium_id"
            :req="true"
            :datas="medium_filter(search_data.institution_id, 'search_data')"
            val="medium_id"
            val_title="medium_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Class"
            field="academic_class_id"
            :req="true"
            :datas="class_filter(search_data.institution_id, 'search_data')"
            val="academic_class_id"
            val_title="class_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="Group"
            field="group_id"
            :req="true"
            :datas="group_filter(search_data.institution_id, 'search_data')"
            val="group_id"
            val_title="group_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="Section"
            field="section_id"
            :req="true"
            :datas="section_filter(search_data.institution_id, 'search_data')"
            val="section_id"
            val_title="section_name"
            col="2"
          />

          <div class="w-100 my-1"></div>

          <div class="col-6 col-xl-2 pe-0">
            <date-picker
              v-model="search_data.from_date"
              valueType="format"
              format="YYYY-MMM-DD"
              :formatter="momentFormat"
              placeholder="Select Date"
            ></date-picker>
          </div>
          <div class="col-6 col-xl-2 pe-0">
            <date-picker
              v-model="search_data.to_date"
              valueType="format"
              format="YYYY-MMM-DD"
              :formatter="momentFormat"
              placeholder="Select Date"
            ></date-picker>
          </div>
          <Search :fields_name="fields_name" :print_excel="false" />
        </form>
      </div>
    </div>

    <!-- Student List -->
    <!-- Student List -->
    <div class="card border" v-if="!$root.spinner">
      <div class="card-header">
        <div class="row">
          <div class="col-5">
            <h6 class="mb-0 text-uppercase">Students Attendance Summary</h6>
          </div>
          <div class="col-1">Total Class :</div>
          <div class="col-2">
            <input
              v-model="total_class"
              type="number"
              class="form-control form-control-sm text-center"
              placeholder="Total Class"
              style="width: 100px"
            />
          </div>
          <div class="col-4">
            <a
              v-if="students && Object.keys(students).length > 0"
              class="btn btn-xs btn-success float-end"
              @click="printReport()"
              href="javascript:;"
            >
              <i class="bx bx-printer"></i> PRINT
            </a>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div id="printArea" class="row g-3 report-table table-fixed printArea">
          <table
            v-if="students && Object.keys(students).length > 0"
            class="table table-bordered table-striped table-hover"
          >
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th>Software ID</th>
                <!-- <th>Roll Number</th> -->
                <th>Student Name</th>
                <th>Guardian Name</th>
                <th>Guardian Mobile</th>
                <th class="text-center">Total Class</th>
                <th class="text-center">Total Present</th>
                <th class="text-center">Total Absent</th>
                <th class="text-center">Present Percentage (%)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, key) in students" :key="key">
                <td class="text-center">{{ key + 1 }}</td>
                <td>{{ item.software_id }}</td>
                <!-- <td>{{ item.roll_number }}</td> -->
                <td>{{ item.name_en }}</td>
                <td>{{ item.guardian_name_en }}</td>
                <td>{{ item.guardian_mobile }}</td>
                <td class="text-center">
                  <b>{{ total_class }} </b>
                </td>
                <td class="text-center text-success">
                  <b>{{ item.total_present }}</b>
                </td>
                <td class="text-danger text-center">
                  <b v-if="total_class > 0">
                    {{ parseInt(total_class) - parseInt(item.total_present) }}
                  </b>
                  <b v-else>{{ 0 }}</b>
                </td>
                <td class="text-warning text-center">
                  <b v-if="total_class > 0">
                    {{
                      (
                        (parseInt(item.total_present) * 100) /
                        parseInt(total_class)
                      ).toFixed(2)
                    }}
                    %
                  </b>
                  <b v-else>{{ 0 }}%</b>
                </td>
              </tr>
            </tbody>
          </table>
          <span class="text-center my-5" v-else> No Students Found </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import moment from "moment";

// define model name
const model = "attendance";

// Add Or Back
const addOrBack = {
  route: "attendance.index",
  title: "Attendance Report",
  icon: "left-arrow-alt",
};

const startOfMonth = moment().startOf("month").format("YYYY-MM-DD");
const endOfMonth = moment().endOf("month").format("YYYY-MM-DD");

export default {
  data() {
    return {
      model: model,
      total_class: 0,
      fields_name: {
        0: "Select One",
        student_id: "Software ID",
        name: "Name",
        mobile: "Mobile",
        reg_no: "Reg No.",
        college_roll: "College Roll",
      },
      search_data: {
        allData: true,
        field_name: "mobile",
        value: "",
        pagination: 5000,
        from_date: startOfMonth,
        to_date: endOfMonth,
        institution_id: "",
        campus_id: "",
        medium_id: "",
        group_id: "",
        section_id: "",
        shift_id: "",
        academic_class_id: "",
      },
      students_list: [],
    };
  },

  computed: {
    students() {
      if (Object.keys(this.students_list).length > 0) {
        return this.students_list;
      }
      return [];
    },
  },

  methods: {
    search() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) return false;
        this.asyncData();
      });
    },
    asyncData() {
      this.get_data("attendance-report", "students_list", this.search_data);
    },
    printReport() {
      $(".report-table").removeAttr("style");
      this.print("printArea", "attendance");
    },
  },

  created() {
    this.setBreadcrumbs(this.model, "view", "Attendance Report", addOrBack);
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
  },

  validators: {
    "search_data.from_date": function (value = null) {
      return Validator.value(value).required("From date is required");
    },
    "search_data.to_date": function (value = null) {
      return Validator.value(value).required("To date is required");
    },
    "search_data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "search_data.campus_id": function (value = null) {
      return Validator.value(value).required("Campus is required");
    },
    "search_data.shift_id": function (value = null) {
      return Validator.value(value).required("Shift is required");
    },
    "search_data.medium_id": function (value = null) {
      return Validator.value(value).required("Medium is required");
    },
    "search_data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "search_data.group_id": function (value = null) {
      return Validator.value(value).required("Group is required");
    },
    "search_data.section_id": function (value = null) {
      return Validator.value(value).required("Section is required");
    },
  },
};
</script>
