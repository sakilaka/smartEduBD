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
const model = "smsTemplate";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "SMS Template",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "name", title: "Template Name" },
  { field: "sms_type", title: "SMS Type" },
  {
    field: "common_message",
    title: "Common Message",
    array: true,
    array_value: [{ 0: "NO", 1: "YES" }],
    align: "center",
  },
  {
    field: "sending_status",
    title: "Sending Status",
    array: true,
    array_value: [{ 0: "OFFLINE", 1: "ONLINE" }],
    align: "center",
  },
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
      fields_name: { 0: "Select One", name: "Name", sms_type: "SMS Type" },
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
    this.setBreadcrumbs(this.model, "index", "SMS Template", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>