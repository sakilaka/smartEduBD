<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!------------ Single Input ------------>
        <div class="col-6 col-xl-1 pe-0">
          <select
            @change="search()"
            v-model="search_data.type"
            class="form-select form-select-sm"
          >
            <option value="">All</option>
            <option
              v-for="(value, name, index) in $root.global.notice_types"
              :key="index"
              :value="name"
            >
              {{ value }}
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
        <template v-slot:[`title`]="row">
          <td>
            <a
              @click="$bvModal.show('view-notice'), (view_notice = row.item)"
              href="javascript:void(0)"
            >
              {{ row.item.title }}
            </a>
          </td>
        </template>
        <template v-slot:[`notice_from`]="row">
          <td>
            <span v-if="row.item.department">
              {{ row.item.department.name }}
            </span>
            <span v-else> Principal’s Office </span>
          </td>
        </template>
        <template v-slot:[`image`]="row">
          <td class="text-center">
            <template v-if="row.item.image">
              <a
                class="btn btn-outline-primary btn-xs"
                :href="row.item.image"
                target="_blank"
              >
                <i class="bx bxs-show"></i>
              </a>
              <a
                class="btn btn-outline-success btn-xs ml-3"
                :href="row.item.image"
                download="download"
              >
                <i class="bx bxs-download"></i>
              </a>
            </template>
            <small v-else>No Image/File</small>
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

    <!-- view Modal -->
    <b-modal id="view-notice" size="lg" title="Notice Details">
      <div class="col-12">
        <table class="table table-bordered table-hover">
          <tr>
            <th style="width: 20%">Date</th>
            <td>
              {{ view_notice.date | formatDate }}
            </td>
          </tr>
          <tr>
            <th>Title</th>
            <td>
              {{ view_notice.title }}
            </td>
          </tr>
          <tr>
            <th>Image</th>
            <td>
              <template v-if="view_notice.image">
                <a
                  class="btn btn-outline-primary btn-xs"
                  :href="view_notice.image"
                  target="_blank"
                >
                  <i class="bx bxs-show"></i>
                </a>
                <a
                  class="btn btn-outline-success btn-xs ml-3"
                  :href="view_notice.image"
                  download="download"
                >
                  <i class="bx bxs-download"></i>
                </a>
              </template>
              <small v-else>No Image/File</small>
            </td>
          </tr>
          <tr>
            <th>Description</th>
            <td>
              <div v-html="view_notice.description"></div>
            </td>
          </tr>
        </table>
      </div>
    </b-modal>
  </div>
</template>

<script>
// define model name
const model = "notice";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: model,
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "institution.name", title: "Institution" },
  {
    field: "type",
    title: "Type",
    array: true,
    array_value: [
      {
        public: "Public Notice",
        office: "Office Notice",
        student: "Student Notice",
      },
    ],
    width: "12%",
  },
  { field: "date", title: "Date", date: true, width: "12%" },
  { field: "title", title: "Title" },
  { field: "notice_from", title: "Notice From", width: "12%" },
  {
    field: "image",
    title: "Image/File",
    align: "center",
    width: "12%",
  },
];
//json fields for export excel
const json_fields = {
  Type: "type",
  Title: "title",
  Date: "Date",
  "Department/Group": "department.name",
  "Image / File": "image",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      view_notice: {},
      fields_name: { 0: "Select One", title: "Title" },
      search_data: {
        pagination: 10,
        field_name: "title",
        value: "",
        type: "",
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
    this.setBreadcrumbs(this.model, "index", null, addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>