<template>
  <div v-if="!$root.spinner" class="row">
    <div class="col-12 col-xl-6">
      <div class="card">
        <div class="card-body">
          <h5>Before Process Data</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <tbody v-if="data.properties && data.properties.old">
                <tr
                  v-for="(item, name) in data.properties.old"
                  :key="'A' + name"
                >
                  <th class="text-capitalize" v-if="typeof item == 'string'">
                    {{ name.replace(new RegExp("_", "g"), " ") }}
                  </th>
                  <td v-if="typeof item == 'string'">
                    <span v-if="name == 'created_at'">{{
                      item | formatDate
                    }}</span>
                    <span v-else-if="name == 'updated_at'">{{
                      item | formatDate
                    }}</span>
                    <span v-else>{{ item }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-xl-6">
      <div class="card">
        <div class="card-body">
          <h5>After Process Data</h5>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <tbody v-if="data.properties && data.properties.attributes">
                <tr
                  v-for="(item, name) in data.properties.attributes"
                  :key="'A' + name"
                >
                  <th class="text-capitalize" v-if="typeof item == 'string'">
                    {{ name.replace(new RegExp("_", "g"), " ") }}
                  </th>
                  <td v-if="typeof item == 'string'">
                    <span v-if="name == 'created_at'">{{
                      item | formatDate
                    }}</span>
                    <span v-else-if="name == 'updated_at'">{{
                      item | formatDate
                    }}</span>
                    <span v-else>{{ item }}</span>
                  </td>
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
const model = "activityLog";

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
      data: {},
    };
  },
  created() {
    this.get_data(`${this.model}/${this.$route.params.id}`);
    this.setBreadcrumbs(this.model, "view", "Activity Log", addOrBack);
  },
};
</script>