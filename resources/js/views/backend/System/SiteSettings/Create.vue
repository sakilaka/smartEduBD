<template>
  <form
    v-on:submit.prevent="submit"
    id="form"
    class="row"
    v-if="!$root.spinner"
  >
    <div class="col-xl-9">
      <div class="row">
        <!-- systems info -->
        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Systems Info</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <!------------ Single Input ------------>
                <Input title="Title" field="title" :req="true" />
                <!------------ Single Input ------------>
                <Input title="Short Title" field="short_title" :req="true" />
                <!------------ Single Input ------------>
                <Input
                  title="Contact Email"
                  field="contact_email"
                  type="email"
                  :req="false"
                />
                <!------------ Single Input ------------>
                <Input
                  title="Feedback Email"
                  field="feedback_email"
                  type="email"
                  :req="false"
                />
                <!------------ Single Input ------------>
                <Input
                  title="Mobile One"
                  field="mobile1"
                  type="number"
                  :req="false"
                />
                <!------------ Single Input ------------>
                <Input
                  title="Mobile Two"
                  field="mobile2"
                  type="number"
                  :req="false"
                />
                <!------------ Single Input ------------>
                <Textarea title="Address" field="address" :req="false" />
                <Input title="Web" field="web" type="url" :req="false" />
                <Input
                  title="Developed By"
                  field="developed_by"
                  :req="false"
                  col="6"
                />
                <Input
                  title="Developed By URL"
                  field="developed_by_url"
                  type="url"
                  :req="false"
                  col="6"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- right side -->
        <div class="col-6">
          <!-- Images -->
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Images</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <!------------ Single Input ------------>
                <File
                  title="Logo"
                  field="logo"
                  mime="img"
                  :req="true"
                  fileClassName="file1"
                  :col="4"
                />
                <!------------ Single Input ------------>
                <File
                  title="Logo Small"
                  field="logo_small"
                  mime="img"
                  :req="false"
                  fileClassName="file2"
                  :col="4"
                />
                <!------------ Single Input ------------>
                <File
                  title="Favicon Logo"
                  field="favicon"
                  mime="img"
                  :req="false"
                  fileClassName="file3"
                  :col="4"
                />
              </div>
            </div>
          </div>

          <!-- Social Media -->
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Social Media</h6>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <Input title="Facebook" field="fb" type="url" :req="false" />
                <Input title="Twitter" field="tw" type="url" :req="false" />
                <Input title="Linkedin" field="ln" type="url" :req="false" />
                <Input title="Youtube" field="yt" type="url" :req="false" />
                <Textarea title="Map" field="map" :req="false" col="12" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus :save_and_edit="false" />
    </div>
  </form>
</template>

<script>
import File from "@components/backend/elements/Form/File.vue";
import Textarea from "@components/backend/elements/Form/Textarea.vue";
import Radio from "@components/backend/elements/Form/Radio";

// define model name
const model = "siteSetting";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File, Textarea, Radio },

  data() {
    return {
      model: model,
      submitType: "",
      data: {
        logo: "",
        logo_small: "",
        favicon: "",
        meta: {},
      },
      images: {},
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
          var form = document.getElementById("form");
          var formData = new FormData(form);
          this.store(this.model, formData, this.submitType);
        }
      });
    },

    // Async Data
    asyncData() {
      this.get_data("siteSetting/1");
      this.setBreadcrumbs(this.model, "create", "Site Setting", addOrBack);
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
    "data.title": function (value = null) {
      return Validator.value(value).required("Title is required");
    },
    "data.short_title": function (value = null) {
      return Validator.value(value).required("Short Title is required");
    },
    "data.logo": function (value = null) {
      return Validator.value(value).required("Logo is required");
    },
  },
};
</script>