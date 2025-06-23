<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <div v-if="Object.keys(data).length > 0" class="row">
      <div class="col-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">Android Version</h6>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
              <tbody>
                <tr>
                  <th style="width: 200px">Latest Version</th>
                  <td>{{ data.android.latest_version }}</td>
                </tr>
                <tr>
                  <th>Minimum Version</th>
                  <td>{{ data.android.minimum_version }}</td>
                </tr>
                <tr>
                  <th>Force Update</th>
                  <td>{{ data.android.force_update ? "YES" : "NO" }}</td>
                </tr>
                <tr>
                  <th>Maintenance Mode</th>
                  <td>{{ data.android.maintenance_mode ? "YES" : "NO" }}</td>
                </tr>
                <tr>
                  <th>Maintenance Start</th>
                  <td>
                    <span v-if="data.android.maintenance_date">
                      {{ data.android.maintenance_date[0] | formatDate }}
                    </span>
                  </td>
                </tr>
                <tr>
                  <th>Maintenance End</th>
                  <td>
                    <span v-if="data.android.maintenance_date">
                      {{ data.android.maintenance_date[1] | formatDate }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">IOS Version</h6>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
              <tbody>
                <tr>
                  <th style="width: 200px">Latest Version</th>
                  <td>{{ data.ios.latest_version }}</td>
                </tr>
                <tr>
                  <th>Minimum Version</th>
                  <td>{{ data.ios.minimum_version }}</td>
                </tr>
                <tr>
                  <th>Force Update</th>
                  <td>{{ data.ios.force_update ? "YES" : "NO" }}</td>
                </tr>
                <tr>
                  <th>Maintenance Mode</th>
                  <td>{{ data.ios.maintenance_mode ? "YES" : "NO" }}</td>
                </tr>
                <tr>
                  <th>Maintenance Start</th>
                  <td>
                    <span v-if="data.ios.maintenance_date">
                      {{ data.ios.maintenance_date[0] | formatDate }}
                    </span>
                  </td>
                </tr>
                <tr>
                  <th>Maintenance End</th>
                  <td>
                    <span v-if="data.ios.maintenance_date">
                      {{ data.ios.maintenance_date[1] | formatDate }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="row py-5">
      <h5 class="text-center text-secondary py-5">Update Mobile App Version</h5>
    </div>
  </div>
</template>


<script>
// define model name
const model = "mobileAppVersion";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "Update Mobile App Version",
  icon: "plus-circle",
};

const breadcrumbMenu = [
  { route: "mobileAppVersion.index", title: "Mobile App Version" },
];

export default {
  data() {
    return {
      model: model,
      data: {},
      breadcrumb: { breadcrumb: breadcrumbMenu, addOrBack: addOrBack },
    };
  },
  created() {
    this.get_data(`${this.model}/1`);
    breadcrumbs.dispatch("setBreadcrumbs", this.breadcrumb);
  },
};
</script>