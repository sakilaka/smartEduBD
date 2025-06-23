<template>
  <div v-if="!$root.spinner">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">
          <i class="bx bx-chevrons-right"></i>Academic Information
        </h6>
      </div>
      <div class="card-body">
        <div class="col-12">
          <table class="table table-bordered table-hover">
            <tbody>
              <tr>
                <th style="width: 200px">Institution</th>
                <td>
                  {{ data.institution ? data.institution.name : "" }}
                </td>
              </tr>
              <tr>
                <th style="width: 200px">Medium/Version</th>
                <td>
                  {{ data.medium ? data.medium.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Class</th>
                <td>
                  {{ data.academic_class ? data.academic_class.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Group</th>
                <td>
                  {{ data.group ? data.group.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Remarks</th>
                <td>
                  {{ data.description }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Fee Setup Details -->
    <div
      class="card"
      v-if="data.details && Object.keys(data.details).length > 0"
    >
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">
          <i class="bx bx-chevrons-right"></i>Fee Setup Details
        </h6>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th>Account Head</th>
              <th class="text-end">Amount</th>
              <th>Gateway Account</th>
              <th>Start Date</th>
              <th>Expired Date</th>
              <th>Additional Date</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, key) in data.details" :key="key">
              <td>
                {{ item.account_head ? item.account_head.name_en : "" }}
              </td>
              <td class="text-end">{{ item.amount | currency }}</td>
              <td>
                {{ item.gateway ? item.gateway.title : "" }}
              </td>
              <td>{{ item.start_date | formatDate }}</td>
              <td>{{ item.expire_date | formatDate }}</td>
              <td>{{ item.additional_date | formatDate }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
// define model name
const model = "feeSetup";

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
      data: [],
    };
  },
  async created() {
    this.get_data(`${this.model}/${this.$route.params.id}`); // get data
    this.setBreadcrumbs(this.model, "view", "Fee Setup", addOrBack); // Set Breadcrumbs
  },
};
</script>