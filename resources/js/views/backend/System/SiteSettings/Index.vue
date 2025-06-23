<template>
  <div v-if="!$root.spinner" class="col-xl-12">
    <div class="row">
      <!-- systems info -->
      <div class="col-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">Systems Info</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-striped mb-0">
              <tbody>
                <tr>
                  <th style="width: 200px">Title</th>
                  <td>{{ data.title }}</td>
                </tr>
                <tr>
                  <th>Short Title</th>
                  <td>{{ data.short_title }}</td>
                </tr>
                <tr>
                  <th>Contact Email</th>
                  <td>{{ data.contact_email }}</td>
                </tr>
                <tr>
                  <th>Feedback Email</th>
                  <td>{{ data.feedback_email }}</td>
                </tr>
                <tr>
                  <th>Mobile One</th>
                  <td>{{ data.mobile1 }}</td>
                </tr>
                <tr>
                  <th>Mobile Two</th>
                  <td>{{ data.mobile2 }}</td>
                </tr>
                <tr>
                  <th>Address</th>
                  <td>{{ data.address }}</td>
                </tr>
                <tr>
                  <th>Web</th>
                  <td>{{ data.web }}</td>
                </tr>
                <tr>
                  <th>Developed By</th>
                  <td>{{ data.developed_by }}</td>
                </tr>
                <tr>
                  <th>Developed By URL</th>
                  <td>{{ data.developed_by_url }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- right side -->
      <div class="col-6">
        <!-- Images -->
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">Images</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-striped mb-0">
              <thead>
                <tr>
                  <th class="text-center">Logo</th>
                  <th class="text-center">Logo Small</th>
                  <th class="text-center">Favicon</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-center">
                    <a :href="data.logo" target="_blank">
                      <img :src="data.logo" height="40px" />
                    </a>
                  </td>
                  <td class="text-center">
                    <a :href="data.logo_small" target="_blank">
                      <img :src="data.logo_small" height="40px" />
                    </a>
                  </td>
                  <td class="text-center">
                    <a :href="data.favicon" target="_blank">
                      <img :src="data.favicon" height="20px" />
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- ID CADR -->
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">ID CADR</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-striped mb-0">
              <thead>
                <tr>
                  <th class="text-center">Front</th>
                  <th class="text-center">Back</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td class="text-center">
                    <a :href="data.idcard_front_part" target="_blank">
                      <img :src="data.idcard_front_part" height="40px" />
                    </a>
                  </td>
                  <td class="text-center">
                    <a :href="data.idcard_back_part" target="_blank">
                      <img :src="data.idcard_back_part" height="40px" />
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Social Media -->
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">Social Media</h6>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-striped mb-0">
              <tbody>
                <tr>
                  <th style="width: 200px">Facebook</th>
                  <td>{{ data.fb }}</td>
                </tr>
                <tr>
                  <th>Twitter</th>
                  <td>{{ data.tw }}</td>
                </tr>
                <tr>
                  <th>Linkedin</th>
                  <td>{{ data.ln }}</td>
                </tr>
                <tr>
                  <th>Youtube</th>
                  <td>{{ data.yt }}</td>
                </tr>
                <tr>
                  <th>Map</th>
                  <td>{{ data.map }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
// define model name
const model = "siteSetting";

// Add Or Back
const addOrBack = {
  route: model + ".create",
  cusTitle: "Update Site Setting",
  icon: "plus-circle",
};

const breadcrumbMenu = [{ route: "siteSetting.index", title: "Site Setting" }];

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