<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9">
      {{ providerData }}
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <Input title="Name" field="name" :req="true" col="6" />
            <File
              title="Logo"
              field="logo"
              mime="img"
              fileClassName="file1"
              col="3"
            />
            <Input title="Domain" field="domain" type="url" col="3" />
            <!------------ Single Input ------------>
            <div class="col-6">
              <Label
                title="Package"
                :condition="validation.hasError('data.package_id')"
                :msg="validation.firstError('data.package_id')"
              />
              <v-select
                v-model="data.package_id"
                label="name"
                :reduce="(obj) => obj.id"
                :options="packages"
                placeholder="--Select Any--"
                :closeOnSelect="true"
              ></v-select>
            </div>
            <Textarea title="Address" field="address" />
          </div>
        </div>
      </div>

      <!-- providers -->
      <div class="row">
        <!-- SHIFT -->
        <div class="col-xl-3">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Shift</h6>
            </div>
            <div class="card-body category-map">
              <template v-if="data.providers.shifts">
                <div
                  v-for="(shift, key) in data.providers.shifts"
                  :key="key"
                  class="col-12 mb-2"
                >
                  <div class="form-check">
                    <input
                      v-model="shift.checked"
                      class="form-check-input"
                      type="checkbox"
                      :value="true"
                    />
                    {{ shift.name }}
                  </div>
                </div>
              </template>
              <div v-else class="text-center my-5">processing...</div>
            </div>
          </div>
        </div>

        <!-- MEDIUM/VERSION -->
        <div class="col-xl-3">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">MEDIUM/VERSION</h6>
            </div>
            <div class="card-body category-map">
              <template v-if="data.providers.mediums">
                <div
                  v-for="(medium, key) in data.providers.mediums"
                  :key="key"
                  class="col-12 mb-2"
                >
                  <div class="form-check">
                    <input
                      v-model="medium.checked"
                      class="form-check-input"
                      type="checkbox"
                      :value="true"
                    />
                    {{ medium.name }}
                  </div>
                </div>
              </template>
              <div v-else class="text-center my-5">processing...</div>
            </div>
          </div>
        </div>

        <!-- GROUPS -->
        <div class="col-xl-3">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">GROUPS</h6>
            </div>
            <div class="card-body category-map">
              <template v-if="data.providers.groups">
                <div
                  v-for="(group, key) in data.providers.groups"
                  :key="key"
                  class="col-12 mb-2"
                >
                  <div class="form-check">
                    <input
                      v-model="group.checked"
                      class="form-check-input"
                      type="checkbox"
                      :value="true"
                    />
                    {{ group.name }}
                  </div>
                </div>
              </template>
              <div v-else class="text-center my-5">processing...</div>
            </div>
          </div>
        </div>

        <!-- SECTIONS -->
        <div class="col-xl-3">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">SECTIONS</h6>
            </div>
            <div class="card-body category-map">
              <template v-if="data.providers.sections">
                <div
                  v-for="(section, key) in data.providers.sections"
                  :key="key"
                  class="col-12 mb-2"
                >
                  <div class="form-check">
                    <input
                      v-model="section.checked"
                      class="form-check-input"
                      type="checkbox"
                      :value="true"
                    />
                    {{ section.name }}
                  </div>
                </div>
              </template>
              <div v-else class="text-center my-5">processing...</div>
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
import Textarea from "@components/backend/elements/Form/Textarea";

// define model name
const model = "institution";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File, Textarea },
  data() {
    return {
      model: model,
      submitType: "",
      data: {
        status: "active",
        package_id: null,
        logo: "",
        providers: {
          shifts: [],
          mediums: [],
          groups: [],
          sections: [],
        },
      },
      packages: [],
      images: {},
    };
  },

  computed: {
    providerData() {
      if (this.$root.global.shifts) this.providerPush("shifts", "Shift");
      if (this.$root.global.mediums) this.providerPush("mediums", "Medium");
      if (this.$root.global.groups) this.providerPush("groups", "Group");
      if (this.$root.global.sections) this.providerPush("sections", "Section");
    },
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
          // var form = document.getElementById("form");
          // var formData = new FormData(form);
          // formData.append("package_id", this.data.package_id);

          let data = { ...this.data };

          data.providers["shifts"] = data.providers.shifts.filter(
            (e) => e.checked === true
          );
          data.providers["mediums"] = data.providers.mediums.filter(
            (e) => e.checked === true
          );
          data.providers["groups"] = data.providers.groups.filter(
            (e) => e.checked === true
          );
          data.providers["sections"] = data.providers.sections.filter(
            (e) => e.checked === true
          );

          if (this.data.id) {
            this.update(this.model, data, data.id);
          } else {
            this.store(this.model, data, this.submitType);
          }
        }
      });
    },

    providerPush(provider, model) {
      let items = [];
      if (this.data.id) {
        items = this.data["providers"][`${provider}`];
      }

      this.data["providers"][`${provider}`] = [];

      if (this.$root.global[provider]) {
        this.$root.global[provider].forEach((arr) => {
          find = items ? items.find((el) => el.id == arr.id) : false;
          arr["checked"] = find ? true : false;
          arr["provider_id"] = arr.id;
          arr["provider_type"] = `\\App\\Models\\MasterSetup\\${model}`;
          this.data["providers"][`${provider}`].push(arr);
        });
      }
    },

    getProvider() {
      if (this.$root.global) {
      }
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

  created() {
    this.get_data("package", "packages", { allData: true });
  },

  // ================== validation rule for form ==================
  validators: {
    "data.package_id": function (value = null) {
      return Validator.value(value).required("Package is required");
    },
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.address": function (value = null) {
      return Validator.value(value).required("Address is required");
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
  },
};
</script>