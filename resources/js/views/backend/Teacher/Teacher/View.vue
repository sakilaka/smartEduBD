<template>
  <div class="card" v-if="!$root.spinner">
    <div class="card-body min-height">
      <table class="table table-bordered table-striped mb-0">
        <thead>
          <tr>
            <th width="280px">Department Name</th>
            <td>
              {{ data.department ? data.department.name : "" }}
            </td>
          </tr>
          <tr>
            <th>Designation</th>
            <td>
              {{
                data.teacher.designation ? data.teacher.designation.name : ""
              }}
            </td>
          </tr>
          <tr>
            <th>Blood Group</th>
            <td>
              {{ data.teacher.blood_group }}
            </td>
          </tr>
          <tr>
            <th>Index/ID Number</th>
            <td>{{ data.teacher.index_number }}</td>
          </tr>
          <tr>
            <th>Date of Birth</th>
            <td>
              {{ data.teacher.date_of_birth | formatDate }}
            </td>
          </tr>
          <tr>
            <th>Joining Date As Lecturer</th>
            <td>
              {{ data.teacher.joining_date_lecturer | formatDate }}
            </td>
          </tr>
          <tr>
            <th>Joining Date As Present Designation</th>
            <td>
              {{ data.teacher.joining_date_present_designation | formatDate }}
            </td>
          </tr>
          <tr>
            <th>Joining Date As Present Work Station</th>
            <td>
              {{ data.teacher.joining_date_present_work_station | formatDate }}
            </td>
          </tr>
          <tr>
            <th>Present Address</th>
            <td>
              {{ data.teacher.present_address }}
            </td>
          </tr>
          <tr>
            <th>Permanent Address</th>
            <td>
              {{ data.teacher.permanent_address }}
            </td>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</template>

<script>
// define model name
const model = "teacher";

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
    await this.get_data(this.model, this.$route.params.id, "data"); // get data
    this.setBreadcrumbs(this.model, "view", null, addOrBack); // Set Breadcrumbs
  },
};
</script>