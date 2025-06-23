<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9">
      <div class="row">
        <!-- institution info -->
        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Institution Info</h6>
            </div>
            <div class="card-body p-3">
              <div class="row g-3">
                <Input title="Name" field="name" :req="true" />
                <Input title="Short Name" field="short_name" :req="true" />
                <File
                  title="Logo"
                  field="logo"
                  mime="img"
                  fileClassName="file1"
                  col="6"
                />
                <Input title="Domain" field="domain" type="url" col="6" />
                <!------------ Single Input ------------>
                <div class="col-12">
                  <Label
                    title="Package"
                    :condition="validation.hasError('data.package_id')"
                    :msg="validation.firstError('data.package_id')"
                  />
                  <v-select
                    v-model="data.package_id"
                    label="name"
                    :reduce="(obj) => obj.id"
                    :options="packages"
                    placeholder="--Select Any--"
                    :closeOnSelect="true"
                  ></v-select>
                </div>
                <Textarea
                  title="Address"
                  field="address"
                  req="true"
                  :col="12"
                />
              </div>
            </div>
          </div>

          <!-- Payments Info -->
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Payments Info</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <Input
                  title="SSL - Service Charge (%)"
                  field="ssl_service_charge_percent"
                  type="text"
                  :req="true"
                />
                <Input
                  title="SPAY - Service Charge (%)"
                  field="spay_service_charge_percent"
                  type="text"
                  :req="true"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">IMAGES</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <!------------ Single Input ------------>
                <File
                  title="ID CARD - Front Part"
                  field="idcard_front_part"
                  mime="img"
                  :req="false"
                  fileClassName="idcard_front_part"
                  :col="6"
                />
                <!------------ Single Input ------------>
                <File
                  title="ID CARD - Back Part"
                  field="idcard_back_part"
                  mime="img"
                  :req="false"
                  fileClassName="idcard_back_part"
                  :col="6"
                />
                <!------------ Single Input ------------>
                <File
                  title="Admit Card"
                  field="admit_card_image"
                  mime="img"
                  :req="false"
                  fileClassName="file6"
                  :col="6"
                />
                <!------------ Single Input ------------>
                <File
                  title="Primary Marksheet"
                  field="primary_term_marksheet"
                  mime="img"
                  :req="false"
                  fileClassName="file7"
                  :col="6"
                />
                <!------------ Single Input ------------>
                <File
                  title="Secondary Marksheet"
                  field="secondary_term_marksheet"
                  mime="img"
                  :req="false"
                  fileClassName="file8"
                  :col="6"
                />
                <!------------ Single Input ------------>
                <File
                  title="Sec. Combined Marksheet"
                  field="secondary_term_marksheet_combined"
                  mime="img"
                  :req="false"
                  fileClassName="file9"
                  :col="6"
                />
                <!------------ Single Input ------------>
                <File
                  title="Admin Admit Card"
                  field="admin_admit_card"
                  mime="img"
                  :req="false"
                  fileClassName="file10"
                  :col="6"
                />
                <!------------ Single Input ------------>
                <File
                  title="Seat Card"
                  field="seat_card"
                  mime="img"
                  :req="false"
                  fileClassName="file11"
                  :col="6"
                />
              </div>
            </div>
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
import File from "@components/backend/elements/Form/File";
import Textarea from "@components/backend/elements/Form/Textarea";

// define model name
const model = "institution";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File, Textarea },
  data() {
    return {
      model: model,
      submitType: "",
      data: {
        status: "active",
        package_id: null,
        logo: "",
        idcard_front_part: "",
        idcard_back_part: "",
        admit_card_image: "",
        primary_term_marksheet: "",
        secondary_term_marksheet: "",
        secondary_term_marksheet_combined: "",
        admin_admit_card: "",
        seat_card: "",
      },
      packages: [],
      images: {},
    };
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
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
          var form = document.getElementById("form");
          var formData = new FormData(form);
          formData.append("package_id", this.data.package_id);

          if (this.data.id) {
            this.update(this.model, formData, this.data.id, "image");
          } else {
            this.store(this.model, formData, this.submitType);
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
      this.setBreadcrumbs(this.model, page, null, addOrBack);
    },
  },

  created() {
    this.get_data("package", "packages", { allData: true });
  },

  // ================== validation rule for form ==================
  validators: {
    "data.package_id": function (value = null) {
      return Validator.value(value).required("Package is required");
    },
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.short_name": function (value = null) {
      return Validator.value(value).required("Name is required").maxLength(15);
    },
    "data.address": function (value = null) {
      return Validator.value(value).required("Address is required");
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
  },
};
</script>
