<template>
  <div class="col-12" v-if="!$root.spinner">
    <div class="card">
      <div class="card-body" style="background: #efefef">
        <a
          class="btn btn-sm btn-success"
          @click="print('printArea', 'invoice')"
          href="javascript:;"
        >
          <i class="bx bx-printer"></i> PRINT
        </a>
        <div
          v-if="data && Object.keys(data).length > -0"
          class="row d-flex justify-content-center"
        >
          <div class="col-12 col-lg-7">
            <div id="printArea" class="w-100">
              <template v-for="(item, key1) in data">
                <invoice-template
                  :key="key1"
                  :data="item"
                  height="510px"
                ></invoice-template>
                <div :key="'abc' + key1 + 11" class="mt-4"></div>
              </template>
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
      data: [],
    };
  },
  async created() {
    this.$root.spinner = true;
    this.setBreadcrumbs(this.model, "view", "Invoice Print", addOrBack); // Set Breadcrumbs
    const res = await this.callApi(
      "post",
      "get-print-invoice",
      this.$route.query
    ); // get data
    if (res.status == 200) {
      this.data = res.data;
    }
    setTimeout(() => (this.$root.spinner = false), 300);
  },
};
</script>