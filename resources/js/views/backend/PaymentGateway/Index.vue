<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <div class="col-6 col-xl-1 pe-0">
          <select
            v-model="search_data.status"
            @change="search()"
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
        <div class="col-6 col-xl-2 pe-0">
          <select
            v-model="search_data.provider"
            @change="search()"
            class="form-select form-select-sm"
          >
            <option value="">--All Provider--</option>
            <option
              v-for="(value, name, index) in $root.global.gateway"
              :key="index"
              :value="name"
            >
              {{ value }}
            </option>
          </select>
        </div>
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Institution"
          field="institution_id"
          :datas="$root.global.institutions"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Academic Class"
          field="academic_class_id"
          :datas="$root.global.classes"
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
const model = "paymentGateway";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Payment Gateway",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "institution.name", title: "Institution" },
  { field: "academic_class.name", title: "Academic Class" },
  { field: "title", title: "Title" },
  { field: "provider", title: "Provider", align: "center" },
];
//json fields for export excel
const json_fields = {
  Institution: "institution.name",
  "Academic Class": "academic_class.name",
  Title: "title",
  Provider: "provider",
  "Store ID": "store_id",
  "Store Password": "store_password",
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
        provider: "",
        institution_id: "",
        academic_class_id: "",
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
    this.setBreadcrumbs(this.model, "index", "Payment Gateway", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>