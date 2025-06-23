<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <div class="col-6 col-xl-1 pe-0">
          <select
            v-model="search_data.status"
            class="form-select form-select-sm"
          >
            <option value="ur">Unread</option>
            <option value="r">Read</option>
          </select>
        </div>
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
const model = "notification";

// Add Or Back
const addOrBack = {};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "title", title: "Title" },
  { field: "description", title: "Description", description: true },
  { field: "status", title: "Status", width: "10%" },
  { field: "created_at", title: "Created at", date: true, width: "10%" },
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
        status: "ur",
      },
      table: {
        columns: tableColumns,
        routes: { view: "notification.show", destroy: "notification.destroy" },
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
    this.get_paginate_data(this.model, this.search_data);
    this.setBreadcrumbs(this.model, "index", null, addOrBack);
  },
};
</script>