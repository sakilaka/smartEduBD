<template>
  <div class="card">
    <div class="card-body min-height">
      <!-- Search Form -->
      <form v-on:submit.prevent="search" class="col-12 row mb-2">
        <div class="col-6 col-xl-2 pe-0">
          <date-picker
            v-model="search_data.from_date"
            valueType="format"
            format="YYYY-MMM-DD"
            :formatter="momentFormat"
            placeholder="Select Date"
          ></date-picker>
        </div>
        <div class="col-6 col-xl-2 pe-0">
          <date-picker
            v-model="search_data.to_date"
            valueType="format"
            format="YYYY-MMM-DD"
            :formatter="momentFormat"
            placeholder="Select Date"
          ></date-picker>
        </div>
        <div class="col-6 col-xl-2 pe-0">
          <select
            @change="search()"
            v-model="search_data.description"
            class="form-select form-select-sm"
          >
            <option value>All User</option>
            <option
              v-for="(admins, index) in extraData.admins"
              :key="index"
              v-bind:value="admins.name"
            >
              {{ admins.name }}
            </option>
          </select>
        </div>

        <div class="col-6 col-xl-1 pe-0">
          <select
            @change="search()"
            v-model="search_data.action"
            class="form-select form-select-sm"
          >
            <option value>All</option>
            <option>Created</option>
            <option>Updated</option>
            <option>Deleted</option>
          </select>
        </div>
        <div class="col-6 col-xl-1 pe-0">
          <select
            @change="search()"
            v-model="search_data.status"
            class="form-select form-select-sm"
          >
            <option value>All</option>
            <option value="r">Read</option>
            <option value="ur">Unread</option>
          </select>
        </div>

        <!--Search-->
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
const model = "activityLog";

// Add Or Back
const addOrBack = {};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "log_name", title: "Module" },
  { field: "description", title: "Description" },
  { field: "status", title: "Status", align: "center", width: "10%" },
  { field: "created_at", title: "Created at", date: true },
];
//json fields for export excel
const json_fields = {
  Module: "log_name",
  Description: "description",
  Status: "status",
  "Created at": "created_at",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: {
        0: "Select One",
        log_name: "Module",
        description: "Description",
      },
      search_data: {
        pagination: 10,
        field_name: 0,
        value: "",
        from_date: "",
        to_date: "",
        description: "",
        action: "",
        status: "ur",
        date: "",
      },
      extraData: {
        admins: [],
      },
      table: {
        columns: tableColumns,
        routes: {
          view: model + ".show",
          destroy: model + ".destroy",
        },
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
      this.get_paginate_data(this.model, this.search_data);
    },
    allRead() {
      this.$root.spinner = true;
      axios
        .get("/allRead")
        .then((res) => {
          this.search();
          this.notify(res.data.message, "success");
        })
        .catch((error) => console.log())
        .then((alw) => setTimeout(() => (this.$root.spinner = false), 200));
    },
  },
  created() {
    this.get_paginate_data(this.model, this.search_data);
    this.get_data("admin", "admins", { allData: true });
    this.setBreadcrumbs(this.model, "index", "Activity Log", addOrBack);
  },
};
</script>
