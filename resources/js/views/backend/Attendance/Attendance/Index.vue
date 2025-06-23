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
          title="All Campus"
          field="campus_id"
          :datas="campuses_filter(search_data.institution_id)"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Shift"
          field="shift_id"
          :datas="shift_filter(search_data.institution_id, 'search_data')"
          val="shift_id"
          val_title="shift_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Medium"
          field="medium_id"
          :datas="medium_filter(search_data.institution_id, 'search_data')"
          val="medium_id"
          val_title="medium_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Class"
          field="academic_class_id"
          :datas="class_filter(search_data.institution_id, 'search_data')"
          val="academic_class_id"
          val_title="class_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="Group"
          field="group_id"
          :datas="group_filter(search_data.institution_id, 'search_data')"
          val="group_id"
          val_title="group_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="Section"
          field="section_id"
          :datas="section_filter(search_data.institution_id, 'search_data')"
          val="section_id"
          val_title="section_name"
          col="2"
        />

        <div class="w-100 my-1"></div>

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
        <template v-slot:[`absent`]="row">
          <td class="text-center">
            <template v-if="row.item">
              {{ row.item.total_student - row.item.present_student_count }}
            </template>
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
const model = "attendance";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  title: model,
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "date", title: "Attendance Date", date: true },
  { field: "shift.name", title: "Shift" },
  { field: "campus.name", title: "Campus" },
  { field: "group.name", title: "Group" },
  { field: "section.name", title: "Section" },
  { field: "academic_class.name", title: "Class" },
  { field: "total_student", title: "Total Student", align: "center" },
  { field: "present_student_count", title: "Present", align: "center" },
  { field: "absent", title: "Absent", align: "center" },
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
        institution_id: "",
        campus_id: "",
        medium_id: "",
        group_id: "",
        section_id: "",
        shift_id: "",
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
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
  },
};
</script>