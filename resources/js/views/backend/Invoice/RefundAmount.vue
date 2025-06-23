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
          title="All Academic Class"
          field="academic_class_id"
          :datas="class_filter(search_data.academic_qualification_id)"
          val="id"
          val_title="name"
        />
        <div class="col-6 col-xl-2 pe-0">
          <b-form-datepicker
            v-model="search_data.from_date"
            id="datepicker1"
            size="sm"
            :date-format-options="{
              year: 'numeric',
              month: 'short',
              day: '2-digit',
            }"
            placeholder="From Date"
          ></b-form-datepicker>
        </div>
        <div class="col-6 col-xl-2 pe-0">
          <b-form-datepicker
            v-model="search_data.to_date"
            id="datepicker2"
            size="sm"
            :date-format-options="{
              year: 'numeric',
              month: 'short',
              day: '2-digit',
            }"
            placeholder="To Date"
          ></b-form-datepicker>
        </div>
        <div class="w-100 mt-2"></div>
        <Search :fields_name="fields_name" />
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
        :totals="totals()"
      >
        <template v-slot:[`student_name`]="row">
          <td>
            <template v-if="row.item.student">
              <b> {{ row.item.student.student_id }}</b> <br />
              {{ row.item.student.name }} <br />
              {{ row.item.student.mobile }}
            </template>
          </td>
        </template>
        <template v-slot:[`qualification`]="row">
          <td>
            {{ row.item.qualification ? row.item.qualification.name : "" }}
            <br />
            ({{ row.item.academic_class ? row.item.academic_class.name : "" }})
          </td>
        </template>
        <template v-slot:[`invoice`]="row">
          <td>
            <router-link
              :to="{ name: 'invoice.show', params: { id: row.item.id } }"
            >
              {{ row.item.invoice_number }}
              <br />
              {{ row.item.payment_date }}
            </router-link>
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
const model = "invoice";

// Add Or Back
const addOrBack = {};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "student_name", title: "Student", subfield: "name" },
  { field: "student", title: "Admission ID", subfield: "admission_id" },
  { field: "department", title: "Dept. / Group", subfield: "name" },
  { field: "qualification", title: "Academic Level / Class", subfield: "name" },
  { field: "head", title: "Purpose", subfield: "name" },
  { field: "invoice", title: "Invoice" },
  {
    field: "refund_amount",
    title: "Refund Amount",
    align: "center",
    amount: true,
  },
  { field: "amount", title: "Amount", align: "center", amount: true },
];

//json fields for export excel
const json_fields = {
  "Software ID": "student.student_id",
  "Admission ID": "student.admission_id",
  "Registration No.": "student.reg_no",
  "Student Name": "student.name",
  "Mobile No": "student.mobile",
  Department: "department.name",
  "Academic Level": "qualification.name",
  "Academic Class": "academic_class.name",
  Purpose: "head.name",
  "Invoice Date": "invoice_date",
  "Payment Date": "payment_date",
  "Invoice Number": "invoice_number",
  Amount: "amount",
};

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: {
        0: "Select One",
        invoice_number: "Invoice No.",
        student_id: "Software ID",
        name: "Name",
        mobile: "Mobile",
        reg_no: "Reg No.",
        admission_id: "Admission ID",
      },
      search_data: {
        pagination: 10,
        field_name: "mobile",
        value: "",
        academic_class_id: "",
        academic_session_id: "",
        academic_qualification_id: "",
        status: "success",
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
    classes() {
      let filters = [];
      if (this.search_data.academic_qualification_id) {
        filters = this.$root.global.classes.filter(
          (e) =>
            e.academic_qualification_id ==
            this.search_data.academic_qualification_id
        );
      }
      return filters;
    },

    totalAmount() {
      let total = 0;
      if (this.table.datas && Object.keys(this.table.datas).length > 0) {
        total = this.table.datas
          .map((item) => item.amount)
          .reduce((prev, next) => parseFloat(prev) + parseFloat(next));
      }
      return total;
    },
    totalRefundAmount() {
      let total = 0;
      if (this.table.datas && Object.keys(this.table.datas).length > 0) {
        total = this.table.datas
          .map((item) => item.refund_amount)
          .reduce((prev, next) => parseFloat(prev) + parseFloat(next));
      }
      return total;
    },
  },
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },
  methods: {
    // Async Data
    asyncData() {
      this.setBreadcrumbs(this.model, "index", "Refund Amount", addOrBack);
      this.get_paginate_data("refund-amount", this.search_data);
    },

    search() {
      this.$route.query.page = "";
      this.get_paginate_data("refund-amount", this.search_data);
    },

    totals() {
      return [
        {
          title: "Refund Amount",
          amount: this.totalRefundAmount,
        },
        {
          title: "Total Amount",
          amount: this.totalAmount,
        },
      ];
    },
  },
};
</script>