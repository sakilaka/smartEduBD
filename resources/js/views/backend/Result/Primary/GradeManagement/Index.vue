<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
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
const model = "primaryGradeManagement";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Grade Management",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "from_mark", title: "From Mark", align: "center" },
  { field: "to_mark", title: "To Mark", align: "center" },
  { field: "grade", title: "Letter Grade", align: "center" },
  { field: "gpa", title: "GPA", align: "center" },
  { field: "from_gpa", title: "From GPA", align: "center" },
  { field: "to_gpa", title: "To GPA", align: "center" },
  { field: "created_at", title: "Created at", date: true },
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
      fields_name: { 0: "Select One", from_mark: "From Mark" },
      search_data: {
        pagination: 10,
        field_name: 0,
        value: "",
      },
      table: {
        columns: tableColumns,
        routes: {},
        datas: [],
        meta: [],
        links: [],
      },
    };
  },
  methods: {
    destroy(id) {
      this.destroy_data(this.model, id, this.search_data);
    },
    search() {
      this.$route.query.page = "";
      this.get_paginate_data(this.model, this.search_data);
    },
  },
  async created() {
    this.getRouteName(this.model);
    this.setBreadcrumbs(
      this.model,
      "index",
      "Primary Grade Management",
      addOrBack
    );
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>