<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9 mx-auto">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <Input title="Title" field="title" :req="true" />
            <!------------ Single Input ------------>
            <File
              title="Slider Image"
              field="slider"
              mime="img"
              :req="true"
              fileClassName="file1"
            />
            <!------------ Single Input ------------>
            <Input
              title="Sorting"
              field="sorting"
              type="number"
              :req="true"
              col="2"
            />
            <!------------ Single Input ------------>
            <Input title="URL" field="url" type="url" col="4" />

            <!------------ Single Input ------------>
            <div class="form-row col-12">
              <label class="col-12">Description</label>
              <div class="col-12">
                <editor :editorModel="'description'" />
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
import editor from "@components/backend/elements/CKEditor";

// define model name
const model = "slider";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File, editor },

  data() {
    return {
      model: model,
      submitType: "",
      images: { slider: "" },
      data: { slider: "", url: null, sorting: "" },
      extraData: { offers: [] },
      errors: {},
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
          if (this.data.description) {
            formData.append("description", this.data.description);
          }

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
      } else {
        this.get_sorting("Website-Gallery-Slider");
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
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
    "data.slider": function (value = null) {
      return Validator.value(value).required("Slider is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).required("Sorting is required");
    },
  },
};
</script>