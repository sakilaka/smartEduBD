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
            <Select
              title="Type"
              field="type"
              :req="false"
              :datas="albumTypes"
              col="2"
            />
            <!------------ Single Input ------------>
            <Input title="Name" field="name" type="text" :req="true" col="5" />
            <!------------ Single Input ------------>
            <File
              title="Image"
              field="image"
              mime="img"
              fileClassName="file1"
              :crop="true"
              col="3"
            />
            <!------------ Single Input ------------>
            <Input
              title="Sorting"
              field="sorting"
              type="number"
              :req="true"
              col="2"
            />
            <!-- FOR CROP IMAGE -->
            <Crop
              field="image"
              :image="images.image"
              :width="250"
              :height="250"
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
import File from "@components/backend/elements/Form/File";
import Crop from "@components/backend/elements/Form/FileCrop";

// define model name
const model = "album";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File, Crop },

  data() {
    return {
      model: model,
      submitType: "",
      exist: false,
      albumTypes: {
        Photos: "Photos",
        Videos: "Videos",
      },
      data: { type: "Photos", image: "", sorting: 0 },
      images: { image: "" },
      errors: {},
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
          if (this.data.slug) {
            this.update(this.model, this.data, this.data.slug);
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
      } else {
        this.get_sorting("Website-Gallery-Album");
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).required("Sorting is required");
    },
    "data.image": function (value = null) {
      if (!value.type) {
        return false;
      }
      return Validator.value(value)
        .required("Image is required")
        .custom(function () {
          if (!Validator.isEmpty(value)) {
            var type = value.type;
            if (
              type == "image/jpeg" ||
              type == "image/jpg" ||
              type == "image/png"
            ) {
            } else {
              return "Image must be of type .jpg .jpeg or .png";
            }
          }
        });
    },
  },
};
</script>