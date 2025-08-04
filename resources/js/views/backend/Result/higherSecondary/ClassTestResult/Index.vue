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
          title="All Academic Level"
          field="academic_qualification_id"
          :datas="$root.global.qualifications"
          loop_type="pluck"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Department"
          field="department_id"
          :datas="departments_filter(search_data.academic_qualification_id)"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Academic Class"
          field="academic_class_id"
          :datas="class_filter(search_data.academic_qualification_id)"
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
        <template v-slot:[`qualification`]="row">
          <td>
            {{ row.item.qualification ? row.item.qualification.name : "" }}
            <br />
            ({{ row.item.academic_class ? row.item.academic_class.name : "" }})
          </td>
        </template>

        <template v-slot:[`action`]="row">
          <td class="action" style="width: 13%">
            <button
              v-if="
                routes.syncResult && $root.checkPermission(routes.syncResult)
              "
              @click="syncResult(row.item.id)"
              title="Sync"
              class="btn btn-outline-primary btn-xs"
              :disabled="sync_spinner"
            >
              <span
                v-if="sync_spinner == row.item.id"
                class="spinner-border spinner-border-sm"
              ></span>
              <i v-else class="bx bx-sync me-0"></i>
            </button>

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
              v-if="$root.checkPermission('classTestResult.show')"
              :to="{
                name: 'classTestResult.show',
                params: { id: row.item.id },
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
const model = "classTestResult";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "class Test Result Entry",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "academic_session", title: "Session", subfield: "name" },
  { field: "department", title: "Dept. / Group", subfield: "name" },
  { field: "qualification", title: "Academic Level / Class", subfield: "name" },
  { field: "exam", title: "Exam Name", subfield: "name" },
  { field: "exam_date", title: "Exam Date", date: true },
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
      sync_spinner: false,
      routes: {
        edit: model + ".edit",
        destroy: model + ".destroy",
        published: model + ".published",
        syncResult: model + ".syncResult",
      },
      search_data: {
        pagination: 10,
        academic_session_id: "",
        academic_class_id: "",
        department_id: "",
        academic_qualification_id: "",
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

    syncResult(id) {
      if (this.sync_spinner) return false;

      this.sync_spinner = id;
      if (confirm("Are you sure want to sync result")) {
        axios.get(`${this.model}-sync/${id}`).then((res) => {
          this.notify(res.data.message, "success");
          this.sync_spinner = false;
        });
      }
    },

    search() {
      this.$route.query.page = "";
      this.get_paginate_data(this.model, this.search_data);
    },
  },
  async created() {
    this.setBreadcrumbs(this.model, "index", "Class Test Result ", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>