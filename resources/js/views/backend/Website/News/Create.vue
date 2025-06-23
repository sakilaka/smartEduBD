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
            <!-- Single Input -->
            <div class="col-12 col-xl-3">
              <Label
                title="Notice Date"
                :req="false"
                :condition="validation.hasError('data.date')"
                :msg="validation.firstError('data.date')"
              />
              <date-picker
                v-model="data.date"
                valueType="format"
                format="YYYY-MMM-DD"
                :formatter="momentFormat"
                placeholder="Select Date"
              ></date-picker>
            </div>
            <!------------ Single Input ------------>
            <Input
              title="Title"
              field="title"
              type="text"
              :req="true"
              col="4"
            />
            <!------------ Single Input ------------>
            <File
              title="Image"
              field="image"
              mime="img"
              :req="true"
              fileClassName="file1"
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
const model = "news";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { editor, File },

  data() {
    return {
      model: model,
      submitType: "",
      data: { image: "", sorting: 0 },
      images: {},
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
          } else {
            formData.append("description", "");
          }
          if (this.data.slug) {
            this.update(this.model, formData, this.data.slug, "image");
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
        this.get_sorting("Website-News");
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
    "data.date": function (value = null) {
      return Validator.value(value).required("Date is required");
    },
    "data.title": function (value = null) {
      return Validator.value(value).required("Title is required");
    },
    "data.image": function (value = null) {
      return Validator.value(value)
        .required("Image is required")
        .custom(function () {
          if (!value.type) {
            return false;
          }
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
    "data.sorting": function (value = null) {
      return Validator.value(value).required("Sorting is required");
    },
  },
};
</script>