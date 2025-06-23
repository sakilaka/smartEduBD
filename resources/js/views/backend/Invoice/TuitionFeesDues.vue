<template>
  <div v-if="!$root.spinner" class="col-12">
    <div class="card border">
      <div class="card-header">
        <h6
          @click="search_filter = !search_filter"
          class="mb-0 text-uppercase cursor-pointer"
        >
          Search Dues Report
          <i class="bx bx-filter float-end" title="Filter">
            <small>FILTER</small>
          </i>
        </h6>
      </div>

      <div v-show="search_filter" class="card-body">
        <div class="row g-2">
          <form v-on:submit.prevent="asyncData" class="col-12 row my-2">
            <!------------ Single Input ------------>
            <SelectSearch
              title="Month"
              field="dues_month"
              :datas="$root.global.months"
              val="key"
              val_title="name"
              :col="1"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              v-if="!$root.institution_id"
              title="All Institution"
              field="institution_id"
              :datas="$root.global.institutions"
              val="id"
              val_title="name"
              :req="true"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="All Campus"
              field="campus_id"
              :datas="campuses_filter(search_data.institution_id)"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="All Shift"
              field="shift_id"
              :datas="shift_filter(search_data.institution_id, 'search_data')"
              val="shift_id"
              val_title="shift_name"
              col="1"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="All Medium"
              field="medium_id"
              :datas="medium_filter(search_data.institution_id, 'search_data')"
              val="medium_id"
              val_title="medium_name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="All Class"
              field="academic_class_id"
              :datas="class_filter(search_data.institution_id, 'search_data')"
              val="academic_class_id"
              val_title="class_name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Group"
              field="group_id"
              :datas="group_filter(search_data.institution_id, 'search_data')"
              val="group_id"
              val_title="group_name"
              col="1"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Section"
              field="section_id"
              :datas="section_filter(search_data.institution_id, 'search_data')"
              val="section_id"
              val_title="section_name"
              col="1"
            />

            <div class="w-100 mt-2"></div>

            <!-- search -->
            <Search :fields_name="fields_name" />
          </form>
        </div>
      </div>
    </div>

    <!-- Student List -->
    <div class="card border">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">Students</h6>
      </div>
      <div class="card-body">
        <base-table :data="table.datas" :columns="table.columns" :routes="{}">
          <template v-slot:[`student`]="row">
            <td>
              <a href="javascript:void(0)" @click="viewDuesModal(row.item)">
                {{ row.item.name_en }} <br />
                {{ row.item.profile.roll_number }}
              </a>
            </td>
          </template>
          <template v-slot:[`campus_shift`]="row">
            <td>
              {{ row.item.campus.name }} ({{ row.item.shift.name }})
              <br />
              {{ row.item.medium.name }}
            </td>
          </template>
          <template v-slot:[`class_section`]="row">
            <td>
              {{ row.item.academic_class.name }}
              <br />
              {{ row.item.group.name }} ({{ row.item.section.name }})
            </td>
          </template>
        </base-table>
      </div>
    </div>

    <!-- View Dues Modal -->
    <b-modal id="view-dues-student" title="Student Dues List">
      <div class="col-12">
        <div class="row">
          <div class="col-12">
            Name : <b>{{ student.name_en }}</b> <br />
            Software ID : <b>{{ student.software_id }}</b>

            <table
              class="table table-sm table-bordered table-striped mb-0 mt-3"
            >
              <thead>
                <tr>
                  <th class="text-center">SL</th>
                  <th>Month</th>
                  <th>Purpose</th>
                  <th class="text-center">Amount</th>
                </tr>
              </thead>
              <tbody
                v-if="
                  student.dues_fees && Object.keys(student.dues_fees).length > 0
                "
              >
                <tr v-for="(fees, key) in student.dues_fees" :key="key">
                  <th class="text-center">{{ key + 1 }}</th>
                  <td>{{ fees.date | formatDate("MMMM") }}</td>
                  <td>Tuition Fees</td>
                  <td class="text-center">{{ fees.amount }}</td>
                </tr>
              </tbody>
              <tbody v-else>
                <tr>
                  <td colspan="5">No hostel fees have been generated yet</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
// define model name
const model = "invoice";

// set breadcrumb
const breadcrumb = [
  { route: "invoice.tuitionFeesDues", title: "Tuition Fees Dues Report" },
];

// Add Or Back
const addOrBack = {};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "student", title: "Student Info" },
  { field: "software_id", title: "Software ID" },
  { field: "campus_shift", title: "Campus/Shift Info" },
  { field: "class_section", title: "Class/Section Info" },
  { field: "total_dues", title: "Dues Amount", align: "center", amount: true },
];

//json fields for export excel
const json_fields = {
  Institution: "institution.name",
  Campus: "campus.name",
  Shift: "shift.name",
  "Medium/Version": "medium.name",
  "Academic Class": "academic_class.name",
  Group: "group.name",
  Section: "section.name",

  "Software ID": "software_id",
  "Roll Number": "profile.roll_number",
  Mobile: "mobile",
  Email: "email",
  "Total Dues": "total_dues",
};

export default {
  data() {
    return {
      model: model,
      search_filter: true,
      json_fields: json_fields,
      customBreadcrumb: { breadcrumb: breadcrumb, addOrBack: addOrBack },
      fields_name: {
        software_id: "Software ID",
        name_en: "Name",
        roll_number: "Roll Number",
      },
      search_data: {
        field_name: "software_id",
        value: "",
        institution_id: "",
        campus_id: "",
        medium_id: "",
        group_id: "",
        section_id: "",
        shift_id: "",
        academic_class_id: "",
        dues_month: "",
      },
      table: {
        columns: tableColumns,
        routes: {},
        datas: [],
        meta: [],
        links: [],
      },
      student: {},
    };
  },

  methods: {
    search() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        if (error > 0) return false;
      });
    },

    viewDuesModal(item) {
      this.student = item;
      this.$bvModal.show("view-dues-student");
    },

    asyncData() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        if (error > 0) return false;
        this.get_paginate_data(`tuition-fees-dues`, this.search_data);
      });
    },
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
    breadcrumbs.dispatch("setBreadcrumbs", this.customBreadcrumb);
  },

  // ================== validation rule for form ==================
  validators: {
    "search_data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
  },
};
</script>