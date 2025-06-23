<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9">
      <div class="row">
        <div class="col-md-6">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Android Version</h6>
            </div>
            <div class="card-body p-3">
              <div class="mb-3">
                <Label title="Latest Version" :req="false" />
                <input
                  class="form-control form-control-sm"
                  v-model="data.android.latest_version"
                  type="text"
                />
              </div>
              <div class="mb-3">
                <Label title="Minimum Version" :req="false" />
                <input
                  class="form-control form-control-sm"
                  v-model="data.android.minimum_version"
                  type="text"
                />
              </div>

              <div class="mb-3 border p-3 rounded">
                <div class="form-check">
                  <input
                    type="checkbox"
                    v-model="data.android.force_update"
                    id="android_fu"
                    class="form-check-input"
                  />
                  <label class="form-check-label" for="android_fu">
                    Force Update
                  </label>
                  <div class="small text-secondary">
                    User might not permit app access till updating to newest
                    version.
                  </div>
                </div>
              </div>
              <div class="mb-3 border p-3 rounded">
                <div class="form-check">
                  <input
                    type="checkbox"
                    v-model="data.android.maintenance_mode"
                    id="android_mm"
                    class="form-check-input"
                  />
                  <label class="form-check-label" for="android_mm">
                    Maintenance Mode
                  </label>
                  <div class="small text-secondary">
                    User might not permit app access till updating to newest
                    version.
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <Label title="Maintenance Date" :req="false" />
                <date-picker
                  v-model="data.android.maintenance_date"
                  type="date"
                  range
                  valueType="format"
                  format="YYYY-MM-DD"
                  placeholder="Select Date"
                ></date-picker>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card border">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">IOS Version</h6>
            </div>
            <div class="card-body p-3">
              <div class="mb-3">
                <Label title="Latest Version" />
                <input
                  class="form-control form-control-sm"
                  v-model="data.ios.latest_version"
                  type="text"
                />
              </div>
              <div class="mb-3">
                <Label title="Minimum Version" />
                <input
                  class="form-control form-control-sm"
                  v-model="data.ios.minimum_version"
                  type="text"
                />
              </div>

              <div class="mb-3 border p-3 rounded">
                <div class="form-check">
                  <input
                    type="checkbox"
                    v-model="data.ios.force_update"
                    id="ios_fu"
                    class="form-check-input"
                  />
                  <label class="form-check-label" for="ios_fu">
                    Force Update
                  </label>
                  <div class="small text-secondary">
                    User might not permit app access till updating to newest
                    version.
                  </div>
                </div>
              </div>
              <div class="mb-3 border p-3 rounded">
                <div class="form-check">
                  <input
                    type="checkbox"
                    v-model="data.ios.maintenance_mode"
                    id="ios_mm"
                    class="form-check-input"
                  />
                  <label class="form-check-label" for="ios_mm">
                    Maintenance Mode
                  </label>
                  <div class="small text-secondary">
                    User might not permit app access till updating to newest
                    version.
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <Label title="Maintenance Date" :req="false" />
                <date-picker
                  v-model="data.ios.maintenance_date"
                  type="date"
                  range
                  valueType="format"
                  format="YYYY-MM-DD"
                  placeholder="Select Date"
                ></date-picker>
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
// define model name
const model = "mobileAppVersion";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Mobile App Version",
  icon: "left-arrow-alt",
};

export default {
  data() {
    return {
      model: model,
      submitType: "",
      data: {
        android: {
          latest_version: "0.0.0",
          minimum_version: "0.0.0",
          force_update: false,
          maintenance_mode: false,
        },
        ios: {
          latest_version: "0.0.0",
          minimum_version: "0.0.0",
          force_update: false,
          maintenance_mode: false,
        },
      },
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
          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data, this.submitType);
          }
        }
      });
    },

    // Async Data
    asyncData() {
      this.get_data(`${this.model}/1`);
      this.setBreadcrumbs(this.model, "edit", "Mobile App Version", addOrBack);
    },
  },
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  validators: {},
};
</script>