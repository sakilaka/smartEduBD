<template>
  <div v-if="!$root.spinner" class="col-xl-5 mx-auto">
    <form v-on:submit.prevent="submit" id="form">
      <div class="card">
        <div class="card-body">
          <div class="border border-success p-4 rounded">
            <!------------ Single Input ------------>
            <Input
              title="Title"
              field="title"
              type="text"
              :req="true"
              col="12"
            />
            <!------------ Single Input ------------>
            <div
              class="col-12 mb-3 mt-2"
              :class="{ 'has-error': errors.file, 'has-success': image.file }"
            >
              <label class="col-3"
                >File <span class="text-danger">*</span></label
              >
              <div class="col-12">
                <input
                  type="file"
                  accept=".pdf"
                  name="file"
                  class="form-control form-control-sm"
                />
                <span class="text-danger" v-if="errors.file">
                  {{ errors.file[0] }}
                </span>
              </div>
            </div>
            <!------------ Single Input ------------>
            <Input
              title="Sorting"
              field="sorting"
              type="number"
              :req="true"
              col="12"
            />

            <!-------------- button -------------->
            <div class="col-12 mt-4">
              <Button title="Submit" process="" />
            </div>
            <!-------------- button -------------->
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
// define model name
const model = "content";

export default {
  data() {
    return {
      model: model,
      data: { sorting: 0 },
      image: { file: "" },
      errors: {},
    };
  },
  methods: {
    submit: function () {
      const error = this.validation.countErrors();
      this.$validate().then((res) => {
        // If there is an error
        if (error > 0) {
          this.notify(
            "You need to fill " + error + " more empty mandatory fields",
            "warning"
          );
          return false;
        }

        // If there is no error
        if (res) {
          this.$root.submit = true;
          var form = document.getElementById("form");
          var formData = new FormData(form);

          axios
            .post("/content-file/" + this.data.slug, formData)
            .then((res) => {
              this.notify(res.data.message, "success");
              this.$router.push({
                name: "content.show",
                params: { slug: this.$route.params.slug },
              });
            })
            .catch((error) => {
              if (error.response.status === 422) {
                this.errors = error.response.data.errors || {};
                this.notify(error, "validate");
              }
            })
            .then((alw) => setTimeout(() => (this.$root.submit = false), 200));
        }
      });
    },
  },
  created() {
    this.data.slug = this.$route.params.slug;
    // set breadcrumb
    var breadcrumb = {
      breadcrumb: [
        {
          route: model + ".create",
          title: model + " Create",
          slug: this.$route.params.slug,
        },
        {
          route: model + ".file",
          title: model + " File Create ",
          slug: this.$route.params.slug,
        },
      ],
      addOrBack: {
        route: model + ".show",
        cusTitle: "View Content",
        icon: "plus-circle",
        slug: this.$route.params.slug,
        extraButtons: [
          {
            route: model + ".create",
            icon: "plus-circle",
            btnColor: "primary",
            title: "Update Content",
            slug: this.$route.params.slug,
          },
        ],
      },
    };
    breadcrumbs.dispatch("setBreadcrumbs", breadcrumb);
  },

  // ================== validation rule for form ==================
  validators: {
    "data.title": function (value = null) {
      return Validator.value(value).required("Title is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value)
        .required("Sorting is required")
        .digit("Must be a digit");
    },
  },
};
</script>