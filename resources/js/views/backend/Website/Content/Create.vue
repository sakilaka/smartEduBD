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
            <Input
              title="Title"
              field="title"
              type="text"
              :req="true"
              col="9"
            />
            <!------------ Single Input ------------>
            <File
              title="Image"
              field="image"
              mime="img"
              :req="false"
              fileClassName="file1"
              col="3"
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
      <ButtonStatus :save_and_edit="false" />
    </div>
  </form>
</template>

<script>
import File from "@components/backend/elements/Form/File";
import editor from "@components/backend/elements/CKEditor";

// define model name
const model = "content";

export default {
  components: { editor, File },

  data() {
    return {
      model: model,
      data: {
        status: "active",
        image: "",
      },
      images: {},
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
    asyncData() {
      this.get_data(`${this.model}/${this.$route.params.slug}`);
    },
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
          this.$root.submit = true;
          var form = document.getElementById("form");
          var formData = new FormData(form);

          if (this.data.description) {
            formData.append("description", this.data.description);
          } else {
            formData.append("description", "");
          }
          formData.append("slug", this.$route.params.slug);

          axios
            .post("/content", formData)
            .then((res) => {
              if (res.data.message) {
                this.notify(res.data.message, "success");
                this.$router.push({
                  name: "content.show",
                  params: { slug: this.$route.params.slug },
                });
              } else if (res.data.error) {
                this.notify(res.data.error, "error");
                setTimeout(() => {
                  location.reload();
                }, 100);
              }
            })
            .catch((error) => console.log(error))
            .then((alw) => setTimeout(() => (this.$root.submit = false), 200));
        }
      });
    },
  },

  created() {
    const breadcrumb = {
      breadcrumb: [
        {
          route: model + ".create",
          title: model + " Create",
          slug: this.$route.params.slug,
        },
      ],
      addOrBack: {
        route: model + ".show",
        cusTitle: "View Content",
        icon: "plus-circle",
        slug: this.$route.params.slug,
      },
    };
    breadcrumbs.dispatch("setBreadcrumbs", breadcrumb);
  },

  // ================== validation rule for form ==================
  validators: {
    "data.title": function (value = null) {
      return Validator.value(value).required("Title is required");
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
  },
};
</script>