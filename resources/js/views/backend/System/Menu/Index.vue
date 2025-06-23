<template>
  <div class="card">
    <div class="card-body min-height">
      <!-- Search Form -->
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!--Search-->
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
const model = "menu";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: model,
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "parent.menu_name", title: "Parent Menu" },
  { field: "menu_name", title: "Menu Name" },
  { field: "route_name", title: "Route Name", align: "center" },
  { field: "params", title: "Params", align: "center" },
  { field: "looks_type", title: "Looks Type", align: "center" },
  {
    field: "sorting",
    title: "Sorting",
    sorting: true,
    namespace: "System-Menu",
    auto: "",
    align: "center",
  },
];
//json fields for export excel
const json_fields = {
  "Menu Name": "name",
  "Parent Name": "parent.name",
  Sorting: "sorting",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: { 0: "Select One", menu_name: "Menu Name" },
      search_data: {
        pagination: 10,
        field_name: "menu_name",
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
  created() {
    this.getRouteName(this.model);
    this.setBreadcrumbs(this.model, "index", "Menu", addOrBack);
    this.get_paginate_data(this.model, this.search_data);
  },
};
</script>