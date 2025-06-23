<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!-- <div class="col-6 col-xl-1 pe-0">
          <select v-model="search_data.type" class="form-select form-select-sm">
            <option value="">All</option>
            <option value="Teacher">Teacher</option>
            <option value="Staff">Staff</option>
          </select>
        </div> -->
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
const model = "teacher";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: model,
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "name", title: "Name" },
  { field: "email", title: "Email" },
  { field: "type", title: "Type" },
  { field: "department", title: "Department", subfield: "name" },
  { field: "mobile", title: "Mobile" },
  { field: "status", title: "Status", align: "center", width: "8%" },
];

//json fields for export excel
const json_fields = {
  Name: "name",
  Email: "email",
  Mobile: "mobile",
  Address: "address",
  "Role Name": "rolename.name",
  "Created at": "created_at",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: { 0: "Select One", name: "Name", mobile: "Mobile" },
      search_data: {
        pagination: 10,
        field_name: "name",
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
    this.setBreadcrumbs(this.model, "index", null, addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>