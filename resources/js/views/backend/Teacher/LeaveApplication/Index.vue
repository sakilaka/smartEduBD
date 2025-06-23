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
const model = "leaveApplication";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Leave Application",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "teacher", title: "Teacher Name", subfield: "name" },
  { field: "leave_type", title: "Leave Type", subfield: "name" },
  { field: "from_date", title: "From Date", date: true },
  { field: "to_date", title: "To Date", date: true },
  { field: "to_date", title: "Total Days", align: "center" },
  { field: "to_date", title: "Approved From Date", date: true },
  { field: "to_date", title: "Approved To Date", date: true },
  { field: "to_date", title: "Approved Total Days", align: "center" },
  { field: "status", title: "Status", align: "center" },
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
      fields_name: { 0: "Select One", title: "Title" },
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
    this.setBreadcrumbs(this.model, "index", "Leave Application", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>