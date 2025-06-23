<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <div class="col-6 col-xl-1 pe-0">
          <select
            v-model="search_data.status"
            @change="search()"
            class="form-select form-select-sm"
          >
            <option value="">All</option>
            <option value="pending">Dues</option>
            <option value="success">Paid</option>
            <option value="failed">Failed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
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
          col="1"
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
          :datas="class_filter(search_data.institution_id, 'search_data')"
          val="academic_class_id"
          val_title="class_name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="Group"
          field="group_id"
          :req="true"
          :datas="group_filter(search_data.institution_id, 'search_data')"
          val="group_id"
          val_title="group_name"
          col="1"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="Section"
          field="section_id"
          :req="true"
          :datas="section_filter(search_data.institution_id, 'search_data')"
          val="section_id"
          val_title="section_name"
          col="1"
        />
        <div class="w-100 mt-2"></div>
        <!------------ Single Input ------------>
        <SelectSearch
          v-if="$route.name == 'invoice.accountHeadWise'"
          title="All Account Head"
          field="account_head_id"
          :datas="$root.global.account_heads"
          val="id"
          val_title="name_en"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          v-if="$route.name == 'invoice.accountWise'"
          title="All Account"
          field="payment_gateway_id"
          :datas="$root.global.accounts_commons"
          val="id"
          val_title="account_no"
        />

        <div class="col-6 col-xl-1 pe-0">
          <select
            v-model="search_data.payment_method"
            @change="search()"
            class="form-select form-select-sm"
          >
            <option value="">--Gateway--</option>
            <option value="SSL">SSL</option>
            <option value="SPAY">SPAY</option>
            <option value="DBL">DBL</option>
          </select>
        </div>

        <div class="col-6 col-xl-1 pe-0">
          <select
            v-model="search_data.created_from"
            @change="search()"
            class="form-select form-select-sm"
          >
            <option value="">--Created--</option>
            <option value="web">WEB</option>
            <option value="app">APP</option>
            <option value="dbank">DBL</option>
          </select>
        </div>

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
        <Search :fields_name="fields_name" />
      </form>

      <TotalCharge :item="total_charge" />

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
        :totals="totals()"
      >
        <template v-slot:[`student`]="row">
          <td>
            {{ row.item.student.software_id }}
            <br />
            {{ row.item.student.name_en }}
          </td>
        </template>
        <template v-slot:[`purpose`]="row">
          <td>
            {{ row.item.head.name_en }}
            <sup class="badge bg-secondary" v-if="row.item.details_count">
              {{ row.item.details_count }}'m
            </sup>
          </td>
        </template>
        <template v-slot:[`campus_shift`]="row">
          <td>
            {{ row.item.campus.name }} ({{ row.item.shift.name }})
            <br />
            {{ row.item.medium.name }}
          </td>
        </template>
        <template v-slot:[`class_section`]="row">
          <td>
            {{ row.item.academic_class.name }}
            <br />
            {{ row.item.group.name }} ({{ row.item.section.name }})
          </td>
        </template>
        <template v-slot:[`invoice`]="row">
          <td class="text-center">
            <router-link
              :to="{ name: 'invoice.show', params: { id: row.item.id } }"
            >
              #{{ row.item.invoice_number }}
              <br />
              {{ row.item.invoice_date | formatDate }}
            </router-link>
          </td>
        </template>
        <template v-slot:[`payment`]="row">
          <td
            :onClick="`alert('${row.item.bank_trans_id}')`"
            class="text-center"
          >
            {{ row.item.payment_date | formatDate }}
            <br />
            {{ row.item.payment_method }}
            <span class="text-uppercase">({{ row.item.created_from }})</span>
          </td>
        </template>
      </base-table>

      <!-- Pagination -->
      <div class="box-footer clearfix">
        <Pagination
          route_url="invoice.serviceCharge"
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
import TotalCharge from "./TotalCharge.vue";

// define model name
const model = "invoice";

// Add Or Back
const addOrBack = {};

const tableColumns = [
  { field: "student", title: "Student Info" },
  { field: "campus_shift", title: "Campus/Shift Info" },
  { field: "class_section", title: "Class/Section Info" },
  { field: "purpose", title: "Purpose" },
  { field: "invoice", title: "Invoice", align: "center" },

  { field: "payment", title: "Payment", align: "center" },
  { field: "amount", title: "Amount", align: "center", amount: true },
  {
    field: "service_charge",
    title: "Service Charge",
    align: "center",
    amount: true,
  },
];

//json fields for export excel
const json_fields = {
  Institution: "institution.name",
  Campus: "campus.name",
  Shift: "shift.name",
  "Medium/Version": "medium.name",
  "Academic Class": "academic_class.name",
  Group: "group.name",
  Section: "section.name",

  "Software ID": "student.software_id",
  "Name (en)": "student.name_en",

  Purpose: "head.name_en",
  "Invoice Date": "invoice_date",
  "Payment Date": "payment_date",
  "Invoice Number": "invoice_number",
  Amount: "amount",
  "Payment Method": "payment_method",
  "Created From": "created_from",
};

export default {
  components: { TotalCharge },

  data() {
    return {
      model: model,
      json_fields: json_fields,
      fields_name: {
        0: "Select One",
        bank_trans_id: "Bank Trans ID",
        invoice_number: "Invoice Number",
        software_id: "Software ID",
        name_en: "Name (en)",
        mobile: "Student Mobile",
        college_roll: "Roll",
        guardian_mobile: "Guardian Mobile",
      },
      search_data: {
        pagination: 10,
        field_name: "software_id",
        value: "",
        institution_id: "",
        campus_id: "",
        medium_id: "",
        group_id: "",
        section_id: "",
        shift_id: "",
        academic_class_id: "",
        payment_method: "",
        account_head_id: "",
        payment_gateway_id: "",
        created_from: "",
        service_charge: true,
        status: "success",
      },
      table: {
        columns: tableColumns,
        routes: {},
        datas: [],
        meta: [],
        links: [],
      },
      total_charge: { invoice: {}, admission: {}, hostel: {} },
    };
  },
  computed: {
    totalAmount() {
      let total = { service_charge: 0, gateway_charge: 0 };

      if (this.table.datas && Object.keys(this.table.datas).length > 0) {
        total.service_charge = this.table.datas
          .map((item) => parseFloat(item.service_charge ?? 0))
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
      this.setBreadcrumbs(
        this.model,
        "index",
        this.$route.meta.title,
        addOrBack
      );
      this.get_paginate_data(this.model, this.search_data);
    },

    search() {
      this.$route.query.page = "";
      this.get_paginate_data(this.model, this.search_data);
      this.getTotalCharge();
    },

    async getTotalCharge() {
      const res = await this.callApi(
        "get",
        "invoice-service-charge",
        this.search_data
      );
      if (res.status == 200) {
        this.total_charge = res.data;
      }
    },

    totals() {
      return [
        {
          title: "Service Charge",
          amount: this.totalAmount.service_charge,
        },
      ];
    },
  },

  mounted() {
    this.getTotalCharge();
  },
};
</script>