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
              v-if="albums"
              title="Album"
              field="album_id"
              :req="true"
              :datas="albums"
            />
            <!------------ Single Input ------------>
            <div class="col-4">
              <Label
                title="Images"
                :req="false"
                :condition="validation.hasError('data.album_id')"
                :msg="validation.firstError('data.album_id')"
              />
              <b-form-file
                accept="image/*"
                id="file-small-file"
                size="sm"
                multiple
                name="files[]"
                v-on:change="previewImages()"
                drop-placeholder="Drop file here"
              ></b-form-file>
            </div>
            <div class="w-100"></div>

            <template v-if="Object.keys(data.uploaded_images).length > 0">
              <div
                v-for="(img, key) in data.uploaded_images"
                :key="key"
                class="col-3"
              >
                <div
                  style="
                    box-shadow: 0px 5px 30px 0px rgba(0, 0, 0, 0.1);
                    border: 1px solid #ddd;
                  "
                  class="item-blcok mb-2 p-3"
                >
                  <div class="img d-flex justify-content-between">
                    <img :src="img.image" alt="" style="height: 50px" />
                    <input
                      v-model="img.sorting"
                      style="width: 80px"
                      type="number"
                      class="text-center form-control form-control-sm rounded-0 mt-2"
                      :name="`uploaded_images[${key}][sorting]`"
                      placeholder="Sorting"
                    />
                    <i
                      class="bx bx-x text-danger"
                      style="cursor: pointer"
                      @click="data.uploaded_images.splice(key, 1)"
                    ></i>
                  </div>
                  <div class="text d-flex pt-2">
                    <input
                      v-model="img.title"
                      type="text"
                      class="form-control form-control-sm rounded-0"
                      placeholder="Title"
                      :name="`uploaded_images[${key}][title]`"
                    />
                  </div>
                </div>
              </div>
            </template>
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

// define model name
const model = "photo";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File },

  data() {
    return {
      model: model,
      submitType: "",
      data: { album_id: null, sorting: 0, uploaded_images: [] },
      images: { image: "" },
      albums: {},
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
          var form = document.getElementById("form");
          var formData = new FormData(form);

          if (Object.keys(this.data.uploaded_images).length == 0) {
            this.notification("Please select image", "error");
            return false;
          }

          this.store(this.model, formData);
        }
      });
    },

    previewImages() {
      let vm = this;
      vm.data.uploaded_images = [];
      let files = $("input[type='file']")[0].files;

      if (files.length > 20) {
        vm.notification("You can select max 20 images", "error");
        return false;
      }

      $.each(files, function (i, file) {
        let obj = {
          image: URL.createObjectURL(file),
          title: "",
          sorting: vm.data.sorting + i,
        };
        vm.data.uploaded_images.push(obj);
      });
    },

    getAlbum() {
      axios.get("/get-album/Photos").then((res) => (this.albums = res.data));
    },

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      } else {
        this.get_sorting("Website-Gallery-Photo");
      }

      this.get_data(`get-album/Photos`, "albums");
      this.setBreadcrumbs(this.model, page, null, addOrBack);
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.album_id": function (value = null) {
      return Validator.value(value).required("Album is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).required("Sorting is required");
    },
  },
};
</script>