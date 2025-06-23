<template>
  <div class="col-xl-6 mx-auto">
    <div class="card">
      <div class="card-body">
        <div class="border border-success p-4 rounded">
          <!------------ Single Input ------------>
          <div class="row">
            <Input
              title="Software ID"
              field="software_id"
              :req="true"
              :col="10"
              class="mb-3 pe-0"
            />
            <div class="col-1 pt-1 ps-0">
              <button
                @click="searchStudent()"
                class="btn btn-sm btn-success mt-3"
              >
                <span
                  v-if="$root.spinner"
                  style="height: 1rem; width: 1rem; font-size: 11px"
                  class="spinner-border text-white"
                ></span>

                <i v-else class="bx bx-search mx-0 fw-bold"></i>
              </button>
            </div>

            <div class="col-6">
              <div class="col-12 mb-3">
                <label> Campus </label>
                <input
                  type="text"
                  v-model="student_info.campus.name"
                  class="form-control form-control-sm"
                  placeholder="Campus"
                  readonly
                />
              </div>
              <div class="col-12 mb-3">
                <label> Shift </label>
                <input
                  type="text"
                  v-model="student_info.shift.name"
                  class="form-control form-control-sm"
                  placeholder="Shift"
                  readonly
                />
              </div>
              <div class="col-12 mb-3">
                <label> Medium/Version </label>
                <input
                  type="text"
                  v-model="student_info.medium.name"
                  class="form-control form-control-sm"
                  placeholder="Medium/Version"
                  readonly
                />
              </div>
              <div class="col-12 mb-3">
                <label> Academic Class </label>
                <input
                  type="text"
                  v-model="student_info.academic_class.name"
                  class="form-control form-control-sm"
                  placeholder="Academic Class"
                  readonly
                />
              </div>
              <div class="col-12 mb-3">
                <label> Group </label>
                <input
                  type="text"
                  v-model="student_info.group.name"
                  class="form-control form-control-sm"
                  placeholder="Group"
                  readonly
                />
              </div>
              <div class="col-12 mb-3">
                <label> Section </label>
                <input
                  type="text"
                  v-model="student_info.section.name"
                  class="form-control form-control-sm"
                  placeholder="Section"
                  readonly
                />
              </div>
            </div>
            <div class="col-6">
              <div class="col-12 mb-3">
                <label> Student Name </label>
                <input
                  type="text"
                  v-model="student_info.name_en"
                  class="form-control form-control-sm"
                  placeholder="Student Name"
                  readonly
                />
              </div>
              <div class="col-12 mb-3">
                <label> Fathers Name </label>
                <input
                  type="text"
                  v-model="student_info.profile.fathers_name_en"
                  class="form-control form-control-sm"
                  placeholder="Fathers Name"
                  readonly
                />
              </div>
              <div class="col-12 mb-3">
                <label> Mothers Name </label>
                <input
                  type="text"
                  v-model="student_info.profile.mothers_name_en"
                  class="form-control form-control-sm"
                  placeholder="Mothers Name"
                  readonly
                />
              </div>
              <!------------ Single Input ------------>
              <div class="col-12 mb-3">
                <Label
                  title="Payment Purpose"
                  :req="true"
                  :condition="validation.hasError('data.status')"
                  :msg="validation.firstError('data.status')"
                />
                <select
                  v-model="data.account_head_id"
                  class="form-select form-select-sm"
                  :class="{
                    'form-invalid': validation.hasError('data.account_head_id'),
                  }"
                >
                  <option :value="null">--Select Any--</option>
                  <template v-for="(item, index) in fees_lists">
                    <option
                      v-if="item.account_head_type == 'school'"
                      :key="index"
                      v-bind:value="item.account_head_id"
                    >
                      {{ item.account_head_name }}
                    </option>
                  </template>
                </select>
              </div>

              <Input
                title="Fees Amount"
                field="fees_amount"
                :req="true"
                :col="12"
                class="mb-3"
                :disable="data.fees_amount == 0 ? false : true"
              />
              <Input
                title="Discount Amount"
                field="discount_amount"
                :req="true"
                :col="12"
                class="mb-3"
              />
              <Input
                title="Payable Amount"
                field="amount"
                :req="true"
                :col="12"
                class="mb-3"
                disable="true"
              />

              <!-- button -->
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
                  <span v-else>Pay Now</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// set breadcrumb
const breadcrumb = [
  { route: "student.index", title: "Student" },
  { route: "student.index", title: "Student Payment" },
];

export default {
  data() {
    return {
      customBreadcrumb: { breadcrumb: breadcrumb, addOrBack: {} },

      student_info: {
        profile: {},
        section: {},
        shift: {},
        campus: {},
        medium: {},
        group: {},
        academic_class: {},
      },
      discounts: [],
      fees_lists: [],
      data: {
        software_id: null,
        student_id: null,
        account_head_id: null,
        fees_amount: null,
        discount_amount: null,
        amount: null,
      },
    };
  },

  methods: {
    submit() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          console.log(this.validation.allErrors());
          this.notify(error, "validate");
          return false;
        }

        if (this.$root.submit) {
          return false;
        }

        // If there is no error
        if (res) {
          this.$root.submit = true;
          axios
            .post("/studentPayment/payment", this.data)
            .then((res) => {
              if (res.status == 200) {
                if (res.data?.data?.checkout_url) {
                  this.notify(res.data.message, "success");
                  window.location.href = res.data.data.checkout_url;
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
            .catch((error) => (this.$root.submit = false))
            .finally((alw) => (this.$root.submit = false));
        }
      });
    },

    calculateAmount() {
      let feesAmount = this.data.fees_amount ? this.data.fees_amount : 0;
      let discount = this.data.discount_amount ? this.data.discount_amount : 0;
      this.data.amount = parseFloat(feesAmount) - parseFloat(discount);
    },

    searchStudent() {
      if (this.$root.spinner) return false;

      this.fees_lists = [];
      this.student_info = {
        profile: {},
        section: {},
        shift: {},
        campus: {},
        medium: {},
        group: {},
        academic_class: {},
      };
      this.data.student_id = null;
      this.data.account_head_id = null;
      this.data.fees_amount = null;
      this.data.discount_amount = null;
      this.data.amount = null;

      this.$root.spinner = true;
      axios
        .get("studentPayment", {
          params: { software_id: this.data.software_id },
        })
        .then((res) => {
          if (res.status == 200) {
            this.student_info = res.data.student_info;
            this.fees_lists = res.data.fees_lists;
            this.discounts = res.data.discounts;
            this.data.student_id = res.data.student_info.id;
          } else {
            this.notify(res.data.error, "error");
          }
        })
        .finally((res) => (this.$root.spinner = false));
    },
  },

  watch: {
    "data.account_head_id": function (id) {
      if (id) {
        let item = this.fees_lists.find((e) => e.account_head_id == id);
        let discount = this.discounts.find((e) => e.account_head_id == id);
        this.data.fees_amount = item?.amount ?? 0;
        this.data.discount_amount = discount?.amount ?? 0;

        this.data.amount =
          parseFloat(this.data.fees_amount) -
          parseFloat(this.data.discount_amount);
      }
    },
    "data.fees_amount": function (val) {
      this.calculateAmount();
    },
    "data.discount_amount": function (val) {
      this.calculateAmount();
    },
  },

  created() {
    breadcrumbs.dispatch("setBreadcrumbs", this.customBreadcrumb);
  },

  validators: {
    "data.software_id": function (value = null) {
      return Validator.value(value).required("Software is required");
    },
    "data.student_id": function (value = null) {
      return Validator.value(value).required("Student is required");
    },
    "data.account_head_id": function (value = null) {
      return Validator.value(value).required("Payment Type is required");
    },
    "data.amount": function (value = null) {
      if (value == 0) {
        return Validator.value(null).required("Amount is required").digit();
      } else if (value < 10) {
        return Validator.value(null).required("Minimum amount is 10").digit();
      }
      return Validator.value(value).required("Amount is required").digit();
    },
  },
};
</script>