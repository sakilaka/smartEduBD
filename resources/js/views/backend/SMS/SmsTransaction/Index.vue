<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <Search :fields_name="fields_name" />
        <div v-if="$root.site.sms_balance" class="col-8 text-center">
          <h6>
            <span style="text-decoration: underline">Available Balance</span> :
            <span class="text-success">
              {{ $root.site.sms_balance | currency }} TK.
            </span>
          </h6>
        </div>
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
      >
        <template v-slot:[`action`]="row">
          <td class="action">
            <button
              v-if="
                routes.destroy &&
                $root.checkPermission(routes.destroy) &&
                row.item.status != 'success'
              "
              @click="notify('Delete', 'confirm', row.item.id, 'destroy')"
              title="Delete"
              class="btn btn-outline-danger btn-xs"
            >
              <i class="bx bx-trash me-0"></i>
            </button>
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
const model = "smsTransaction";

const routes = {
  destroy: model + ".destroy",
};

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "Recharge Balance",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "invoice_date", title: "Invoice Date" },
  { field: "invoice_number", title: "Invoice Number" },
  { field: "amount", title: "Amount", align: "center", amount: true },
  {
    field: "payment_date",
    title: "Payment Date",
    date: true,
  },
  { field: "status", title: "Status", align: "center", width: "10%" },
  { field: "action", title: "Action", align: "center", width: "10%" },
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
      fields_name: { 0: "Select One", invoice_number: "Invoice Number" },
      search_data: {
        pagination: 10,
        field_name: "invoice_number",
        value: "",
      },
      routes: routes,
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
    search() {
      this.$route.query.page = "";
      this.get_paginate_data(this.model, this.search_data);
    },
  },
  async created() {
    this.setBreadcrumbs(this.model, "index", "SMS Transaction", addOrBack);
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>