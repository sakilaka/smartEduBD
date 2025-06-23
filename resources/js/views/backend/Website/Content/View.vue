<template>
  <div v-if="!$root.spinner">
    <div class="card">
      <div class="card-body">
        <h5>{{ data.title }}</h5>
        <div class="row">
          <div class="col-9" v-html="data.description"></div>
          <div class="col-3">
            <img :src="data.image" height="150px" width="100%" />
          </div>
        </div>
      </div>
    </div>

    <!-- Content Files -->
    <div
      v-if="
        data && data.content_files && Object.keys(data.content_files).length > 0
      "
      class="card"
    >
      <div class="card-body">
        <h5>Content Files</h5>
        <div class="col-12">
          <table class="table table-bordered table-hover table-striped">
            <thead>
              <tr>
                <th class="sl">#</th>
                <th>Title</th>
                <th class="action">File</th>
              </tr>
            </thead>
            <tbody>
              <slot v-for="(dataFile, index) in data.content_files">
                <tr :key="index">
                  <td class="text-center">{{ index + 1 }}</td>
                  <td>{{ dataFile.title }}</td>
                  <td class="action">
                    <a
                      target="_blank"
                      :href="$root.baseurl + '/storage/' + dataFile.file"
                      class="btn btn-xs btn-success action-view"
                    >
                      <i class="bx bx-show"></i>
                    </a>
                    <button
                      v-if="$root.checkPermission('content.destroy')"
                      @click="notify('Delete', 'confirm', dataFile.id)"
                      class="btn btn-xs btn-danger"
                      title="Delete"
                    >
                      <i class="bx bx-trash"></i>
                    </button>
                  </td>
                </tr>
              </slot>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
// define model name
const model = "content";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "Update Content",
  icon: "plus-circle",
  extraButtons: [
    {
      route: model + ".file",
      icon: "plus-circle",
      btnColor: "primary",
      title: "Add Content Files",
    },
  ],
};

export default {
  data() {
    return {
      model: model,
      data: {},
    };
  },
  methods: {
    callBack(id) {
      axios
        .delete("/" + this.model + "/" + id)
        .then((res) => {
          this.get_data(`${this.model}/${this.$route.params.slug}`);
          this.notify(res.data.message, "success");
        })
        .catch((error) => console.log(error));
    },
  },
  created() {
    var breadcrumb = {
      breadcrumb: [
        {
          route: model + ".show",
          title: model + " View",
          slug: this.$route.params.slug,
        },
      ],
      addOrBack: addOrBack,
    };
    breadcrumbs.dispatch("setBreadcrumbs", breadcrumb);
    this.get_data(`${this.model}/${this.$route.params.slug}`);
  },
};
</script>