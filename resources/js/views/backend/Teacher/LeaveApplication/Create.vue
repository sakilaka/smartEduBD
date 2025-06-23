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
            <Select
              title="Leave Type"
              field="leave_type_id"
              :req="true"
              :datas="
                $root.global.qualifications ? $root.global.qualifications : []
              "
            />
            <Input
              title="Total Days"
              field="total_leave_days"
              type="number"
              :req="true"
            />
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label
                title="From Date"
                :req="true"
                :condition="validation.hasError('data.from_date')"
                :msg="validation.firstError('data.from_date')"
              />
              <date-picker
                v-model="data.from_date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label
                title="To Date"
                :req="true"
                :condition="validation.hasError('data.to_date')"
                :msg="validation.firstError('data.to_date')"
              />
              <date-picker
                v-model="data.to_date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>

            <!------------ Single Input ------------>
            <Textarea
              title="Description"
              :fixheight="true"
              field="description"
              :rows="5"
              :col="12"
            />
            <File
              title="Attachment (.jpg, jpeg, .png)"
              field="attachment"
              mime="img"
              :req="false"
              fileClassName="file1"
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
import File from "./../../../../components/backend/elements/Form/File";

// define model name
const model = "leaveApplication";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Leave Application",
  icon: "left-arrow-alt",
};

export default {
  components: {
    Textarea: () =>
      import("./../../../../components/backend/elements/Form/Textarea"),
    File,
  },

  data() {
    return {
      model: model,
      submitType: "",
      data: { status: "active", leave_type_id: null },
      images: { attachment: "" },
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
      this.setBreadcrumbs(this.model, page, "Leave Application", addOrBack);
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
    "data.leave_type_id": function (value = null) {
      return Validator.value(value).required("Leave Type is required");
    },
    "data.total_leave_days": function (value = null) {
      return Validator.value(value).required("Total Days is required");
    },
    "data.from_date": function (value = null) {
      return Validator.value(value).required("From Date is required");
    },
    "data.to_date": function (value = null) {
      return Validator.value(value).required("To Date is required");
    },
  },
};
</script>