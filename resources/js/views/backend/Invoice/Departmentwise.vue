<template>
  <div class="card">
    <div class="card-body min-height">
      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
        :totals="totals()"
      >
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
const model = "invoice";

// breadcrumb
const breadcrumb = {
  breadcrumb: [
    {
      route: "invoice.departmentWise",
      title: "Total Department Wise Payment Report",
    },
  ],
  addOrBack: {},
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "name", title: "Department/Group Name" },
  {
    field: "invoice_count",
    title: "Total Trans.",
    align: "center",
  },
  {
    field: "total_payment",
    title: "Total Amount",
    align: "right",
    amount: true,
  },
];

export default {
  data() {
    return {
      model: model,
      fields_name: {
        0: "Select One",
        invoice_number: "Invoice No.",
        name: "Name",
        mobile: "Mobile",
        reg_no: "Reg No.",
        admission_id: "Admission ID",
      },
      search_data: {
        pagination: 50,
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
  computed: {
    totalAmount() {
      let total = 0;
      let totalTrans = 0;
      if (this.table.datas && Object.keys(this.table.datas).length > 0) {
        this.table.datas.forEach((el) => {
          if (el.total_payment) {
            total += parseFloat(el.total_payment);
            totalTrans += parseFloat(el.invoice_count);
          }
        });
      }
      return { total: total, totalTrans: totalTrans };
    },
  },
  methods: {
    search() {
      this.$route.query.page = "";
      this.get_paginate_data("department-wise-payment", this.search_data);
    },

    totals() {
      return [
        {
          title: "Total Trans.",
          amount: this.totalAmount.totalTrans,
        },
        {
          title: "Total Amount",
          amount: this.totalAmount.total,
        },
      ];
    },
  },

  created() {
    breadcrumbs.dispatch("setBreadcrumbs", breadcrumb);

    this.get_paginate_data("department-wise-payment", this.search_data);
  },
};
</script>