<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header mt-2">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">
                {{ data.name }} - <small class="fw-normal">Class Mapping</small>
              </h6>
            </div>
            <div class="col-6 d-flex justify-content-end">
              <button
                type="button"
                :disabled="$root.submit ? true : false"
                class="btn btn-success btn-sm px-3"
                @click="submit()"
              >
                <span v-if="$root.submit">
                  <span class="spinner-border spinner-border-sm"></span>
                  <span>processing...</span>
                </span>
                <span v-else> <i class="bx bx-save"></i> Save</span>
              </button>
            </div>
          </div>
        </div>
        <div class="card-body p-3">
          <div class="row g-3">
            <label class="col-6 col-xl-2">
              Campus <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Shift <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Medium <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Academic Class <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-2">
              Group <span class="req">*</span>
            </label>
            <label class="col-6 col-xl-1">
              Section <span class="req">*</span>
            </label>
          </div>
          <div
            v-for="(mapping, key) in data.class_mappings"
            :key="key"
            class="row g-3 mb-4 pb-2 border-bottom border-success"
          >
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <select
                v-model="mapping.campus_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(campus, key) in campuses_filter(data.id)"
                  :key="key"
                  v-bind:value="campus.id"
                >
                  {{ campus.name }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <select
                v-model="mapping.shift_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(shift, key) in $root.global.shifts"
                  :key="key"
                  v-bind:value="shift.id"
                >
                  {{ shift.name }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <select
                v-model="mapping.medium_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(medium, key) in $root.global.mediums"
                  :key="key"
                  v-bind:value="medium.id"
                >
                  {{ medium.name }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <select
                v-model="mapping.academic_class_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(cls, key) in $root.global.classes"
                  :key="key"
                  v-bind:value="cls.id"
                >
                  {{ cls.name }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-2">
              <select
                v-model="mapping.group_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(group, key) in $root.global.groups"
                  :key="key"
                  v-bind:value="group.id"
                >
                  {{ group.name }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-xl-1">
              <select
                v-model="mapping.section_id"
                class="form-select form-select-sm"
              >
                <option :value="null">--Select Any--</option>
                <option
                  v-for="(section, key) in $root.global.sections"
                  :key="key"
                  v-bind:value="section.id"
                >
                  {{ section.name }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <div class="col-6 col-6 col-xl-1">
              <i
                @click="copyItem(key)"
                class="bx bx-copy btn btn-xs btn-success"
              >
              </i>
              <i
                v-if="Object.keys(data.class_mappings).length > 1"
                @click="data.class_mappings.splice(key, 1)"
                class="bx bx-minus btn btn-xs btn-danger"
              >
              </i>
              <i
                v-if="Object.keys(data.class_mappings).length == key + 1"
                @click="
                  data.class_mappings.push({
                    campus_id: null,
                    shift_id: null,
                    medium_id: null,
                    academic_class_id: null,
                    group_id: null,
                    section_id: null,
                  })
                "
                class="bx bx-plus btn btn-xs btn-primary ml-2"
              >
              </i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
// define model name
const model = "academicClassMapping";

const breadcrumb = [
  { route: "institution.index", title: "Institution" },
  { route: "institution.index", title: "Class Mapping" },
];

// Add Or Back
const addOrBack = {
  route: "institution.index",
  title: "",
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      customBreadcrumb: { breadcrumb: breadcrumb, addOrBack: addOrBack },
      data: {
        id: null,
        class_mappings: [],
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
      axios
        .put(`${this.model}/${this.data.id}`, this.data)
        .then((res) => {
          if (res.data.message) {
            this.notify(res.data.message, "success");
          } else if (res.data.error) {
            this.notify(res.data.error, "error");
          } else if (res.data.warning) {
            this.notify(res.data.warning, "warning");
          }

          this.$router.push({ name: "institution.index" });
        })
        .catch((error) => {
          if (error.response.status === 422) {
            this.$bvModal.show("validate-error");
            if (error.response.data.exception) {
              this.$root.exception_errors = error.response.data.exception;
            } else {
              this.$root.validation_errors = error.response.data.errors || {};
            }
            this.notify("You need to fill empty mandatory fields", "warning");
          }
        })
        .finally((alw) => setTimeout(() => (this.$root.submit = false), 400));
    },

    copyItem(index) {
      let item = this.data.class_mappings[index];
      let copy_item = { ...item };
      copy_item.id = null;
      this.data.class_mappings.push(copy_item);
    },

    // Async Data
    asyncData() {
      if (this.$route.params.id) {
        let page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
        this.setBreadcrumbs(this.model, page, "Class Mapping", addOrBack);
      }
    },
  },

  mounted() {
    this.data.id = this.$root.institution_id;

    breadcrumbs.dispatch("setBreadcrumbs", this.customBreadcrumb);
  },
};
</script>