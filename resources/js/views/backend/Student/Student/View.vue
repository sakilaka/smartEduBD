<template>
  <div class="row" v-if="!$root.spinner">
    <div class="col-xl-4 col-12">
      <!-- Academic Information -->
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            <i class="bx bx-chevrons-right"></i> Academic Information
          </h6>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered table-striped mb-0">
            <tbody>
              <tr>
                <th>Institution</th>
                <td>
                  {{ data.institution ? data.institution.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Academic Session</th>
                <td>
                  {{ data.academic_session ? data.academic_session.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Campus</th>
                <td>{{ data.campus ? data.campus.name : "" }}</td>
              </tr>
              <tr>
                <th>Shift</th>
                <td>
                  {{ data.shift ? data.shift.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Medium/Version</th>
                <td>
                  {{ data.medium ? data.medium.name : "" }}
                </td>
              </tr>
              <tr>
                <th>Academic Class</th>
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
                <th>Section</th>
                <td>
                  {{ data.section ? data.section.name : "" }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- CONTACT INFORMATION -->
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            <i class="bx bx-chevrons-right"></i> CONTACT INFORMATION
          </h6>
        </div>
        <div class="card-body" style="min-height: 169px">
          <table class="table table-sm table-bordered table-striped mb-0">
            <tbody>
              <tr>
                <th style="width: 200px">Email</th>
                <td>{{ data.email }}</td>
              </tr>
              <tr>
                <th>Mobile</th>
                <td>{{ data.mobile }}</td>
              </tr>
              <tr>
                <th>Present Address</th>
                <td>{{ data.profile.present_address }}</td>
              </tr>
              <tr>
                <th>Permanent Address</th>
                <td>{{ data.profile.permanent_address }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Student Information -->
    <div class="col-xl-4 col-12">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            <i class="bx bx-chevrons-right"></i> Student Information
          </h6>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered table-striped mb-0">
            <tbody>
              <tr>
                <th style="width: 180px">Profile Image</th>
                <td>
                  <img
                    :src="
                      data.profile.profile
                        ? data.profile.profile
                        : $root.userimage
                    "
                    style="height: 113px"
                    class="img"
                    alt="PROFILE"
                  />
                </td>
              </tr>
              <tr>
                <th>Software ID</th>
                <td>
                  {{ data.software_id }}
                </td>
              </tr>
              <tr>
                <th>Roll Number</th>
                <td>{{ data.profile.roll_number }}</td>
              </tr>
              <tr>
                <th>Name (en)</th>
                <td>{{ data.name_en }}</td>
              </tr>
              <tr>
                <th>Name (bn)</th>
                <td>{{ data.name_bn }}</td>
              </tr>
              <tr>
                <th>NID/Birth Reg No</th>
                <td>{{ data.profile.nid_or_birth_reg }}</td>
              </tr>
              <tr>
                <th>Date of Birth</th>
                <td>{{ data.profile.dob | formatDate }}</td>
              </tr>
              <tr>
                <th>Religion</th>
                <td>{{ data.profile.religion }}</td>
              </tr>
              <tr>
                <th>Gender</th>
                <td>{{ data.profile.gender }}</td>
              </tr>
              <tr>
                <th>Disability</th>
                <td>{{ data.profile.disability }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-xl-4 col-12">
      <!-- Family Information -->
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            <i class="bx bx-chevrons-right"></i> Family Information
          </h6>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered table-striped mb-0">
            <tbody>
              <tr>
                <th style="width: 200px">Father's Name (en)</th>
                <td>{{ data.profile.fathers_name_en }}</td>
              </tr>
              <tr>
                <th>Father's Name (bn)</th>
                <td>{{ data.profile.fathers_name_bn }}</td>
              </tr>
              <tr>
                <th>Father's Mobile</th>
                <td>{{ data.profile.fathers_mobile }}</td>
              </tr>
              <tr>
                <th>Father's NID/Birth Reg No</th>
                <td>{{ data.profile.fathers_nid_or_birth_reg }}</td>
              </tr>
              <tr>
                <th>Mother's Name (en)</th>
                <td>{{ data.profile.mothers_name_en }}</td>
              </tr>
              <tr>
                <th>Mother's Name (bn)</th>
                <td>{{ data.profile.mothers_name_bn }}</td>
              </tr>
              <tr>
                <th>Mother's Mobile</th>
                <td>{{ data.profile.mothers_mobile }}</td>
              </tr>
              <tr>
                <th>Mother's NID/Birth Reg No</th>
                <td>{{ data.profile.mothers_nid_or_birth_reg }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- GUARDIAN INFORMATION -->
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            <i class="bx bx-chevrons-right"></i> GUARDIAN INFORMATION
          </h6>
        </div>
        <div class="card-body">
          <table class="table table-sm table-bordered table-striped mb-0">
            <tbody>
              <tr>
                <th style="width: 200px">Guardian Type</th>
                <td>{{ data.guardian.type }}</td>
              </tr>
              <tr>
                <th>Relations</th>
                <td>{{ data.guardian.relations }}</td>
              </tr>
              <tr>
                <th>Name (en)</th>
                <td>{{ data.guardian.name_en }}</td>
              </tr>
              <tr>
                <th>Name (bn)</th>
                <td>{{ data.guardian.name_bn }}</td>
              </tr>
              <tr>
                <th>NID/Birth Reg No</th>
                <td>{{ data.guardian.nid_or_birth_reg }}</td>
              </tr>
              <tr>
                <th>Mobile</th>
                <td>{{ data.guardian.mobile }}</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>{{ data.guardian.email }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Previous Academic Information -->
    <div class="w-100"></div>
    <div class="col-12 mt-3">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase" style="color: rgb(197 86 61)">
            <i class="bx bx-chevrons-right"></i> Previous Academic Information
          </h6>
        </div>
      </div>
    </div>
    <!-- Previous Block -->
    <template v-if="data.promoted && Object.keys(data.promoted).length > 0">
      <div
        v-for="(promote, key) in data.promoted"
        :key="key"
        class="col-xl-4 col-12"
      >
        <div v-if="promote.old_json" class="card">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">
              Prev. Class -
              <span v-if="$root.global.classes">
                {{
                  echoData(
                    "classes",

                    promote.old_json.academic_class_id
                  )
                }}
                / {{ echoData("groups", promote.old_json.group_id) }} ({{
                  echoData("sections", promote.old_json.section_id)
                }})
              </span>
            </h6>
          </div>
          <div class="card-body">
            <table class="table table-sm table-bordered table-striped mb-0">
              <tbody>
                <tr>
                  <th>Software ID</th>
                  <td>
                    {{ promote.old_json.software_id }}
                  </td>
                </tr>
                <tr>
                  <th>Roll Number</th>
                  <td>
                    {{ promote.old_json.profile.roll_number }}
                  </td>
                </tr>
                <tr>
                  <th>Institution</th>
                  <td>
                    {{
                      echoData("institutions", promote.old_json.institution_id)
                    }}
                  </td>
                </tr>
                <tr>
                  <th>Academic Session</th>
                  <td>
                    {{
                      echoData("sessions", promote.old_json.academic_session_id)
                    }}
                  </td>
                </tr>
                <tr>
                  <th>Campus</th>
                  <td>
                    {{ echoData("campuses", promote.old_json.campus_id) }}
                  </td>
                </tr>
                <tr>
                  <th>Shift</th>
                  <td>
                    {{ echoData("shifts", promote.old_json.shift_id) }}
                  </td>
                </tr>
                <tr>
                  <th>Medium/Version</th>
                  <td>
                    {{ echoData("mediums", promote.old_json.medium_id) }}
                  </td>
                </tr>
                <tr>
                  <th>Academic Class</th>
                  <td>
                    {{
                      echoData("classes", promote.old_json.academic_class_id)
                    }}
                  </td>
                </tr>
                <tr>
                  <th>Group</th>
                  <td>
                    {{ echoData("groups", promote.old_json.group_id) }}
                  </td>
                </tr>

                <tr>
                  <th>Section</th>
                  <td>
                    {{ echoData("sections", promote.old_json.section_id) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
// define model name
const model = "student";

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
  methods: {
    echoData(table, id, field = "name") {
      if (this.$root.global) {
        let find = this.$root.global[table].find((e) => e.id == id);
        return find ? find[field] : "";
      }
    },
    deptName(id) {
      let dept = this.$root.global.departments.find((e) => e.id == id);
      return dept ? dept.name : "";
    },
  },
  async created() {
    this.get_data(`${this.model}/${this.$route.params.id}`);
    this.setBreadcrumbs(this.model, "view", null, addOrBack);
  },
};
</script>
