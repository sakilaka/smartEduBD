<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-4">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Academic Info</h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12 text-center">
              <button
                type="button"
                :disabled="$root.tableSpinner ? true : false"
                @click="getStudents()"
                class="btn btn-sm btn-success my-3"
              >
                <span v-if="$root.tableSpinner">
                  <span class="spinner-border spinner-border-sm"></span>
                </span>
                <span v-else>
                  <i class="bx bx-search mx-0 fw-bold"></i>
                  Search Students
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-5">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Payment Info</h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <div class="col-12">
              <Label
                title="Student"
                :req="true"
                :condition="validation.hasError('data.student_id')"
                :msg="validation.firstError('data.student_id')"
              />
              <v-select
                v-model="data.student_id"
                label="name"
                :reduce="(obj) => obj.id"
                :options="student_lists"
                placeholder="--Select Any--"
                :closeOnSelect="true"
                :class="
                  validation.hasError('data.student_id') ? 'required' : ''
                "
              ></v-select>
            </div>

            <!------------ Single Input ------------>
            <SelectCustom
              title="Payment Type"
              field="account_head_id"
              :req="true"
              :datas="fees_lists"
              val="id"
              val_title="name"
              col="12"
            />
            <!------------ Single Input ------------>
            <Input
              title="Amount"
              field="amount"
              type="number"
              :req="true"
              :col="12"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus />
    </div>
  </form>
</template>

<script>
// define model name
const model = "invoice";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      data: {
        academic_class_id: null,
        account_head_id: null,
        student_id: null,
        payment_gateway_id: null,
      },
      students: [],
      fees_lists: [],
    };
  },

  computed: {
    student_lists() {
      let student = [];
      if (this.students.length > 0) {
        this.students.forEach((el) => {
          let college_roll = el.college_roll ? `(${el.college_roll}) -` : "";
          student.push({
            id: el.id,
            name: `${college_roll} ${el.name} - (${el.mobile})`,
          });
        });
      }
      return student;
    },
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
    "data.account_head_id": function (id) {
      if (id && this.fees_lists.length > 0) {
        let res = this.fees_lists.find((el) => el.id == id);
        this.data.payment_gateway_id = res ? res.payment_gateway_id : "";
      }
    },
  },

  methods: {
    submit: function () {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          console.log(this.validation.allErrors());
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          let data = { ...this.data };
          this.store(this.model, data, "no");
          this.data = {
            academic_class_id: null,
            academic_session_id: null,
            academic_qualification_id: null,
            account_head_id: null,
            student_id: null,
          };
        }
      });
    },

    getStudents() {
      let params = {
        academic_class_id: this.data.academic_class_id,
        allData: true,
      };

      if (
        !params.academic_session_id &&
        !params.academic_class_id &&
        !params.academic_qualification_id
      ) {
        this.notify("Please select search criteria", "error");
        return false;
      }

      this.$root.tableSpinner = true;
      axios
        .get("/student", { params: params })
        .then((res) => (this.students = res.data))
        .finally((alw) => (this.$root.tableSpinner = false));

      // fees lists
      axios
        .get("/fees-lists", { params: params })
        .then((res) => (this.fees_lists = res.data));
    },

    // Async Data
    asyncData() {
      this.setBreadcrumbs(this.model, "create", null, addOrBack);
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.academic_session_id": function (value = null) {
      return Validator.value(value).required("Session is required");
    },
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "data.academic_qualification_id": function (value = null) {
      return Validator.value(value).required("Academic Level is required");
    },

    "data.amount": function (value = null) {
      return Validator.value(value).required("Amount is required").digit();
    },
    "data.account_head_id": function (value = null) {
      return Validator.value(value).required("Payment Type is required");
    },
    "data.student_id": function (value = null) {
      return Validator.value(value).required("Student is required");
    },
    "data.payment_gateway_id": function (value = null) {
      return Validator.value(value).required("Payment gateway is required");
    },
  },
};
</script>