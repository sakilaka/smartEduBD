<template>
  <div>
    <div v-if="info" class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
      <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Today’s Payment</p>
                <h4 class="my-1 text-info">
                  {{
                    info.todays_payment
                      ? info.todays_payment
                      : "0.00" | currency
                  }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-scooter">৳</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Previous Day Payment</p>
                <h4 class="my-1 text-info">
                  {{
                    info.prev_day_payment
                      ? info.prev_day_payment
                      : "0.00" | currency
                  }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-scooter">৳</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Last 7 Day's Payment</p>
                <h4 class="my-1 text-info">
                  {{
                    info.prev7_day_payment
                      ? info.prev7_day_payment
                      : "0.00" | currency
                  }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-scooter">৳</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div class="card radius-10 border-start border-0 border-3 border-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Current Year Payment</p>
                <h4 class="my-1 text-info">
                  {{
                    info.current_year_payment
                      ? info.current_year_payment
                      : "0.00" | currency
                  }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-scooter">৳</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div
          class="card radius-10 border-start border-0 border-3 border-success"
        >
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Total Payment</p>
                <h4 class="my-1 text-success">
                  {{
                    info.total_payment ? info.total_payment : "0.00" | currency
                  }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-ohhappiness">
                ৳
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div
          class="card radius-10 border-start border-0 border-3 border-danger"
        >
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Today’s No. of Transaction</p>
                <h4 class="my-1 text-danger">
                  {{ info.todays_trans ? info.todays_trans : 0 }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-bloody">
                <i class="bx bxs-wallet"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div
          class="card radius-10 border-start border-0 border-3 border-danger"
        >
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Prev. Day No. of Transaction</p>
                <h4 class="my-1 text-danger">
                  {{ info.prev_day_trans ? info.prev_day_trans : 0 }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-bloody">
                <i class="bx bxs-wallet"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <div
          class="card radius-10 border-start border-0 border-3 border-warning"
        >
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div>
                <p class="mb-0 text-secondary">Total Students</p>
                <h4 class="my-1 text-warning">
                  {{ info.total_student | currency(0) }}
                </h4>
              </div>
              <div :class="commonClass()" class="bg-gradient-blooker">
                <i class="bx bxs-group"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment History -->
    <div class="card radius-10">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2">
          <div>
            <h6 class="mb-0">Today's Payments</h6>
          </div>
        </div>
        <!-- BaseTable -->
        <base-table
          :data="table.datas"
          :columns="table.columns"
          :routes="table.routes"
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
          <template v-slot:[`academic`]="row">
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
            </td>
          </template>
        </base-table>
      </div>
    </div>
  </div>
</template>

<script>
// define table coloumn show in datatable / datalist
const tableColumns = [
  {
    field: "student",
    title: "Student Info",
  },
  { field: "shift.name", title: "Shift" },
  { field: "campus.name", title: "Campus" },
  { field: "academic", title: "Academic Info" },
  { field: "purpose", title: "Purpose" },
  { field: "invoice", title: "Invoice", align: "center" },

  { field: "payment", title: "Payment", align: "center" },
  { field: "amount", title: "Amount", align: "center", amount: true },
  { field: "status", title: "Status", align: "center" },
];

export default {
  data() {
    return {
      search_data: {
        pagination: 15,
        status: "success",
        from_date: new Date().toISOString().slice(0, 10),
        to_date: new Date().toISOString().slice(0, 10),
      },
      info: {},
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
    commonClass() {
      return "widgets-icons-2 rounded-circle  bg-gradient-ohhappiness text-white  ms-auto";
    },
  },

  async created() {
    this.get_paginate_data("today-payments", this.search_data);
    const res = await this.callApi("get", "get-dashboard-info");
    this.info = res.data;
  },

  beforeCreate() {
    breadcrumbs.dispatch("setBreadcrumbs", []);
  },
};
</script>
<style>
.btn .badge {
  position: absolute;
}
.btn.btn-app {
  width: 105px;
  font-size: 12px;
  white-space: inherit;
  height: 90px;
}
</style>
