<template>
  <div v-if="!$root.spinner" class="col-xl-5 mx-auto">
    <div class="card">
      <div class="card-body">
        <div class="border border-success p-4 rounded">
          <!------------ Single Input ------------>
          <div class="row mb-3">
            <div class="col-5">
              <Label title="Invoice Date :" :req="true" />
            </div>
            <div class="col-7 ps-0">
              <input
                v-model="invoice_date"
                readonly
                type="text"
                class="form-control form-control-sm"
              />
            </div>
          </div>
          <div class="row">
            <div class="col-5">
              <Label
                title="Recharge Amount :"
                :req="true"
                :condition="validation.hasError('data.amount')"
                :msg="validation.firstError('data.amount')"
              />
            </div>
            <div class="col-7 ps-0">
              <input
                type="number"
                v-model="data.amount"
                class="form-control form-control-sm"
                placeholder="Recharge Amount"
                :class="reqClass('amount')"
              />
            </div>
          </div>

          <!-------------- button -------------->
          <div class="col-xl-12 text-center mt-5">
            <button
              @click="submit"
              type="submit"
              :disabled="
                validation.countErrors() > 0 || $root.submit ? true : false
              "
              class="btn btn-sm btn-success"
            >
              <span v-if="$root.submit">
                <span class="spinner-border spinner-border-sm"></span>
                processing..
              </span>
              <span v-else>PAY NOW</span>
            </button>
          </div>
          <!-------------- button -------------->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// define model name
const model = "smsTransaction";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "SMS Transaction",
  icon: "left-arrow-alt",
};

let date = new Date().toISOString().slice(0, 10);

export default {
  data() {
    return {
      model: model,
      invoice_date: date,
      data: {},
    };
  },
  methods: {
    submit: function () {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          this.$root.submit = true;
          axios
            .post("/smsTransaction", this.data)
            .then((res) => {
              if (res.status == 200) {
                if (res.data.payment_url.url) {
                  this.notify(res.data.message, "success");
                  window.location.href = res.data.payment_url.url;
                } else {
                  this.notify(
                    "Sorry!! Payment cannot proceed at this time, please try again",
                    "error"
                  );
                }
              } else {
                this.notify(res.data.error, "error");
              }
            })
            .catch((err) => this.notify("Something went wrong", "error"))
            .then((alw) => setTimeout(() => (this.$root.submit = false), 200));
        }
      });
    },

    // Async Data
    asyncData() {
      this.setBreadcrumbs(this.model, "create", "SMS Transaction", addOrBack);
    },

    reqClass(field) {
      if (this.validation.hasError("data." + field)) {
        return "form-invalid";
      } else if (this.data[field]) {
        return "form-valid";
      }
    },
  },
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.amount": function (value = null) {
      if (value == 0) {
        return Validator.value(null).required("Amount is required").digit();
      } else if (value < 10) {
        return Validator.value(null).required("Minimum amount is 100").digit();
      }
      return Validator.value(value).required("Amount is required").digit();
    },
  },
};
</script>