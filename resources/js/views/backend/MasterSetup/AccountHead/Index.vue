<template>
  <div class="card">
    <div class="card-body min-height">
      <!-- Search Form -->
      <form v-on:submit.prevent="search" class="col-12 row mb-2">
        <div class="col-12 col-xl-1 pe-0">
          <select
            @change="search()"
            v-model="search_data.status"
            class="form-select form-select-sm"
          >
            <option value="">All</option>
            <option
              v-for="(value, name, index) in $root.global.status"
              :key="index"
              :value="name"
            >
              {{ value }}
            </option>
          </select>
        </div>
        <div class="col-12 col-xl-2 pe-0">
          <select
            @change="search()"
            v-model="search_data.type"
            class="form-select form-select-sm"
          >
            <option value="">Head Type</option>
            <option value="admission">Admission</option>
            <option value="college">College</option>
            <option value="hostel">Hostel</option>
            <option value="certificate">Certificate</option>
          </select>
        </div>
        <!--Search -->
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
const model = "accountHead";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Account Head",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "name_en", title: "Name (en)" },
  { field: "name_bn", title: "Name (bn)" },
  { field: "type", title: "Type", align: "center" },
  { field: "status", title: "Status", align: "center", width: "8%" },
  { field: "created_at", title: "Created at", date: true },
];
//json fields for export excel
const json_fields = {
  Name: "name",
  Status: "status",
  "Created at": "created_at",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: { 0: "Select One", name: "Name" },
      search_data: {
        pagination: 10,
        field_name: 0,
        value: "",
        type: "",
        status: "active",
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
    await this.get_paginate_data(this.model, this.search_data);
    this.getRouteName(this.model);
    this.setBreadcrumbs(this.model, "index", "Account Head", addOrBack);
  },
};
</script>