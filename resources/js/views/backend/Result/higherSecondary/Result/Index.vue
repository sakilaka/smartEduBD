<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Session"
          field="academic_session_id"
          :datas="$root.global.sessions"
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
          title="All Academic Group"
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
      >
        <template v-slot:[`class`]="row">
          <td>
            ({{ row.item.academic_class ? row.item.academic_class.name : "" }})
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
              v-if="$root.checkPermission('result.result')"
              :to="{ name: 'result.result', query: { result_id: row.item.id } }"
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
const model = "higherSecondaryResult";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "Result Entry",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "academic_session", title: "Session", subfield: "name" },
  { field: "group", title: "Group", subfield: "name" },
  { field: "class", title: "Academic Level / Class", subfield: "name" },
  { field: "exam", title: "Exam Name", subfield: "name" },
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
    this.setBreadcrumbs(this.model, "index", null, addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>
