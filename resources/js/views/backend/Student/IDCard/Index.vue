<template>
  <div class="col-xl-12">
    <div class="card border">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">Search Students</h6>
      </div>
      <div class="card-body p-3">
        <div class="row">
          <!------------ Single Input ------------>
          <SelectSearch
            v-if="!$root.institution_id"
            title="All Institution"
            field="institution_id"
            :req="true"
            :datas="$root.global.institutions"
            val="id"
            val_title="name"
          />
          <!------------ Single Input ------------>
          <SelectSearch
            title="All Campus"
            field="campus_id"
            :req="true"
            :datas="campuses_filter(search_data.institution_id)"
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
            :datas="class_filter(search_data.institution_id, 'search_data')"
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

          <div class="w-100 my-1"></div>

          <!------------ Single Input ------------>
          <SelectSearch
            title="Section"
            field="section_id"
            :req="true"
            :datas="section_filter(search_data.institution_id, 'search_data')"
            val="section_id"
            val_title="section_name"
            col="2"
          />

          <!------------ Single Input ------------>
          <div class="col-6 col-xl-2 pe-0">
            <date-picker
              v-model="expired_date"
              valueType="format"
              format="YYYY-MMM-DD"
              :formatter="momentFormat"
              placeholder="Validity Date"
            ></date-picker>
          </div>
          <!------------ Single Input ------------>
          <div class="col-6 col-xl-1 pe-0">
            <select
              v-model="search_data.skip"
              class="form-select form-select-sm"
            >
              <option :value="0">100</option>
              <option :value="100">200</option>
              <option :value="200">300</option>
              <option :value="300">400</option>
              <option :value="400">500</option>
              <option :value="500">600</option>
              <option :value="600">700</option>
              <option :value="700">800</option>
              <option :value="800">900</option>
              <option :value="900">1000</option>
              <option :value="1000">1100</option>
              <option :value="1100">1200</option>
              <option :value="1200">1300</option>
              <option :value="1300">1400</option>
              <option :value="1400">1500</option>
              <option :value="1500">1600</option>
              <option :value="1600">1700</option>
              <option :value="1700">1800</option>
              <option :value="1800">1900</option>
              <option :value="1900">2000</option>
            </select>
          </div>

          <div class="col-6 col-xl-1 pe-0">
            <select
              v-model="search_data.order_by"
              class="form-select form-select-sm"
            >
              <option value="asc">ASC</option>
              <option value="desc">DESC</option>
            </select>
          </div>
          <div class="col-6 col-xl-1 pe-0">
            <input
              type="text"
              v-model="search_data.take"
              placeholder="Take"
              class="form-control form-control-sm"
            />
          </div>
          <!------------ Single Input ------------>
          <div class="col-6 col-xl-1 pe-0">
            <select
              v-model="search_data.field_name"
              class="form-select form-select-sm"
            >
              <option
                v-for="(field, key) in fields_name"
                :key="key"
                :value="key"
              >
                {{ field }}
              </option>
            </select>
          </div>
          <!------------ Single Input ------------>
          <div class="col-6 col-xl-2 ps-0 pe-0">
            <input
              type="text"
              placeholder="Type your text"
              v-model="search_data.value"
              class="form-control form-control-sm"
            />
          </div>
          <!------------ Button ------------>
          <div class="col-1 ps-0">
            <button
              type="button"
              :disabled="$root.submit ? true : false"
              @click="searchStudent()"
              class="btn btn-sm btn-success"
            >
              <span v-if="$root.submit">
                <span class="spinner-border spinner-border-sm"></span>
              </span>
              <span v-else> <i class="bx bx-search mx-0 fw-bold"></i></span>
            </button>
          </div>
          <a
            v-if="data.students.length > 0"
            class="btn btn-sm btn-success"
            @click="print('id-card', 'idcard')"
            href="javascript:;"
            style="width: 100px; margin-right: 0; margin-left: auto"
          >
            <i class="bx bx-printer"></i> PRINT
          </a>
        </div>
      </div>
    </div>

    <!-- Student List -->
    <!-- Student List -->
    <div class="col-12" v-if="!$root.spinner" id="id-card">
      <div class="id-card">
        <template v-for="(student, key1) in data.students">
          <div class="id-bg" :key="'abc' + key1 + 1">
            <div class="id-front">
              <img
                v-if="data.institution.idcard_front_part"
                :src="data.institution.idcard_front_part"
              />
              <div class="profile-image">
                <img v-if="student.profile" :src="student.profile.profile" />
              </div>
              <div class="std-name">
                {{ student.name_en }}
              </div>
              <div class="front-info">
                <p>{{ student.academic_class_name }}</p>
                <p>{{ student.roll_number }}</p>
                <p>{{ student.software_id }}</p>
                <p>
                  {{ student.fathers_name_en.substr(0, 16) }}
                </p>
                <p>
                  {{ student.mothers_name_en.substr(0, 16) }}
                </p>
                <p>{{ student.guardian_mobile }}</p>
              </div>
            </div>

            <div class="id-back">
              <img
                v-if="data.institution.idcard_back_part"
                :src="data.institution.idcard_back_part"
              />

              <div class="back-info">
                <p>{{ student.blood_group ? student.blood_group : "--" }}</p>
                <p>{{ student.dob ? student.dob : "--" }}</p>
                <p class="expired_date">
                  {{ expired_date ? expired_date : "--" | formatDate }}
                </p>
              </div>

              <div v-if="student.qr_code" class="qr-code">
                <img :src="'data:image/png;base64, ' + student.qr_code" />
              </div>
            </div>
          </div>
          <div
            class="page-break"
            v-if="(key1 + 1) % 5 == 0"
            :key="'cdf' + key1 + 1"
          ></div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
// define model name
const model = "student";

export default {
  data() {
    return {
      model: model,
      checkAll: 1,
      expired_date: "",
      fields_name: {
        0: "Select One",
        software_id: "Software ID",
        name_en: "Name",
        roll_number: "Roll Number",
      },
      search_data: {
        custom_rolls: 0,
        roll_lists: "",
        skip: 0,
        take: 100,
        field_name: "software_id",
        value: "",
        order_by: "asc",
        institution_id: "",
        campus_id: "",
        medium_id: "",
        group_id: "",
        section_id: "",
        shift_id: "",
        academic_class_id: "",
      },

      data: {
        institution: {},
        students: [],
      },
    };
  },
  watch: {
    checkAll: function (val) {
      this.data.students.map((e) => (e.check = val));
    },
  },
  methods: {
    search() {},
    // Search Students
    searchStudent() {
      // if suctom search
      if (this.search_data.custom_rolls) {
        this.$root.submit = true;
        this.get_data("student-idcard", "data", this.search_data);
        setTimeout(() => (this.$root.submit = false), 1000);
        return false;
      }

      // without custom search
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        if (error > 0) {
          this.notify("Please select search criteria", "error");
          return false;
        }
        this.$root.submit = true;
        this.get_data("student-idcard", "data", this.search_data);
        setTimeout(() => (this.$root.submit = false), 1000);
      });
    },
  },

  created() {
    const breadcrumb = [
      {
        route: "idcard.index",
        title: "Studet ID Card",
      },
    ];
    let obj = {
      addOrBack: {},
      breadcrumb: breadcrumb,
    };
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

<style>
.id-card {
  width: 100%;
  float: left;
  position: relative;
}
.id-bg {
  position: relative;
  height: 658.5px;
  width: 208.2px;
  float: left;
  clear: right;
}
.id-card img {
  height: 100%;
  width: 100%;
}
.id-card .id-front {
  position: relative;
  padding: 0px;
  float: left;
  height: 328.27px;
}
.id-card .id-front .std-name {
  position: absolute;
  top: 166px;
  text-align: center;
  margin: auto;
  width: 100%;
  font-weight: bold;
  font-size: 11px;
  padding: 0px 10px;
  height: 40px;
  display: flex;
  justify-content: center;
  align-items: center;
}
.id-card .id-front .front-info {
  position: absolute;
  top: 204px;
  left: 92px;
  font-size: 10px;
  color: #000;
}
.id-card .id-front .front-info p {
  margin-bottom: -2.1px !important;
}

.id-card .id-front .front-info .dept-name {
  font-size: 7px;
  margin-top: 3px;
  margin-bottom: 0px !important;
}
.id-card .id-front .profile-image img {
  position: absolute;
}
.id-card .id-front .profile-image img {
  height: 88.4px;
  width: 78px;
  top: 80.3px;
  left: 65.7px;
  border-radius: 7px;
}

/* back info */
.id-card .id-back {
  position: relative;
  height: 100%;
  padding: 0px;
  float: left;
  height: 328.27px;
  margin-top: 5px;
  /* transform: rotate(-180deg); */
}

.id-card .id-back .back-info {
  position: absolute;
  top: 33.5px;
  left: 90px;
  font-size: 9px;
  color: #000;
}

.id-card .id-back .back-info p {
  margin-bottom: -1px !important;
}

.id-card .id-back .back-info .fathers-name {
  height: 27px;
  font-size: 9px;
}

.id-card .id-back .qr-code {
  position: absolute;
  left: 72px;
  top: 81px;
}
.id-card .id-back .qr-code img {
  height: 66px;
  width: 66px;
}
.expired_date {
  color: red;
  font-weight: bold;
}

@media print {
  /* .id-card .id-back {
    transform: rotate(-180deg);
  } */
  .page-break {
    page-break-after: always;
  }
}
</style>
