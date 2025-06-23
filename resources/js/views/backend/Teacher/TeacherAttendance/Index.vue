<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
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
        <Search :fields_name="fields_name" :search_field="false" />
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
      >
        <template v-slot:[`total_absent`]="row">
          <td class="text-center">
            {{
              parseInt(row.item.total_teacher) -
              parseInt(row.item.total_present)
            }}
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
const model = "teacherAttendance";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: "Teacher Attendance",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "date", title: "Attendance Date", date: true },
  { field: "total_teacher", title: "Total Teacher", align: "center" },
  { field: "total_present", title: "Total Present", align: "center" },
  { field: "total_absent", title: "Total Absent", align: "center" },
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
      fields_name: {},
      search_data: {
        pagination: 10,
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
    this.setBreadcrumbs(this.model, "index", "Teacher Attendance", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>