<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <div class="col-6 col-xl-1 pe-0 mb-2">
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
          title="All Institution Category"
          field="institution_category_id"
          :datas="$root.global.institution_categories"
          val="id"
          val_title="name"
        />
        <!------------ Single Input ------------>
        <SelectSearch
          title="All Session"
          field="academic_session_id"
          :datas="
            sessions_filter(search_data.institution_category_id, 'search_data')
          "
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
          col="1"
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
          :datas="
            category_classes_filter(
              search_data.institution_id,
              search_data.institution_category_id,
              'search_data'
            )
          "
          val="academic_class_id"
          val_title="class_name"
          class="mb-2"
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
        />
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

        <div class="col-6 col-xl-2 pe-0">
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

        <div class="col-6 col-xl-2 pe-0">
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
        <Search :fields_name="fields_name" class="mt-2" />

        <!-- <div class="col-2">
          <a
            @click="printInvoice()"
            class="btn btn-sm btn-primary float-end"
            href="javascript:void(0)"
          >
            Print Invoice
          </a>
        </div> -->
      </form>

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
            {{ row.item.academic_class.name }} ({{
              row.item.academic_session?.name
            }})
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

        <!-- Actions -->
        <template v-slot:[`action`]="row">
          <td class="text-center">
            <button
              v-if="$root.checkPermission(model + '.edit')"
              @click="editInvoiceModal(row.item)"
              title="Edit"
              class="btn btn-outline-primary btn-xs"
            >
              <i class="bx bx-edit me-0"></i>
            </button>
            <button
              v-if="
                $root.checkPermission(model + '.destroy') &&
                row.item.status != 'success'
              "
              @click="notify('Delete', 'confirm', row.item.id)"
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

    <!-- Edit Modal -->
    <b-modal id="edit-invoice" title="Paid Invoice">
      <div class="col-12">
        <div class="row">
          <div class="col-12 mb-2 border-bottom pb-2">
            <label>
              Software ID: <b>{{ editInvoice.software_id }}</b>
            </label>
            <label>
              Name: <b>{{ editInvoice.name }}</b>
            </label>
            <label>
              Invoice No: <b>#{{ editInvoice.invoice_number }}</b>
            </label>
          </div>

          <div class="col-6">
            <label>Amount</label>
            <input
              class="form-control form-control-sm"
              v-model="editInvoice.amount"
              type="number"
              placeholder="Amount"
              :readonly="invoice_status == 'failed' ? true : false"
            />
          </div>
          <div class="col-6">
            <label>
              Account Head
              <i class="bx bx-edit" @click="account_head = !account_head"></i>
            </label>
            <select
              v-model="editInvoice.account_head_id"
              class="form-select form-select-sm"
              :disabled="account_head"
            >
              <option
                :value="head.id"
                v-for="head in $root.global.account_heads"
                :key="head.id"
              >
                {{ head.name_en }}
              </option>
            </select>
          </div>

          <div class="col-6 mt-2">
            <label> Payment Date </label>
            <input
              type="date"
              class="form-control form-control-sm"
              v-model="editInvoice.payment_date"
            />
          </div>
        </div>
      </div>
      <div class="col-12 d-flex justify-content-center my-4">
        <button
          type="button"
          :disabled="$root.submit ? true : false"
          class="btn btn-success btn-sm px-3"
          @click="paidInvoice()"
        >
          <span v-if="$root.submit">
            <span class="spinner-border spinner-border-sm"></span>
            <span>processing...</span>
          </span>
          <span v-else> <i class="bx bx-save"></i> Paid & Update Info</span>
        </button>
      </div>
    </b-modal>
  </div>
</template>

<script>
// define model name
const model = "invoice";

// Add Or Back
const addOrBack = {};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "student", title: "Student Info" },
  { field: "campus_shift", title: "Campus/Shift Info" },
  { field: "class_section", title: "Class/Section Info" },
  { field: "purpose", title: "Purpose" },
  { field: "invoice", title: "Invoice", align: "center" },

  { field: "payment", title: "Payment", align: "center" },
  { field: "amount", title: "Amount", align: "center", amount: true },
  { field: "status", title: "Status", align: "center", width: "5%" },
  { field: "action", title: "Action", align: "center", width: "7%" },
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
        institution_category_id: "",
        academic_session_id: "",
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
        status: "success",
      },
      table: {
        columns: tableColumns,
        routes: {},
        datas: [],
        meta: [],
        links: [],
      },

      account_head: true,
      invoice_status: true,
      refunded: false,
      total_amount: 0,
      editInvoice: {},
    };
  },
  computed: {
    totalAmount() {
      let total = 0;
      if (this.table.datas && Object.keys(this.table.datas).length > 0) {
        total = this.table.datas
          .map((item) => item.amount)
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
    },

    // Edit Invoice
    editInvoiceModal(item) {
      this.account_head = true;
      this.total_amount = item.amount;
      let date = new Date().toISOString().slice(0, 10);

      this.editInvoice = {
        id: item.id,
        amount: item.amount,
        status: "success",
        invoice_number: item.invoice_number,
        account_head_id: item.account_head_id,
        payment_date: item.payment_date ? item.payment_date : date,
        software_id: item.student ? item.student.software_id : "",
        name: item.student ? item.student.name_en : "",
      };

      this.$bvModal.show("edit-invoice");
    },

    // Update Invoice
    paidInvoice() {
      if (confirm("Are you sure want to update?")) {
        this.$root.submit = true;
        axios
          .put("/invoice/" + this.editInvoice.id, this.editInvoice)
          .then((res) => {
            this.notify(res.data.message, "success");
            this.search();
            this.$bvModal.hide("edit-invoice");
          })
          .catch((err) => this.notify("Something went wrong", "error"))
          .then((alw) => setTimeout(() => (this.$root.submit = false), 200));
      }
    },

    // For Delete
    callBack(id) {
      this.destroy(id);
    },
    destroy(id) {
      this.destroy_data(this.model, id, this.search_data);
    },

    printInvoice() {
      let data = this.search_data;
      if (!data.academic_session_id && !data.academic_qualification_id) {
        this.notify("Please select Session & Level & Departmant", "error");
        return false;
      }
      this.$router.push({
        name: "invoice.print",
        query: {
          session: data.academic_session_id,
          class: data.academic_class_id,
          account_head: data.account_head_id,
          status: data.status,
          payment_gateway_id: data.payment_gateway_id,
          from_date: data.from_date,
          to_date: data.to_date,
        },
      });
    },

    totals() {
      return [
        {
          title: "Total Amount",
          amount: this.totalAmount,
        },
      ];
    },
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
  },
};
</script>