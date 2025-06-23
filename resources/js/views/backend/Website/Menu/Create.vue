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
              v-if="parentMenu"
              title="Parent Menu"
              field="parent_id"
              :req="false"
              :datas="parentMenu"
            />
            <!------------ Single Input ------------>
            <Input title="Title" field="title" type="text" :req="true" />
            <!------------ Single Input ------------>
            <Select
              v-if="menu_types"
              title="Menu Type"
              field="type"
              :req="true"
              :datas="menu_types"
            />
            <!------------ Single Input ------------>
            <Select
              v-if="menu_look_types"
              title="Menu look type"
              field="menu_look_type"
              :req="false"
              :datas="menu_look_types"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              v-if="contents && data.type == 'content'"
              title="Content"
              field="content_id"
              :req="false"
              :datas="contents"
              val="id"
              val_title="title"
            />
            <!------------ Single Input ------------>
            <div
              v-if="
                data.type == 'outside_website' || data.type == 'external_link'
              "
              class="form-group col-12 col-xl-3"
            >
              <label v-if="data.type == 'outside_website'">URL</label>
              <label v-if="data.type == 'external_link'">Route Name</label>
              <input
                type="text"
                v-model="data.url"
                class="form-control form-control-sm"
                :placeholder="
                  data.type == 'outside_website' ? 'URL' : 'Route Name'
                "
              />
              <span class="help-block">
                {{ validation.firstError("data.url") }}
              </span>
            </div>
            <!------------ Single Input ------------>
            <Input title="Params" field="params" type="text" :req="false" />
            <!------------ Single Input ------------>
            <Select
              v-if="positions"
              title="Menu position"
              field="position"
              :req="false"
              :datas="positions"
            />
            <!------------ Single Input ------------>
            <Input
              title="Sorting"
              field="sorting"
              type="number"
              :req="true"
              col="2"
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
// define model name
const model = "frontMenu";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      submitType: "",
      contents: [],
      positions: {
        header: "Header",
        top_bar: "Top Bar",
        footer_bottom: "Footer Bottom",
      },
      menu_look_types: {
        normal: "Normal",
        mega: "Mega",
      },
      menu_types: {
        content: "Content",
        external_link: "External Link",
        outside_website: "Outside Website",
      },
      parentMenu: {},
      data: {
        parent_id: null,
        content_id: null,
        type: null,
        position: "header",
        content: null,
        menu_look_type: "normal",
        status: "active",
        sorting: 0,
      },
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
        this.get_sorting("Website-FrontMenu");
      }
      this.setBreadcrumbs(this.model, page, "Menu", addOrBack);

      this.get_data("parent-menus", "parentMenu");
      this.get_data("get-content", "contents", { allData: true });
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.title": function (value = null) {
      return Validator.value(value).required("Title is required");
    },
    "data.type": function (value = null) {
      return Validator.value(value).required("Menu Type is required");
    },
    "data.position": function (value = null) {
      return Validator.value(value).required("Menu Position is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).required("Sorting is required");
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
  },
};
</script>