<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <Input title="Name" field="name" type="text" :req="true" />
            <Input title="Father Name" field="father_name" type="text" :req="true" />
            <Input title="Mother Name" field="mother_name" type="text" :req="true" />
            <Input title="Mobile" field="mobile" type="number" :req="false" />
            <Input title="Email" field="email" type="email" :req="true" />

            <!------------ Single Input ------------>
            <Input
              title="Password"
              field="password"
              type="password"
              :req="true"
            />
            <!------------ Single Input ------------>
            <!-- <div class="col-6">
              <Label
                title="Department"
                :condition="validation.hasError('data.department_id')"
                :msg="validation.firstError('data.department_id')"
              />
              <v-select
                v-if="$root.checkPermission('department.index')"
                v-model="data.department_id"
                label="name"
                :reduce="(obj) => obj.id"
                :options="
                  $root.global.departments ? $root.global.departments : []
                "
                placeholder="--Select Any--"
                :closeOnSelect="true"
              ></v-select>
            </div> -->
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label
                title="Designation"
                :condition="validation.hasError('data.teacher.designation_id')"
                :msg="validation.firstError('data.teacher.designation_id')"
              />
              <v-select
                v-model="data.teacher.designation_id"
                label="name"
                :reduce="(obj) => obj.id"
                :options="extraData.designations"
                placeholder="--Select Any--"
                :closeOnSelect="true"
              ></v-select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label title="Index/ID Number" />
              <input
                class="form-control form-control-sm"
                v-model="data.teacher.index_number"
                name="index_number"
                placeholder="Index/ID Number"
              />
            </div>
            <!------------ Single Input ------------>
            <!-- <div class="col-6">
              <Label title="BCS Batch" />
              <input
                class="form-control form-control-sm"
                v-model="data.teacher.bcs_batch"
                name="bcs_batch"
                placeholder="BCS Batch"
              />
            </div> -->
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label title="Date of Birth" />
              <date-picker
                v-model="data.teacher.date_of_birth"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label title="Present Address" />
              <textarea
                class="form-control form-control-sm"
                v-model="data.teacher.present_address"
                name="present_address"
                placeholder="Present Address"
              ></textarea>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label title="Permanent Address" />
              <textarea
                class="form-control form-control-sm"
                v-model="data.teacher.permanent_address"
                name="permanent_address"
                placeholder="Permanent Address"
              ></textarea>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label title="Joining Date As Lecturer" />
              <date-picker
                v-model="data.teacher.joining_date_lecturer"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label title="Joining Date As Present Designation" />
              <date-picker
                v-model="data.teacher.joining_date_present_designation"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label title="Joining Date As Present Work Station" />
              <date-picker
                v-model="data.teacher.joining_date_present_work_station"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>

            <Radio
              title="Type"
              field="type"
              statusTitle1="Teacher"
              statusTitle2="Staff"
              value1="Teacher"
              value2="Staff"
            />
          </div>
        </div>
      </div>

      <!-- Subjects Assign -->
      <!-- <div v-if="data.type == 'Teacher'" class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Subject Assign</h6>
        </div>
        <div class="card-body p-3">
          <SubjectAssign />
        </div>
      </div> -->
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus :save_and_edit="false" />
    </div>
  </form>
</template>

<script>
import SubjectAssign from "./SubjectAssign.vue";

// define model name
const model = "teacher";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: {
    Radio: () => import("./../../../../components/backend/elements/Form/Radio"),
    SubjectAssign,
  },

  data() {
    return {
      model: model,
      submitType: "",
      data: {
        role_id: 9,
        // department_id: null,
        teacher: { designation_id: null },
        status: "active",
        // subject_assigns: [
        //   {
        //     department_id: null,
        //     academic_qualification_id: null,
        //     academic_class_id: null,
        //     subject_id: null,
        //     status: "active",
        //   },
        // ],
      },
      extraData: {
        designations: [],
        subjects: [],
      },
    };
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
          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data, this.submitType);
          }
        }
      });
    },

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(this.model, this.$route.params.id, "data");
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
    },
  },

  mounted() {
    this.get_paginate_data(
      "get-designation",
      { allData: true },
      "designations"
    );
    this.get_paginate_data("subject", { allData: true }, "subjects");
    console.log(this.extraData.designations);
    
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.type": function (value = null) {
      return Validator.value(value).required("Type is required");
    },
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.email": function (value = null) {
      return Validator.value(value).required("Email is required").email();
    },
    "data.password": function (value = null) {
      if (!this.$route.params.id) {
        return Validator.value(value)
          .required("Password is required")
          .minLength(6);
      }
      return Validator.value(value);
    },
    "data.mobile": function (value = null) {
      return Validator.value(value)
        .digit()
        .regex("01+[0-9+-]*$", "Must start with 01.")
        .minLength(11)
        .maxLength(11);
    },
  },
};
</script>