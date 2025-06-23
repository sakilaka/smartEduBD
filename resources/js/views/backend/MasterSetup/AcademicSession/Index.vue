<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <div class="col-6 col-xl-1 pe-0">
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
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Institution Category"
          field="institution_category_id"
          :datas="$root.global.institution_categories"
          val="id"
          val_title="name"
        />
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
const model = "academicSession";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Academic Session",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "institution_category.name", title: "Institution Category" },
  { field: "name", title: "Session Name" },
  {
    field: "current",
    title: "Current Financial Year",
    align: "center",
    width: "15%",
    array: true,
    array_value: [{ 1: "Active", 0: "- - - -" }],
  },
  {
    field: "online_admission",
    title: "Online Admission",
    array: true,
    array_value: [{ 1: "Yes", 0: "No" }],
    align: "center",
  },
  {
    field: "status",
    title: "Status",
    align: "center",
    width: "10%",
  },
];
//json fields for export excel
const json_fields = {
  Name: "name",
  Description: "description",
  Status: "status",
  "Created at": "created_at",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: { 0: "Select One", name: "Session Name" },
      search_data: {
        pagination: 10,
        field_name: "name",
        value: "",
        institution_category_id: "",
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
    this.getRouteName(this.model);
    this.setBreadcrumbs(this.model, "index", "Academic Session", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>