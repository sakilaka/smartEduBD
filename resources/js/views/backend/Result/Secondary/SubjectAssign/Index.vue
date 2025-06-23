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
          title="Medium"
          field="medium_id"
          :datas="$root.global.mediums"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="Academic Class"
          field="academic_class_id"
          :datas="classes"
          val="id"
          val_title="name"
        />

        <Search :search_field="false" :print_excel="false" />
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
const model = "secondarySubjectAssign";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Subject Assign",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "institution.name", title: "institution Name" },
  { field: "medium.name", title: "Medium/Version" },
  { field: "academic_class.name", title: "Academic Class" },
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
        institution_id: "",
        medium_id: "",
        academic_class_id: "",
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

  computed: {
    classes() {
      let classesArr = [];
      if (this.$root.global.classes) {
        classesArr = this.$root.global.classes.filter(
          (e) => e.institution_category_id == 2
        );
      }
      return classesArr;
    },
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
    this.setBreadcrumbs(
      this.model,
      "index",
      "Secondary Subject Assign",
      addOrBack
    );
    this.get_paginate_data(this.model, this.search_data);
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
  },
};
</script>