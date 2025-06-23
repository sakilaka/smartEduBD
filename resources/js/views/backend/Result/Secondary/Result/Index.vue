<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Session"
          field="academic_session_id"
          :datas="sessions_filter(2)"
          val="id"
          val_title="name"
        />
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
          :req="true"
          :datas="shift_filter(search_data.institution_id, 'search_data')"
          val="shift_id"
          val_title="shift_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Medium"
          field="medium_id"
          :req="true"
          :datas="medium_filter(search_data.institution_id, 'search_data')"
          val="medium_id"
          val_title="medium_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Class"
          field="academic_class_id"
          :datas="
            category_classes_filter(
              search_data.institution_id,
              2,
              'search_data'
            )
          "
          val="academic_class_id"
          val_title="class_name"
        />
        <div class="w-100 my-1"></div>
        <!------------ Single Input ------------>
        <SelectSearch
          title="Group"
          field="group_id"
          :req="true"
          :datas="group_filter(search_data.institution_id, 'search_data')"
          val="group_id"
          val_title="group_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="Section"
          field="section_id"
          :req="true"
          :datas="section_filter(search_data.institution_id, 'search_data')"
          val="section_id"
          val_title="section_name"
        />

        <Search :search_field="false" :print_excel="false" />
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
      >
        <template v-slot:[`shift`]="row">
          <td>
            {{ row.item.shift.name }}
            <br />
            {{ row.item.medium.name }}
          </td>
        </template>
        <template v-slot:[`academic`]="row">
          <td>
            {{ row.item.academic_class.name }}
            <br />
            {{ row.item.group.name }} ({{ row.item.section.name }})
          </td>
        </template>

        <template v-slot:[`action`]="row">
          <td class="action" style="width: 13%">
            <button
              v-if="routes.published && $root.checkPermission(routes.published)"
              @click="publishedCallback(row.item.id)"
              class="btn btn-xs"
              :title="
                row.item.status == 'published' ? 'Unpublished' : 'Published'
              "
              :class="
                row.item.status == 'published'
                  ? 'btn-outline-danger'
                  : 'btn-outline-success'
              "
            >
              <i
                v-if="row.item.status != 'published'"
                class="bx bxs-lock-open-alt me-0"
              ></i>
              <i v-else class="bx bxs-lock-alt"></i>
            </button>

            <router-link
              v-if="$root.checkPermission('secondaryResult.result')"
              :to="{
                name: 'secondaryResult.result',
                query: { result_id: row.item.id },
              }"
              title="View Result"
              class="btn btn-outline-success btn-xs"
            >
              <i class="bx bxs-show me-0"></i>
            </router-link>

            <template v-if="row.item.status != 'published'">
              <router-link
                v-if="routes.edit && $root.checkPermission(routes.edit)"
                :to="{ name: routes.edit, params: { id: row.item.id } }"
                title="Edit"
                class="btn btn-outline-primary btn-xs"
              >
                <i class="bx bx-edit me-0"></i>
              </router-link>

              <button
                v-if="routes.destroy && $root.checkPermission(routes.destroy)"
                @click="notify('Delete', 'confirm', row.item.id, 'destroy')"
                title="Delete"
                class="btn btn-outline-danger btn-xs"
              >
                <i class="bx bx-trash me-0"></i>
              </button>
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
import PublishedConfirm from "./PublishedConfirm.vue";

// define model name
const model = "secondaryResult";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "Result Entry",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "academic_session.name", title: "Session" },
  { field: "campus.name", title: "Campus" },
  { field: "shift", title: "Shift" },
  { field: "academic", title: "Academic Info" },
  { field: "exam.name", title: "Exam Name" },
  {
    field: "published_date",
    title: "Published Date",
    date: true,
  },
  { field: "status", title: "Status", align: "center", width: "10%" },
  { field: "action", title: "Action", align: "center" },
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
      routes: {
        edit: model + ".edit",
        destroy: model + ".destroy",
        published: model + ".published",
      },
      search_data: {
        pagination: 10,
        academic_session_id: "",
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
    callBack(id) {
      this.destroy(id);
    },
    destroy(id) {
      this.destroy_data(this.model, id, this.search_data);
    },

    publishedCallback(item) {
      this.$toast.info(
        {
          component: PublishedConfirm,
          listeners: {
            confirm: (inputValue) => {
              this.publishedSubmitCallback(item, inputValue);
            },
          },
        },
        {
          position: "top-center",
          timeout: 0,
          closeButton: false,
        }
      );
    },
    publishedSubmitCallback(result_id, pub_date) {
      this.$root.tableSpinner = true;
      let data = {
        result_id: result_id,
        published_date: pub_date,
      };

      axios
        .post(`${this.model}-published`, data)
        .then((res) => {
          if (res.data.message) {
            this.notify(res.data.message, "success");
            this.get_paginate_data(this.model, this.search_data);
          } else if (res.data.error) {
            this.notify(res.data.error, "error");
            return false;
          }
        })
        .catch((err) => console.log(err));
    },

    search() {
      this.$route.query.page = "";
      this.get_paginate_data(this.model, this.search_data);
    },
  },
  async created() {
    this.setBreadcrumbs(this.model, "index", "Secondary Result", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
  },
};
</script>