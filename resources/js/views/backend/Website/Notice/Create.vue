<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-6">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectCustom
              v-show="!$root.institution_id"
              title="Institution"
              field="institution_id"
              :req="true"
              :datas="$root.global.institutions"
              val="id"
              val_title="name"
              :col="12"
            />
            <!-- Single Input -->
            <div class="col-12 col-xl-3">
              <Label
                title="Notice Date"
                :req="true"
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
            <Input title="Title" field="title" :req="true" :col="7" />
            <!------------ Single Input ------------>
            <Select
              title="File Type"
              field="file_type"
              :req="true"
              :datas="file_types"
              :col="2"
            />
            <!------------ Single Input ------------>
            <Select
              title="Notice Type"
              field="type"
              :req="true"
              :datas="
                $root.global.notice_types ? $root.global.notice_types : []
              "
              :col="4"
            />
            <!------------ Single Input ------------>
            <File
              v-if="data.file_type"
              :title="data.file_type"
              field="image"
              :mime="data.file_type == 'Image' ? 'img' : 'pdf'"
              :req="false"
              fileClassName="file1"
              col="8"
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

    <div class="col-xl-3">
      <!-- Classes -->
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            Specific Class &nbsp;
            <template v-if="data.type == 'student'">
              <input
                type="checkbox"
                v-model="data.all_class"
                name="all_class"
                value="1"
              />
              <small>All</small>
            </template>
          </h6>
        </div>

        <div
          v-if="data.type == 'public'"
          class="text-center my-5 py-5 category-map"
        >
          This is a public notice applicable to all
        </div>

        <!-- Class  -->
        <div
          v-if="data.type == 'student'"
          class="card-body"
          style="height: 465px; overflow: scroll"
        >
          <template v-if="$root.global.classes">
            <div
              v-for="(cls, index) in $root.global.classes"
              :key="index"
              class="col-12 border-bottom py-1 mt-2"
            >
              <div class="form-check">
                <input
                  v-model="data.assignables"
                  class="form-check-input"
                  type="checkbox"
                  :value="{
                    academic_class_id: cls.id,
                  }"
                  :disabled="data.all_class ? true : false"
                />
                {{ cls.name }}
              </div>
            </div>
          </template>
          <div v-else class="text-center my-5">processing...</div>
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
import editor from "@components/backend/elements/CKEditor";
import File from "@components/backend/elements/Form/File";

// define model name
const model = "notice";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: {
    File,
    editor,
  },
  data() {
    return {
      model: model,
      submitType: "",
      file_types: {
        Image: "Image",
        PDF: "PDF",
      },
      data: {
        image: null,
        institution_id: null,
        type: "public",
        file_type: "Image",
        date: new Date().toISOString().slice(0, 10),
        assignables: [],
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
          if (this.data.description) {
            formData.append("description", this.data.description);
          } else {
            formData.append("description", "");
          }

          if (this.data.assignables) {
            formData.append(
              "assignables",
              JSON.stringify(this.data.assignables)
            );
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
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
    },
  },

  mounted() {
    if (this.$root.institution_id) {
      this.data.institution_id = this.$root.institution_id;
    }
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "data.title": function (value = null) {
      return Validator.value(value).required("Title is required");
    },
    "data.date": function (value = null) {
      return Validator.value(value).required("Date is required");
    },
    "data.type": function (value = null) {
      return Validator.value(value).required("Notice Type is required");
    },
    "data.file_type": function (value = null) {
      return Validator.value(value).required("File Type is required");
    },
    "data.image": function (value = null) {
      return Validator.value(value).custom(function () {
        if (value && !value.type) {
          return false;
        }
        if (!Validator.isEmpty(value)) {
          var type = value.type;
          if (
            type == "image/jpeg" ||
            type == "image/jpg" ||
            type == "image/png" ||
            type == "application/pdf"
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