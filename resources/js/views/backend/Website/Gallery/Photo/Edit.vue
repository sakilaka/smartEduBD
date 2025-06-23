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
            <Input title="Title" field="title" type="text" col="4" />
            <!------------ Single Input ------------>
            <Input
              title="Sorting"
              field="sorting"
              type="number"
              :req="true"
              col="2"
            />
            <!------------ Single Input ------------>
            <File
              title="Image"
              field="thumb"
              mime="img"
              :req="true"
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
      data: { album_id: null, thumb: "", sorting: 0 },
      images: {},
      albums: {},
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

          this.update(this.model, formData, this.data.id, "image");
        }
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
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.thumb": function (value = null) {
      return Validator.value(value).required("Image is required");
    },
    "data.album_id": function (value = null) {
      return Validator.value(value).required("Album is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).required("Sorting is required");
    },
  },
};
</script>