<template>
  <div v-if="!$root.spinner" class="col-xl-12" id="printArea">
    <div class="card border">
      <div class="card-header">
        <div class="row">
          <div class="col-6">
            <h6 class="mb-0 text-uppercase">Search Students For Discount</h6>
          </div>
          <div class="col-6 text-end p-none">
            <button
              @click="print('printArea', 'Promotion')"
              class="btn btn-xs btn-success"
            >
              <i class="bx bx-printer"></i> PRINT
            </button>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div
          class="p-show"
          style="display: none"
          v-if="students && Object.keys(students).length > 0"
        >
          <table class="table table-striped table-sm">
            <tbody>
              <tr>
                <th style="width: 8%">Institution</th>
                <td style="width: 22%">
                  {{
                    findData(
                      $root.global.institutions,
                      "institution_id",
                      "name",
                      "id"
                    )
                  }}
                </td>
                <th style="width: 8%">Session</th>
                <td style="width: 30%">
                  {{
                    findData(
                      sessions_filter(
                        search_data.institution_category_id,
                        "search_data"
                      ),
                      "academic_session_id",
                      "name",
                      "id"
                    )
                  }}
                </td>
                <th style="width: 8%">Accont Head</th>
                <td style="width: 25%">
                  {{ findData(feesLists, "account_head_id", "name_en", "id") }}
                </td>
              </tr>
              <tr>
                <th>Campus</th>
                <td>
                  {{
                    findData(
                      campuses_filter(
                        search_data.institution_id,
                        "search_data"
                      ),
                      "campus_id",
                      "name",
                      "id"
                    )
                  }}
                </td>
                <th>Shift</th>
                <td>
                  {{
                    findData(
                      shift_filter(search_data.institution_id, "search_data"),
                      "shift_id",
                      "shift_name"
                    )
                  }}
                </td>
                <th>Medium</th>
                <td>
                  {{
                    findData(
                      medium_filter(search_data.institution_id, "search_data"),
                      "medium_id",
                      "medium_name"
                    )
                  }}
                </td>
              </tr>
              <tr>
                <th>Class</th>
                <td>
                  {{
                    findData(
                      class_filter(search_data.institution_id, "search_data"),
                      "academic_class_id",
                      "class_name"
                    )
                  }}
                </td>
                <th>Group</th>
                <td>
                  {{
                    findData(
                      group_filter(search_data.institution_id, "search_data"),
                      "group_id",
                      "group_name"
                    )
                  }}
                </td>
                <th>Section</th>
                <td>
                  {{
                    findData(
                      section_filter(search_data.institution_id, "search_data"),
                      "section_id",
                      "section_name"
                    )
                  }}
                </td>
              </tr>

              <slot></slot>
            </tbody>
          </table>
        </div>

        <form v-on:submit.prevent="search" class="col-12 row p-none">
          <!------------ Single Input ------------>
          <SelectSearch
            v-if="!$root.institution_id"
            title="All Institution"
            field="institution_id"
            :req="true"
            :datas="$root.global.institutions"
            val="id"
            val_title="name"
            class="mb-2"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Institution Category"
            field="institution_category_id"
            :req="true"
            :datas="$root.global.institution_categories"
            val="id"
            val_title="name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Session"
            field="academic_session_id"
            :req="true"
            :datas="
              sessions_filter(
                search_data.institution_category_id,
                'search_data'
              )
            "
            val="id"
            val_title="name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Campus"
            field="campus_id"
            :req="true"
            :datas="campuses_filter(search_data.institution_id, 'search_data')"
            val="id"
            val_title="name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Shift"
            field="shift_id"
            :req="true"
            :datas="shift_filter(search_data.institution_id, 'search_data')"
            val="shift_id"
            val_title="shift_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Medium"
            field="medium_id"
            :req="true"
            :datas="medium_filter(search_data.institution_id, 'search_data')"
            val="medium_id"
            val_title="medium_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Class"
            field="academic_class_id"
            :req="true"
            :datas="
              category_classes_filter(
                search_data.institution_id,
                search_data.institution_category_id,
                'search_data'
              )
            "
            val="academic_class_id"
            val_title="class_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="Group"
            field="group_id"
            :req="true"
            :datas="group_filter(search_data.institution_id, 'search_data')"
            val="group_id"
            val_title="group_name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="Section"
            field="section_id"
            :req="true"
            :datas="section_filter(search_data.institution_id, 'search_data')"
            val="section_id"
            val_title="section_name"
          />

          <!------------ Single Input ------------>
          <div class="col-6 col-xl-2 pe-0">
            <select
              v-model="search_data.discount_type"
              class="form-select form-select-sm"
            >
              <option value="">All Student</option>
              <option value="1">Discount Student</option>
            </select>
          </div>

          <Search :fields_name="fields_name" :print_excel="false" />
        </form>
      </div>
    </div>

    <!-- Student List -->
    <!-- Student List -->
    <div class="card border">
      <div class="card-header">
        <div class="row">
          <div class="col-6">
            <h6 class="mb-0 text-uppercase">Students</h6>
          </div>

          <div class="col-6 d-flex justify-content-end">
            <div class="col-6 col-xl-5 pe-0 p-none">
              <select
                v-model="search_data.account_head_id"
                class="form-select form-select-sm"
              >
                <option value="">--Account Head--</option>
                <option
                  v-for="(fees, key) in feesLists"
                  :key="key"
                  :value="fees.id"
                >
                  {{ fees.name_en }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body p-3">
        <div class="row g-3">
          <table
            v-if="students && Object.keys(students).length > 0"
            class="table table-bordered table-hover"
          >
            <thead>
              <tr>
                <th class="text-center">#</th>
                <th class="text-center">Software ID</th>
                <th>Student Name</th>
                <th>Roll Number</th>
                <th>Guardian</th>
                <th class="text-center">Discount</th>
                <th class="text-center">Discount Given</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(student, key) in students" :key="key">
                <td class="text-center">{{ key + 1 }}</td>
                <td class="text-center">{{ student.software_id }}</td>
                <td>{{ student.name_en }}</td>
                <td>
                  {{ student.profile ? student.profile.roll_number : "" }}
                </td>
                <td>
                  {{ student.guardian ? student.guardian.name_en : "" }}
                  <br />
                  {{ student.guardian ? student.guardian.mobile : "" }}
                </td>
                <td class="d-flex justify-content-center">
                  <input
                    type="number"
                    class="form-control form-control-sm text-center"
                    style="width: 120px"
                    v-model="student.discount"
                    placeholder="ex: 1000"
                    v-on:keyup.enter="
                      discountStudent(student.id, $event.target.value)
                    "
                  />
                </td>
                <td class="text-center">
                  <ul
                    class="mb-0"
                    v-if="
                      student.discounts &&
                      Object.keys(student.discounts).length > 0
                    "
                  >
                    <template v-for="(dis, key1) in student.discounts">
                      <li
                        v-if="
                          search_data.account_head_id == dis.account_head_id
                        "
                        style="list-style: none"
                        :key="key1"
                      >
                        <b>{{ dis.amount }}</b>
                      </li>
                    </template>
                  </ul>
                </td>
              </tr>
            </tbody>
          </table>

          <span class="text-center my-5" v-else> No Students Found </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const model = "student";

const breadcrumb = [{ route: "student.discount", title: "Payment Discount" }];

export default {
  data() {
    return {
      model: model,
      breadcrumb: breadcrumb,
      checkAll: 1,
      fields_name: {
        0: "Select One",
        software_id: "Software ID",
        name_en: "Name (en)",
        mobile: "Student Mobile",
        college_roll: "Roll",
        guardian_mobile: "Guardian Mobile",
      },
      search_data: {
        field_name: "software_id",
        value: "",
        institution_id: "",
        institution_category_id: "",
        academic_session_id: "",
        campus_id: "",
        medium_id: "",
        group_id: "",
        section_id: "",
        shift_id: "",
        academic_class_id: "",
        discount_type: "",
        account_head_id: "",
        status: "active",
        discountStudent: true,
        allData: true,
      },
      students: [],
      feesLists: [],
    };
  },
  watch: {
    checkAll: function (val) {
      this.students.map((e) => (e.check = val));
    },
  },
  methods: {
    discountStudent: function (studentID, discount) {
      if (!this.search_data.account_head_id) {
        this.notify("Please select account head", "error");
        return false;
      }

      if (confirm("Are you sure want to discount this student?")) {
        this.$root.submit = true;

        let data = {
          student_id: studentID,
          discount: discount,
          account_head_id: this.search_data.account_head_id,
        };
        axios
          .post("/student-discount", data)
          .then((res) => {
            this.asyncData();
            this.notify(res.data.message, "success");
          })
          .finally((e) => (this.$root.submit = false));
      }
    },

    // Search Students
    search() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          // this.notify("Please select all search criteria", "error");
          return false;
        }
        this.asyncData();
      });
    },

    findData(data, findId = "id", fieldName = "name", find_extra_Id = "") {
      let result = "";
      if (data && findId) {
        let field_value = this.search_data[findId];
        find = data.find(
          (e) => e[find_extra_Id ? find_extra_Id : findId] == field_value
        );
        result = find ? find[fieldName] : "";
      }
      return result;
    },

    asyncData() {
      this.$root.tableSpinner = true;
      this.get_data(`${this.model}`, "students", this.search_data, false);
      this.get_data(`fees-lists`, "feesLists", this.search_data, false);
    },
  },

  created() {
    let obj = { addOrBack: {}, breadcrumb: breadcrumb };
    breadcrumbs.dispatch("setBreadcrumbs", obj);
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
    }
  },

  // ================== validation rule for form ==================
  validators: {
    "search_data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "search_data.institution_category_id": function (value = null) {
      return Validator.value(value).required(
        "Institution category is required"
      );
    },
    "search_data.academic_session_id": function (value = null) {
      return Validator.value(value).required("Academic session is required");
    },
    "search_data.campus_id": function (value = null) {
      return Validator.value(value).required("Campus is required");
    },
    "search_data.shift_id": function (value = null) {
      return Validator.value(value).required("Shift is required");
    },
    "search_data.medium_id": function (value = null) {
      return Validator.value(value).required("Medium is required");
    },
    "search_data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "search_data.group_id": function (value = null) {
      return Validator.value(value).required("Group is required");
    },
    "search_data.section_id": function (value = null) {
      return Validator.value(value).required("Section is required");
    },
  },
};
</script>