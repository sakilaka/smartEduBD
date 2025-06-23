<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-5">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <Input title="Session Name" field="name" :req="true" col="12" />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Institution Category"
              field="institution_category_id"
              :req="true"
              :datas="$root.global.institution_categories"
              val="id"
              val_title="name"
              col="12"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <Radio
              title="Current Financial Year"
              field="current"
              statusTitle1="Yes"
              statusTitle2="No"
              value1="1"
              value2="0"
              col="6"
            />
            <Radio
              title="Online Admission"
              field="online_admission"
              statusTitle1="Yes"
              statusTitle2="No"
              value1="1"
              value2="0"
              col="6"
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
const model = "academicSession";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Academic Session",
  icon: "left-arrow-alt",
};

export default {
  components: {
    Radio: () => import("@components/backend/elements/Form/Radio"),
  },

  data() {
    return {
      model: model,
      submitType: "",
      data: {
        status: "active",
        current: 0,
        online_admission: 0,
        institution_category_id: null,
      },
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
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(this.model, page, "Academic Session", addOrBack);
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
    "data.name": function (value = null) {
      return Validator.value(value).required("Session Name is required");
    },
    "data.institution_category_id": function (value = null) {
      return Validator.value(value).required(
        "Institution Category is required"
      );
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
  },
};
</script>