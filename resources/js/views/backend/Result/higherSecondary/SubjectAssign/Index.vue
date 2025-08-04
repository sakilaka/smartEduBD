<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">

        <!------------ Single Input ------------>
        <SelectSearch
          title="All Academic Class"
          field="academic_class_id"
          :datas="$root.global.classes"
          val="id"
          val_title="name"
        />

        <!------------ Single Input ------------>
        <SelectSearch
          title="All Academic Groups"
          field="group_id"
          :datas="$root.global.groups"
          val="id"
          val_title="name"
        />
        <!------------ Button ------------>
        <div class="col-1">
          <button
            type="button"
            :disabled="$root.submit ? true : false"
            @click="search()"
            class="btn btn-sm btn-success"
          >
            <span v-if="$root.submit">
              <span class="spinner-border spinner-border-sm"></span>
            </span>
            <span v-else> <i class="bx bx-search mx-0 fw-bold"></i></span>
          </button>
        </div>
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
    const model = "higherSecondarySubjectAssign";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Subject Assign",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "academic_class", title: "Academic Class", subfield: "name" },
  { field: "group", title: "Academic Group", subfield: "name" },
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
      search_data: {
        pagination: 10,
        field_name: 0,
        value: "",
        academic_class_id: "",
        group_id: "",
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
    this.setBreadcrumbs(this.model, "index", "Subject Assign", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>
