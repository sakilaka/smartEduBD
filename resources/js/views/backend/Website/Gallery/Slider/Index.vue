<template>
  <div class="card">
    <div class="card-body min-height">
      <!-- Search Form -->
      <form v-on:submit.prevent="search" class="col-12 row mb-2">
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
const model = "slider";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: model,
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "title", title: "Title" },
  {
    field: "slider",
    title: "Slider",
    image: true,
    imgWidth: "200px",
    height: "60px",
    align: "center",
  },
  {
    field: "sorting",
    title: "Sorting",
    sorting: true,
    namespace: "Website-Gallery-Slider",
    auto: true,
    align: "center",
  },
];
//json fields for export excel
const json_fields = {
  Title: "title",
  Sorting: "sorting",
  Slider: "slider",
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
        field_name: "title",
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
    this.setBreadcrumbs(this.model, "index", null, addOrBack);
    this.get_paginate_data(this.model, this.search_data);
  },
};
</script>