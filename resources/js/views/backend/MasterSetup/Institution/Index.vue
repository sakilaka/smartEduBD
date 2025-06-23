<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <div class="col-6 col-xl-2 pe-0">
          <select
            @change="search()"
            v-model="search_data.package_id"
            class="form-select form-select-sm"
          >
            <option value>All Package</option>
            <option
              v-for="(pkg, index) in packages"
              :key="index"
              v-bind:value="pkg.id"
            >
              {{ pkg.name }}
            </option>
          </select>
        </div>
        <Search :fields_name="fields_name" />
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
      >
        <template v-slot:[`id`]="row">
          <td class="text-center">
            <router-link
              v-if="$root.checkPermission('academicClassMapping.edit')"
              :to="{
                name: 'academicClassMapping.edit',
                params: { id: row.item.id },
              }"
              title="Class Mapping"
              class="btn btn-outline-primary btn-xs"
            >
              <i class="bx bx-sitemap"></i>
            </router-link>
          </td>
        </template>
      </base-table>

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
const model = "institution";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: model,
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  {
    field: "logo",
    title: "Logo",
    image: true,
    height: "30px",
    align: "center",
  },
  { field: "name", title: "Institution Name" },
  { field: "short_name", title: "Short Name" },
  { field: "package.name", title: "Package Name" },
  { field: "expired_date", title: "Package Expired", date: true },
  { field: "status", title: "Status", align: "center", width: "8%" },
  { field: "id", title: "Class Mapping", align: "center", width: "12%" },
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
      fields_name: { 0: "Select One", name: "Name" },
      search_data: {
        pagination: 10,
        field_name: "name",
        value: "",
        package_id: "",
      },
      table: {
        columns: tableColumns,
        routes: {},
        datas: [],
        meta: [],
        links: [],
      },
      packages: [],
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
    this.get_data("package", "packages", { allData: true });
  },
};
</script>