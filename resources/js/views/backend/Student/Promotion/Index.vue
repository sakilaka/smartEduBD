<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Session"
          field="academic_session_id"
          :datas="$root.global.sessions"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Academic Level"
          field="academic_qualification_id"
          :datas="$root.global.qualifications"
          loop_type="pluck"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Department"
          field="department_id"
          :datas="departments_filter(search_data.academic_qualification_id)"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Academic Class"
          field="academic_class_id"
          :datas="class_filter(search_data.academic_qualification_id)"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Student Type"
          field="student_type"
          :datas="$root.global.student_types"
          loop_type="pluck"
        />
        <div class="w-100 mt-2"></div>
        <Search :fields_name="fields_name" />
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
      ></base-table>

      <!-- Pagination -->
      <div class="box-footer clearfix">
        <Pagination
          :url="model"
          :search_data="search_data"
          v-if="!$root.tableSpinner"
        />
      </div>
      <!-- Pagination -->
    </div>
  </div>
</template>

<script>
// define model name
const model = "studentPromotion";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Student Promotion",
  icon: "plus-circle",
};

// For Call back funciton use in data
const routes = {
  view: model + ".show",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "student_id", title: "Software ID" },
  { field: "name", title: "Name" },
  { field: "mobile", title: "Mobile" },
  { field: "admission_id", title: "Admission ID" },
  { field: "reg_no", title: "Reg No." },
  { field: "academic_session", title: "Session", subfield: "name" },
  { field: "department", title: "Department", subfield: "name" },
  { field: "qualification", title: "Qualification", subfield: "name" },
  { field: "academic_class", title: "Class", subfield: "name" },
  { field: "student_type", title: "Std. Type" },
];
//json fields for export excel
const json_fields = {
  Title: "title",
  "Created at": "created_at",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: {
        0: "Select One",
        name: "Name",
        mobile: "Mobile",
        reg_no: "Reg No.",
        admission_id: "Admission ID",
      },
      search_data: {
        pagination: 10,
        field_name: 0,
        value: "",
        academic_class_id: "",
        academic_session_id: "",
        department_id: "",
        academic_qualification_id: "",
        student_type: "",
      },
      table: {
        columns: tableColumns,
        routes: routes,
        datas: [],
        meta: [],
        links: [],
      },
    };
  },
  computed: {},
  methods: {
    destroy(id) {
      this.destroy_data(this.model, id, this.search_data);
    },
    search() {
      this.$route.query.page = "";
      this.get_paginate_data("student", this.search_data);
    },
  },
  async created() {
    this.setBreadcrumbs(this.model, "index", "Student Promotion", addOrBack);
    await this.get_paginate_data("student", this.search_data);
  },
};
</script>