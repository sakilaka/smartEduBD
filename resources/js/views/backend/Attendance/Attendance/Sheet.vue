<template>
  <div class="col-xl-12">
    <div class="card border">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">Search Attendance Sheet</h6>
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
          <Search :search_field="false" :print_excel="false" />
        </form>
      </div>
    </div>

    <!-- Student List -->
    <!-- Student List -->
    <div class="card border" v-if="!$root.spinner">
      <div class="card-header">
        <div class="row">
          <div class="col-8">
            <h6 class="mb-0 text-uppercase">Students Attendance Sheet</h6>
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
        <div class="row g-3 report-table">
          <div class="table-responsive table-fixed" id="printArea">
            <table
              v-if="students && Object.keys(students).length > 0"
              class="table table-bordered table-striped table-hover"
            >
              <thead>
                <tr>
                  <th>Software ID</th>
                  <template v-if="Object.keys(dates).length > 0">
                    <th
                      class="text-center"
                      style="font-size: 11px"
                      v-for="(status, date) in dates"
                      :key="date"
                    >
                      {{ date | formatDate("DD") }}
                    </th>
                  </template>
                </tr>
              </thead>
              <tbody v-if="students && Object.keys(students).length > 0">
                <tr v-for="(student, keyStd) in students" :key="keyStd">
                  <td>{{ student.software_id }}</td>
                  <template v-if="Object.keys(dates).length > 0">
                    <th
                      class="text-center"
                      style="font-size: 11px"
                      v-for="(status, date) in dates"
                      :key="date"
                    >
                      <span
                        class="text-success"
                        v-if="student.attendance_data.includes(date)"
                        >P</span
                      >
                      <span class="text-danger" v-else>A</span>
                    </th>
                  </template>
                </tr>
              </tbody>
              <span class="text-center my-5" v-else> No Students Found </span>
            </table>
          </div>
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
  title: "Attendance Sheet",
  icon: "left-arrow-alt",
};

const startOfMonth = moment().startOf("month").format("YYYY-MM-DD");
const endOfMonth = moment().endOf("month").format("YYYY-MM-DD");

export default {
  data() {
    return {
      model: model,
      total_class: 0,
      search_data: {
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
      attendance: { dates: [], students: [] },
    };
  },

  computed: {
    students() {
      if (Object.keys(this.attendance.students).length > 0) {
        return this.attendance.students;
      }
      return [];
    },
    dates() {
      if (Object.keys(this.attendance.dates).length > 0) {
        return this.attendance.dates;
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
      this.get_data("attendance-sheet", "attendance", this.search_data);
    },
    printReport() {
      $(".report-table").removeAttr("style");
      this.print("printArea", "attendance");
    },
  },

  created() {
    this.setBreadcrumbs(this.model, "view", "Attendance Sheet", addOrBack);
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
