<template>
  <div>
    <div class="card">
      <div class="card-body">
        <form v-on:submit.prevent="search" class="row">
          <!-- { id: 'hostel', name: 'Hostel Payments' }, -->
          <SelectSearch
            title="Report Type"
            field="report_type"
            :datas="[
              { id: 'all', name: 'All Payments' },
              { id: 'invoice', name: 'Student Payments' },
              { id: 'admission', name: 'External Payments' },
            ]"
            val="id"
            val_title="name"
          />

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
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Account Head"
            field="account_head_id"
            :datas="$root.global.account_heads"
            loop_type="pluck"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Account"
            field="store_id"
            :datas="accounts_lists"
            val="store_id"
            val_title="account_no"
          />

          <div class="px-0 col-xl-1">
            <button type="submit" class="btn btn-sm btn-success">
              <!-- :disabled="$root.tableSpinner ? true : false" -->
              <span
                v-if="$root.tableSpinner"
                style="height: 1rem; width: 1rem; font-size: 11px"
                class="spinner-border text-white"
              ></span>

              <i v-else class="bx bx-search mx-0 fw-bold"></i>
            </button>
          </div>

          <div class="col-1">
            <a
              v-if="Object.keys(extraData.summaries).length > 0"
              class="btn btn-xs btn-success float-end"
              @click="print('printArea')"
              href="javascript:;"
            >
              <i class="bx bx-printer"></i> PRINT
            </a>
          </div>
        </form>
      </div>
    </div>

    <!-- data -->
    <div class="card">
      <div class="card-body">
        <div
          v-if="Object.keys(extraData.summaries).length > 0"
          id="printArea"
          class="row g-3 p-2"
          style="background: #fff"
        >
          <div v-if="$root.site" class="col-12 text-center">
            <h3>{{ $root.site.title_en }}</h3>
            <h5>Smart Education ERP</h5>
            <p class="mb-1">
              <b>{{ search_data.from_date | formatDate }}</b> to
              <b>{{ search_data.to_date | formatDate }}</b>
            </p>
            <p>{{ $root.baseurl }}</p>
          </div>
          <template v-for="(summaries, levelKey) in extraData.summaries">
            <div :key="`total_amount${levelKey}`" class="fw-bold p-0">
              <span style="font-size: 20px">
                {{ academicLevel(levelKey) }}
              </span>
              ( {{ totals(levelKey).total_amount | currency }} ) BDT
            </div>
            <table
              :key="`levelKey${levelKey}`"
              class="table table-bordered table-striped table-hover mt-2"
            >
              <thead>
                <tr>
                  <th width="500px">Department Name</th>
                  <th width="200px" class="text-center">Number of Student</th>
                  <th width="200px" class="text-center">Payment Purpose</th>
                  <th width="200px" class="text-center">Head Amount</th>
                  <th width="200px" class="text-center">Total Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, key) in summaries" :key="key">
                  <td>{{ row.dept_name }}</td>
                  <td class="text-center">{{ row.total_student }}</td>
                  <td class="text-center">{{ row.head_name_en }}</td>
                  <td class="text-center">{{ row.head_amount | currency }}</td>
                  <td class="text-center">{{ row.total_amount | currency }}</td>
                </tr>
                <tr>
                  <td></td>
                  <th class="text-center">
                    {{ totals(levelKey).total_student | currency(0) }}
                  </th>
                  <td></td>
                  <th class="text-end">Total =</th>
                  <th class="text-center">
                    {{ totals(levelKey).total_amount | currency }} BDT
                  </th>
                </tr>
              </tbody>
            </table>
          </template>
        </div>

        <div v-else class="py-5 text-center">No data found</div>
      </div>
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
      route: "invoice.accountSummary",
      title: "Settlement Summary",
    },
  ],
  addOrBack: {},
};

export default {
  data() {
    return {
      model: model,
      search_data: {
        account_head_id: "",
        store_id: "",
        report_type: "",
      },
      accounts_lists: [],
      extraData: {
        summaries: [],
      },
    };
  },

  watch: {
    "search_data.report_type": function (val) {
      if (val == "admission") {
        this.accounts_lists = this.$root.global.accounts_admissions;
      } else {
        this.accounts_lists = this.$root.global.accounts_commons;
      }
    },
  },
  methods: {
    search() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) return false;
        this.get_paginate_data(
          "account-summary",
          this.search_data,
          "summaries"
        );
      });
    },

    totals(key) {
      let total_amount = 0;
      let total_student = 0;
      if (Object.keys(this.extraData.summaries[key]).length > 0) {
        this.extraData.summaries[key].forEach((el) => {
          if (el.total_amount) {
            total_amount += parseFloat(el.total_amount);
            total_student += parseFloat(el.total_student);
          }
        });
      }
      return { total_student: total_student, total_amount: total_amount };
    },

    academicLevel(id) {
      let level = "";
      if (this.$root.global.qualifications) {
        level = this.$root.global.qualifications[id];
      }
      return level;
    },
  },

  created() {
    breadcrumbs.dispatch("setBreadcrumbs", breadcrumb);
  },

  validators: {
    "search_data.from_date": function (value = null) {
      return Validator.value(value).required("From date is required");
    },
    "search_data.to_date": function (value = null) {
      return Validator.value(value).required("To date is required");
    },
    "search_data.report_type": function (value = null) {
      return Validator.value(value).required("Report Type is required");
    },
  },
};
</script>