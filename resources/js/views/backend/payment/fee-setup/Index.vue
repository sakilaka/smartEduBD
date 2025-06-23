<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!------------ Single Input ------------>
        <SelectSearch
          v-if="!$root.institution_id"
          title="All Institution"
          field="institution_id"
          :datas="$root.global.institutions"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Shift"
          field="shift_id"
          :datas="$root.global.shifts"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Medium/Version"
          field="medium_id"
          :datas="$root.global.mediums"
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
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Group"
          field="group_id"
          :datas="$root.global.groups"
          val="id"
          val_title="name"
        />

        <Search :fields_name="fields_name" :search_field="false" />
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
const model = "feeSetup";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Fee Setup",
  icon: "plus-circle",
};

// routes name
const routes = {
  view: model + ".show",
  edit: model + ".edit",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "institution.name", title: "Institution" },
  { field: "shift.name", title: "Shift" },
  { field: "medium.name", title: "Medium/Version" },
  { field: "academic_class.name", title: "Academic Class" },
  { field: "group.name", title: "Group" },
];
//json fields for export excel
const json_fields = {
  Institution: "institution.name",
  "Medium/Version": "medium.name",
  "Academic Class": "academic_class.name",
  Group: "group.name",
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
        institution_id: "",
        shift_id: "",
        medium_id: "",
        academic_class_id: "",
        group_id: "",
      },
      table: {
        columns: tableColumns,
        routes: routes,
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
    this.setBreadcrumbs(this.model, "index", "Fee Setup", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
  },
};
</script>