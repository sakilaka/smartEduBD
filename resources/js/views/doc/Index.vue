<template>
  <div class="card">
    <div class="card-body min-height">
      <!-- Search Form -->
      <form v-on:submit.prevent="search" class="col-12 row mb-2">
        <div class="col-2 pe-0">
          <input
            v-model="search_data.text"
            placeholder="Text"
            type="text"
            class="form-control form-control-sm"
          />
        </div>

        <!--Search-->
        <Search :fields_name="fields_name" />
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
      >
        <template v-slot:[`status`]="row">
          <td>{{ row.item.status }} sd</td>
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
const model = "admin";

// For Call back funciton use in data : { routes:{} }
const routes = {
  view: model + ".show",
  edit: model + ".edit",
  destroy: model + ".destroy",
  array: [
    {
      route: "size.edit",
      icon: "edit",
      btnColor: "info",
      callBack: (item) => this.clickCallback(item),
      hideCallBack: (item) => true,
      label: "Click Size",
    },
  ],
};

// routes name
const routes1 = {
  view: model + ".show",
  edit: model + ".edit",
  destroy: model + ".destroy",
  array: [
    {
      route: model + ".passGenerate",
      icon: "users",
      btnColor: "info",
    },
    {
      route: model + ".quotationList",
      icon: "users",
      btnColor: "success",
    },
  ],
};

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: model,
  // cusTitle: "Update Content",
  icon: "plus-circle",
  extraButtons: [
    {
      route: model + ".index",
      icon: "plus-circle",
      btnColor: "primary",
      title: "Extra 1",
    },
    {
      route: model + ".create",
      icon: "list-ul",
      btnColor: "success",
      title: "Extra 2",
    },
  ],
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "name", title: "Name" },
  { field: "email", title: "Email" },
  {
    field: "image",
    title: "Image",
    image: true,
    imgWidth: "50px",
    height: "50px",
  },
  { field: "role.name", title: "Role Name" },
  {
    field: "album.name",
    title: "Album Name",
    align: "center",
  },
  {
    field: "status",
    title: "Custom Arry Value",
    array: true,
    array_value: [{ active: "Active", deactive: "Deactive", p: "Pending" }],
  },
  {
    field: "status",
    title: "Status",
    align: "center",
    width: "10%",
  },
  { field: "created_at", title: "Created at", date: true },
  {
    field: "sorting",
    title: "Sorting",
    sorting: true,
    namespace: "System-Menu",
    auto: "", // auto true / ""
    align: "center",
  },
];

//json fields for export excel
const json_fields = {
  Name: "name",
  Name: "album.name", // sub filed album, colunm = name
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
      },
      data: {},
      errors: {},
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
    callBack(id = null) {
      this.destroy(id);
    },
    destroy(id) {
      alert(id);
      return false;
      this.destroy_data(this.model, id, this.search_data);
    },
    search() {
      this.$route.query.page = "";
      this.get_paginate_data(this.model, this.search_data);
    },
    clickCallback(item) {
      console.log(item);
    },
  },
  // For promise request
  // async mounted() {
  //   const res = this.callApi("get", "admin");
  //   console.log(res.data);
  // },
  created() {
    this.get_paginate_data(this.model, this.search_data);
    list;
    this.getRouteName(this.model);
    this.setBreadcrumbs(this.model, "create", null, addOrBack);
    // this.get_paginate_data("admin", { allData: true }, "admins"); // get admins
  },
  beforeCreate() {
    this.$root.spinner = true;
  },
};
</script>

<style>
</style>