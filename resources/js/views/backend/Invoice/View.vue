<template>
  <div class="col-12" v-if="!$root.spinner">
    <div class="card">
      <div class="card-body" style="background: #efefef">
        <a
          class="btn btn-sm btn-success float-end"
          @click="print('printArea', 'invoice')"
          href="javascript:;"
        >
          <i class="bx bx-printer"></i> PRINT
        </a>
        <div class="row d-flex justify-content-center">
          <div class="col-12 col-lg-7">
            <div id="printArea">
              <invoice-template
                v-for="i in 2"
                :key="i"
                :data="data"
                :height="data?.head?.type == 'tuition' ? '1024px' : '515px'"
                :month="data?.head?.type == 'tuition' ? true : false"
              >
                <template
                  v-slot:[`account-heads`]
                  v-if="data?.head?.type == 'tuition'"
                >
                  <tbody v-if="data.details">
                    <tr v-for="(fees, key) in data.details" :key="key">
                      <th>{{ key + 1 }}</th>
                      <td>{{ fees.invoice_date | formatDate("MMMM") }}</td>
                      <td>{{ fees.head ? fees.head.name_en : "" }}</td>
                      <td>{{ fees.amount | currency }}</td>
                    </tr>
                    <tr>
                      <th colspan="3" class="text-end">Sub-Total =</th>
                      <th>
                        {{ totalAmount | currency }}
                      </th>
                    </tr>
                    <tr>
                      <th colspan="3" class="text-end">Discount =</th>
                      <th>
                        {{ data.discount_amount | currency }}
                      </th>
                    </tr>
                    <tr>
                      <th colspan="3" class="text-end">Total =</th>
                      <th>
                        {{ data.amount | currency }}
                      </th>
                    </tr>
                  </tbody>
                </template>
              </invoice-template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import InvoiceTemplate from "./InvoiceTemplate.vue";

// define model name
const model = "invoice";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { "invoice-template": InvoiceTemplate },

  data() {
    return {
      model: model,
      data: {},
    };
  },

  computed: {
    totalAmount() {
      if (this.data) {
        return Number(this.data.amount) + Number(this.data.discount_amount);
      }
    },
  },

  async created() {
    this.setBreadcrumbs(this.model, "view", null, addOrBack); // Set Breadcrumbs
    this.get_data(`${this.model}/${this.$route.params.id}`); // get data
  },
};
</script>