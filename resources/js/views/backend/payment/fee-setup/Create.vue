<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-12">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectCustom
              v-show="!$root.institution_id"
              title="Institution"
              field="institution_id"
              :req="true"
              :datas="$root.global.institutions"
              col="3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Shift"
              field="shift_id"
              :req="true"
              :datas="$root.global.shifts"
              col="2"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Medium/Version"
              field="medium_id"
              :req="true"
              :datas="$root.global.mediums"
              col="2"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Academic Class"
              field="academic_class_id"
              :req="true"
              :datas="$root.global.classes"
              col="2"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Group"
              field="group_id"
              :req="true"
              :datas="$root.global.groups"
              col="3"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <Textarea
              title="Remarks"
              :fixheight="true"
              field="description"
              :col="12"
            />
          </div>
        </div>
      </div>

      <!-- Fee Setup Details -->
      <div class="card border">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">Fee Setup Details</h6>
            </div>
            <div class="col-6 d-flex justify-content-end">
              <button
                type="button"
                :disabled="$root.submit ? true : false"
                class="btn btn-success btn-sm px-3"
                @click="submit()"
              >
                <span v-if="$root.submit">
                  <span class="spinner-border spinner-border-sm"></span>
                  <span>processing...</span>
                </span>
                <span v-else> <i class="bx bx-save"></i> Save</span>
              </button>
            </div>
          </div>
        </div>

        <div class="card-body p-3">
          <div class="row g-3">
            <label class="col-6 col-xl-2">
              Account Head <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-1">
              Amount <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Gateway Account <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Start Date <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Expired Date <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Additional Date <span class="req">*</span>
            </label>
          </div>
          <div
            v-for="(fees, key) in data.details"
            :key="key"
            class="row g-3 mb-4 pb-2 border-bottom border-success"
          >
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <select
                v-model="fees.account_head_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(head, key) in $root.global.account_heads"
                  :key="key"
                  v-bind:value="head.id"
                >
                  {{ head.name_en }}
                </option>
              </select>

              <select
                v-model="fees.depend_head_id"
                class="form-select form-select-sm mt-2"
              >
                <option :value="null">--Depend On--</option>
                <option
                  v-for="(head, key) in $root.global.account_heads"
                  :key="key"
                  v-bind:value="head.id"
                >
                  {{ head.name_en }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-1">
              <input
                class="form-control form-control-sm"
                v-model="fees.amount"
                type="number"
                placeholder="Amount"
              />
              <!-- Payment Duration -->
              <select
                v-model="fees.payment_duration"
                class="form-select form-select-sm mt-2"
              >
                <option :value="null">--Durations--</option>
                <option
                  v-for="(value, key, index) in $root.global.payment_durations"
                  :key="index"
                  v-bind:value="key"
                >
                  {{ value }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2 position-relative">
              <select
                v-model="fees.payment_gateway_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(account, key) in payment_gateways"
                  :key="key"
                  v-bind:value="account.id"
                >
                  {{ account.title }}
                </option>
              </select>

              <input
                :id="`per${key}`"
                type="checkbox"
                @click="
                  applicable = applicable == `per${key}` ? '' : `per${key}`
                "
                class="mt-3"
                :checked="applicable == `per${key}` ? true : false"
              />
              <label :for="`per${key}`" style="width: auto; cursor: pointer">
                <b>Applicable For</b>
              </label>

              <!-- applicables -->
              <div class="applicable" v-show="applicable == `per${key}`">
                <ul>
                  <li>
                    <input
                      type="checkbox"
                      v-model="fees.online_addmission_fees"
                      value="1"
                      class="mt-3"
                    />
                    <b>Online Adminssion Fee</b>
                  </li>
                  <li>
                    <input
                      type="checkbox"
                      value="1"
                      v-model="fees.service_charge"
                      class="mt-3"
                    />
                    <b>Charge Applicable</b>
                  </li>
                  <li>
                    <input
                      type="checkbox"
                      v-model="fees.school_fees"
                      value="1"
                      class="mt-3"
                    />
                    <b>School/College Fee</b>
                  </li>
                  <li>
                    <input
                      type="checkbox"
                      v-model="fees.migration_fees"
                      value="1"
                      class="mt-3"
                    />
                    <b>Migration Fee</b>
                  </li>
                  <li>
                    <input
                      type="checkbox"
                      v-model="fees.check_registration_no"
                      value="1"
                      class="mt-3"
                    />
                    <b>Check Registration No.</b>
                  </li>
                </ul>
              </div>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <date-picker
                v-model="fees.start_date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <date-picker
                v-model="fees.expire_date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <date-picker
                v-model="fees.additional_date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-6 col-xl-1">
              <i
                v-if="Object.keys(data.details).length > 1"
                @click="data.details.splice(key, 1)"
                class="bx bx-minus btn btn-sm btn-danger"
              >
              </i>
              <i
                v-if="Object.keys(data.details).length == key + 1"
                @click="
                  data.details.push({
                    depend_head_id: null,
                    account_head_id: null,
                    payment_duration: null,
                    payment_gateway_id: null,
                  })
                "
                class="bx bx-plus btn btn-sm btn-primary ml-2"
              >
              </i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
// define model name
const model = "feeSetup";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Fee Setup",
  icon: "left-arrow-alt",
};

export default {
  components: {
    Textarea: () => import("@components/backend/elements/Form/Textarea"),
  },

  data() {
    return {
      model: model,
      submitType: "",
      setup_exist: false,
      applicable: "",
      data: {
        id: null,
        institution_id: null,
        shift_id: null,
        medium_id: null,
        academic_class_id: null,
        group_id: null,
        details: [
          {
            depend_head_id: null,
            account_head_id: null,
            payment_duration: null,
            payment_gateway_id: null,
          },
        ],
      },
      gateways: [],
    };
  },

  computed: {
    payment_gateways: function () {
      let institution_id = this.data.institution_id;
      let class_id = this.data.academic_class_id;
      let gateways = [];
      if (class_id && institution_id && Object.keys(this.gateways.length > 0)) {
        gateways = this.gateways.filter(
          (e) =>
            e.academic_class_id == class_id &&
            e.institution_id == institution_id
        );
      }
      return gateways;
    },
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },

    "data.shift_id": function (id) {
      if (id) this.checkExist();
    },
    "data.group_id": function (id) {
      if (id) this.checkExist();
    },
    "data.medium_id": function (id) {
      if (id) this.checkExist();
    },
    "data.academic_class_id": function (id) {
      if (id) this.checkExist();
    },
  },

  methods: {
    submit: function () {
      if (this.setup_exist) {
        this.notify("Sorry!! Already Setup This Class", "error");
        return false;
      }
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data, this.submitType);
          }
        }
      });
    },

    // Check Exist  Fee Setup
    checkExist() {
      if (
        !this.data.institution_id ||
        !this.data.medium_id ||
        !this.data.academic_class_id ||
        !this.data.group_id ||
        !this.data.shift_id
      ) {
        return false;
      }

      let data = {
        institution_id: this.data.institution_id,
        shift_id: this.data.shift_id,
        medium_id: this.data.medium_id,
        group_id: this.data.group_id,
        academic_class_id: this.data.academic_class_id,
        id: this.data.id,
      };
      axios.get("exist-feeSetup", { params: data }).then((res) => {
        if (res.data) {
          this.notify("Sorry!! Already Setup This Class", "error");
        }
        this.setup_exist = res.data;
      });
    },

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(this.model, page, "Fee Setup", addOrBack);

      this.get_data("get-paymentGateway", "gateways", { allData: true });
    },
  },

  mounted() {
    if (this.$root.institution_id) {
      this.data.institution_id = this.$root.institution_id;
    }
  },

  // ================== validation rule for form ==================
  validators: {
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "data.medium_id": function (value = null) {
      return Validator.value(value).required("Medium is required");
    },
    "data.group_id": function (value = null) {
      return Validator.value(value).required("Group is required");
    },
    "data.shift_id": function (value = null) {
      return Validator.value(value).required("Shift is required");
    },
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
  },
};
</script>

<style scoped>
.applicable {
  position: absolute;
  z-index: 9;
  background: #fff;
  padding: 0px 10px;
  box-shadow: 1px 3px 8px #ccc;
  border: 1px solid #ccc;
  border-radius: 5px;
}
.applicable ul {
  padding: 0;
}
.applicable ul li {
  list-style: none;
}
</style>