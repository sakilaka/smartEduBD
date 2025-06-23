<template>
  <div class="card">
    <div class="card-body min-height">
      <!-- Search Form -->
      <form v-on:submit.prevent="search" class="col-12 row mb-2">
        <SelectSearch
          title="Menu type"
          field="type"
          :datas="menu_types"
          loop_type="pluck"
        />
        <SelectSearch
          title="Menu Position"
          field="position"
          :datas="positions"
          loop_type="pluck"
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
const model = "frontMenu";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Menu",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "parent.title", title: "Parent" },
  { field: "title", title: "Title" },
  { field: "type", title: "Menu Type", align: "center" },
  { field: "content.title", title: "Content" },
  {
    field: "sorting",
    title: "Sorting",
    sorting: true,
    namespace: "Website-FrontMenu",
    auto: "",
    align: "center",
  },
];
//json fields for export excel
const json_fields = {
  "Parent Menu": "parent.title",
  Title: "title",
  "Menu Type": "type",
  "Menu Look Type": "menu_look_type",
  Content: "content.title",
  Position: "position",
  Sorting: "sorting",
  "Created at": "created_at",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      positions: {
        header: "Header",
        top_bar: "Top Bar",
        footer_bottom: "Footer Bottom",
      },
      menu_types: {
        content: "Content",
        external_link: "External Link",
        outside_website: "Outside Website",
      },
      fields_name: { 0: "Select One", title: "Title", sorting: "Sorting" },
      search_data: {
        pagination: 10,
        field_name: "title",
        value: "",
        type: "",
        position: "",
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
    this.setBreadcrumbs(this.model, "index", "Menu", addOrBack);
    this.get_paginate_data(this.model, this.search_data);
  },
};
</script>