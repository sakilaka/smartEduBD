<template>
  <form v-on:submit.prevent="submit" id="form">
    <!-- Search -->
    <div class="col-xl-12">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            <i class="bx bx-chevrons-right"></i>Search Promoted Class
          </h6>
        </div>
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectSearch
              :req="true"
              v-if="!$root.institution_id"
              title="Select Institution"
              field="institution_id"
              :datas="$root.global.institutions"
              val="id"
              val_title="name"
            />
            <SelectSearch
              title="Institution Category"
              field="institution_category_id"
              :req="true"
              :datas="$root.global.institution_categories"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Select Session"
              field="academic_session_id"
              :datas="
                sessions_filter(
                  search_data.institution_category_id,
                  'search_data'
                )
              "
              :req="true"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Select Campus"
              field="campus_id"
              :datas="
                campuses_filter(search_data.institution_id, 'search_data')
              "
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Select Shift"
              field="shift_id"
              :req="true"
              :datas="shift_filter(search_data.institution_id, 'search_data')"
              val="shift_id"
              val_title="shift_name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Select Medium"
              field="medium_id"
              :req="true"
              :datas="medium_filter(search_data.institution_id, 'search_data')"
              val="medium_id"
              val_title="medium_name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Select Class"
              field="academic_class_id"
              :datas="
                category_classes_filter(
                  search_data.institution_id,
                  search_data.institution_category_id,
                  'search_data'
                )
              "
              val="academic_class_id"
              val_title="class_name"
              :req="true"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Select Group"
              field="group_id"
              :req="true"
              :datas="group_filter(search_data.institution_id, 'search_data')"
              val="group_id"
              val_title="group_name"
            />
            <!------------ Single Input ------------>
            <SelectSearch
              title="Select Section"
              field="section_id"
              :req="true"
              :datas="section_filter(search_data.institution_id, 'search_data')"
              val="section_id"
              val_title="section_name"
            />

            <div class="col-1">
              <button
                @click="searchStudent()"
                type="button"
                class="btn btn-sm btn-info"
              >
                <span
                  v-if="spinner"
                  class="spinner-border spinner-border-sm"
                ></span>
                <span v-else> <i class="bx bx-search"></i> </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Result -->
    <div class="col-xl-12">
      <div class="card border">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">
                <i class="bx bx-chevrons-right"></i>Student List
              </h6>
            </div>

            <div class="col-6 text-end">
              <button
                v-if="Object.keys(this.students).length > 0"
                type="button"
                :disabled="$root.submit ? true : false"
                class="btn btn-success btn-sm px-3"
                @click="submit()"
              >
                <span v-if="$root.submit">
                  <span class="spinner-border spinner-border-sm"></span>
                  <span>processing...</span>
                </span>
                <span v-else> <i class="bx bx-save"></i> SUBMIT</span>
              </button>
            </div>
          </div>
        </div>

        <div class="card-body p-3">
          <template v-if="Object.keys(students).length > 0 && !spinner">
            <div class="row g-3">
              <!------------ Single Input ------------>
              <SelectCustom
                v-show="!$root.institution_id"
                title="Select Institution"
                field="institution_id"
                :datas="$root.global.institutions"
                val="id"
                val_title="name"
                col="2"
                :req="true"
              />
              <SelectCustom
                title="Institution Category"
                field="institution_category_id"
                :req="true"
                :datas="$root.global.institution_categories"
                val="id"
                val_title="name"
                col="2"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Session"
                field="academic_session_id"
                :datas="sessions_filter(data.institution_category_id)"
                :req="true"
                val="id"
                val_title="name"
                col="2"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Campus"
                field="campus_id"
                :datas="campuses_filter(data.institution_id)"
                val="id"
                val_title="name"
                col="2"
                :req="true"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Shift"
                field="shift_id"
                :req="true"
                :datas="shift_filter(data.institution_id)"
                val="shift_id"
                val_title="shift_name"
                col="2"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Medium"
                field="medium_id"
                :req="true"
                :datas="medium_filter(data.institution_id)"
                val="medium_id"
                val_title="medium_name"
                col="2"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Class"
                field="academic_class_id"
                :datas="
                  category_classes_filter(
                    data.institution_id,
                    data.institution_category_id
                  )
                "
                val="academic_class_id"
                val_title="class_name"
                col="2"
                :req="true"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Group"
                field="group_id"
                :req="true"
                :datas="group_filter(data.institution_id)"
                val="group_id"
                val_title="group_name"
                col="2"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Select Section"
                field="section_id"
                :req="true"
                :datas="section_filter(data.institution_id)"
                val="section_id"
                val_title="section_name"
                col="2"
              />

              <div class="w-100"></div>
              <!-- Student List -->
              <table
                class="table table-sm table-bordered table-striped table-hover"
              >
                <thead>
                  <tr>
                    <th class="text-center">#</th>
                    <th class="text-center">
                      <input
                        type="checkbox"
                        v-model="check_all"
                        :value="true"
                      />
                    </th>
                    <th class="text-center">Software ID</th>
                    <th>Student Name</th>
                    <th class="text-center">Present Roll Number</th>
                    <th class="text-center">New Roll Number</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(std, key) in students" :key="key">
                    <th class="text-center">{{ key + 1 }}</th>
                    <td class="text-center">
                      <input type="checkbox" v-model="std.check" value="1" />
                    </td>
                    <td class="text-center">{{ std.software_id }}</td>
                    <td>{{ std.name_en }}</td>
                    <td class="text-center">{{ std.profile?.roll_number }}</td>
                    <td class="d-flex justify-content-center">
                      <input
                        type="number"
                        placeholder="Roll No."
                        v-model.number="std.new_roll_number"
                        class="form-control form-control-sm text-center"
                        style="width: 100px"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </template>
          <div
            v-if="Object.keys(students).length == 0 && !spinner"
            class="col-12 text-center my-4"
          >
            No students found
          </div>

          <div v-if="spinner" class="col-12 text-center my-4">
            <span class="spinner-border spinner-border-sm"></span>
            <span>processing...</span>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
// define model name
const model = "studentPromotion";

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
      submitType: model + ".create",
      spinner: false,
      check_all: false,
      students: {},
      search_data: {
        institution_category_id: "",
        academic_session_id: "",
        institution_id: "",
        campus_id: "",
        medium_id: "",
        group_id: "",
        section_id: "",
        shift_id: "",
        academic_class_id: "",
      },
      data: {
        institution_category_id: null,
        academic_session_id: null,
        institution_id: null,
        campus_id: null,
        medium_id: null,
        group_id: null,
        section_id: null,
        shift_id: null,
        academic_class_id: null,
      },
    };
  },
  watch: {
    check_all(val) {
      if (Object.keys(this.students).length > 0) {
        this.students.forEach((el) => {
          el.check = val;
        });
      }
    },
  },

  methods: {
    search() {
      return false;
    },

    submit: function () {
      if (Object.keys(this.students).length == 0) {
        this.notify("Please select student", "error");
        return false;
      }
      let filterStudent = this.students.filter(
        (e) => e.check && e.new_roll_number
      );
      if (Object.keys(filterStudent).length == 0) {
        this.notify("Please select student and give new roll number", "error");
        return false;
      }

      if (
        this.search_data.group_id == this.data.group_id &&
        this.search_data.section_id == this.data.section_id &&
        this.search_data.academic_class_id == this.data.academic_class_id &&
        this.search_data.academic_session_id == this.data.academic_session_id
      ) {
        this.notify(
          "You cannot promote same session/class/group and section",
          "error"
        );
        return false;
      }

      let promoted_ids = this.students
        .filter((e) => e.check && e.new_roll_number)
        .map((e) => e.id);

      let promoted_rolls = this.students
        .filter((e) => e.check && e.new_roll_number)
        .reduce((acc, e) => {
          acc[e.id] = e.new_roll_number;
          return acc;
        }, {});

      let irregular_ids = this.students
        .filter((e) => !e.check || !e.new_roll_number)
        .map((e) => e.id);

      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          this.data.promoted_ids = promoted_ids;
          this.data.promoted_rolls = promoted_rolls;
          this.data.irregular_ids = irregular_ids;
          this.store(this.model, this.data, "no");
          setTimeout(() => (this.students = []), 1000);
        }
      });
    },

    // Search
    searchStudent() {
      for (const key in this.search_data) {
        if (!this.search_data[key]) {
          this.notify("Please select search criteria", "error");
          return false;
        }
      }

      if (this.spinner) return false;

      this.search_data.allData = true;
      this.spinner = true;
      axios
        .get("student", { params: this.search_data })
        .then((res) => {
          this.students = res.data;
          if (Object.keys(res.data).length > 0) {
            this.students.forEach((el) => {
              el.check = true;
              el.new_reg_no = el.reg_no;
              el.new_admission_id = el.admission_id;
            });
            this.check_all = true;
          }
        })
        .finally((alw) => (this.spinner = false));
    },
  },
  created() {
    this.setBreadcrumbs(this.model, "create", "Student Promotion", addOrBack);
  },

  mounted() {
    if (this.$root.institution_id) {
      this.search_data.institution_id = this.$root.institution_id;
      this.data.institution_id = this.$root.institution_id;
    }
  },

  // ================== validation rule for form ==================
  validators: {
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "data.institution_category_id": function (value = null) {
      return Validator.value(value).required(
        "Institution category is required"
      );
    },
    "data.academic_session_id": function (value = null) {
      return Validator.value(value).required("Academic session is required");
    },
    "data.campus_id": function (value = null) {
      return Validator.value(value).required("Campus is required");
    },
    "data.shift_id": function (value = null) {
      return Validator.value(value).required("Shift is required");
    },
    "data.medium_id": function (value = null) {
      return Validator.value(value).required("Medium is required");
    },
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "data.group_id": function (value = null) {
      return Validator.value(value).required("Group is required");
    },
    "data.section_id": function (value = null) {
      return Validator.value(value).required("Section is required");
    },
  },
};
</script>