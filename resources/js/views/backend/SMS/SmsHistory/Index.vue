<template>
  <div class="card">
    <div class="card-body min-height">
      <form v-on:submit.prevent="search" class="col-12 row mb-3">
        <!------------ Single Input ------------>
        <SelectSearch
          title="SMS Template"
          field="sms_template_id"
          :datas="extraData.sms_templates"
          val="id"
          val_title="name"
        />
        <div class="col-2 pe-0">
          <date-picker
            v-model="search_data.from_date"
            valueType="format"
            format="YYYY-MMM-DD"
            :formatter="momentFormat"
            placeholder="From Date"
          ></date-picker>
        </div>
        <div class="col-2 pe-0">
          <date-picker
            v-model="search_data.to_date"
            valueType="format"
            format="YYYY-MMM-DD"
            :formatter="momentFormat"
            placeholder="To Date"
          ></date-picker>
        </div>
        <Search :fields_name="{}" :search_field="false" />

        <div class="col-5 text-end">
          <span class="text-success">
            <b>Total Send : {{ Number(total.total_send) }}</b>
          </span>
          &nbsp; | &nbsp;
          <span class="text-danger">
            <b>Total Cost :{{ total.total_cost | currency }} </b>
          </span>
        </div>
      </form>

      <!-- BaseTable -->
      <base-table
        :data="table.datas"
        :columns="table.columns"
        :routes="table.routes"
      >
        <template v-slot:[`total_sms_cost`]="row">
          <td class="text-center">
            {{
              (Number(row.item.sms_cost) * Number(row.item.total_sending_sms))
                | currency
            }}
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
const model = "smsHistory";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "Send SMS",
  icon: "plus-circle",
};

// define table coloumn show in datatable / datalist
const tableColumns = [
  { field: "date", title: "Date", date: true },
  { field: "sms_template", title: "SMS Template", subfield: "name" },
  { field: "sms_cost", title: "SMS Cost", align: "center" },
  { field: "total_sending_sms", title: "Total Sending SMS", align: "center" },
  { field: "total_sms_cost", title: "Total Cost", align: "center" },
];
//json fields for export excel
const json_fields = {
  Date: "date",
  "SMS Template": "sms_template.name",
  "SMS Cost": "sms_cost",
  "Total Sending SMS": "total_sending_sms",
  "Total Cost": {
    callback: (value) => {
      return `${value.total_sending_sms * value.sms_cost}`;
    },
  },
};

let date = new Date().toISOString().slice(0, 10);

export default {
  data() {
    return {
      model: model,
      json_fields: json_fields,
      search_data: {
        pagination: 10,
        sms_template_id: "",
        from_date: date,
        to_date: date,
      },
      table: {
        columns: tableColumns,
        routes: {},
        datas: [],
        meta: [],
        links: [],
      },
      extraData: { sms_templates: [] },
    };
  },

  computed: {
    total() {
      let total = {
        total_send: 0,
        total_cost: 0,
      };

      if (Object.keys(this.table.datas).length > 0) {
        this.table.datas.forEach((el) => {
          total.total_send += parseFloat(el.total_sending_sms);
          total.total_cost +=
            parseFloat(el.total_sending_sms) * parseFloat(el.sms_cost);
        });
      }
      return total;
    },
  },

  methods: {
    destroy(id) {
      this.destroy_data(this.model, id, this.search_data);
    },
    search() {
      this.$route.query.page = "";
      this.get_paginate_data(this.model, this.search_data);
    },
  },
  async created() {
    this.setBreadcrumbs(this.model, "index", "Sms History", addOrBack);
    this.get_paginate_data("smsTemplate", { allData: true }, "sms_templates");
    await this.get_paginate_data(this.model, this.search_data);
  },
};
</script>