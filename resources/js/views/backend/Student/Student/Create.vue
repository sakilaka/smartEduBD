<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9">
      <div class="row">
        <!-- Academic Information -->
        <div class="col-xl-4 col-12">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">
                <i class="bx bx-chevrons-right"></i> Academic Information
              </h6>
            </div>
            <div class="card-body">
              <!------------ Single Input ------------>
              <SelectCustom
                v-show="!$root.institution_id"
                title="Institution"
                field="institution_id"
                :req="true"
                :datas="$root.global.institutions"
                col="12"
                class="mb-3"
                val="id"
                val_title="name"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                v-show="!data.id"
                title="Institution Category"
                field="institution_category_id"
                :datas="$root.global.institution_categories"
                :req="true"
                col="12"
                class="mb-3"
                val="id"
                val_title="name"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Session"
                field="academic_session_id"
                :datas="
                  sessions_filter(
                    data.id
                      ? data.academic_class?.institution_category_id
                      : data.institution_category_id
                  )
                "
                :req="true"
                col="12"
                class="mb-3"
                val="id"
                val_title="name"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Campus"
                field="campus_id"
                :req="true"
                :datas="campuses_filter(data.institution_id)"
                col="12"
                class="mb-3"
                val="id"
                val_title="name"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Shift"
                field="shift_id"
                :req="true"
                :datas="shift_filter(data.institution_id)"
                col="12"
                class="mb-3"
                val="shift_id"
                val_title="shift_name"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Medium"
                field="medium_id"
                :req="true"
                :datas="medium_filter(data.institution_id)"
                col="12"
                class="mb-3"
                val="medium_id"
                val_title="medium_name"
              /><!------------ Single Input ------------>
              <SelectCustom
                title="Academic Class"
                field="academic_class_id"
                :req="true"
                :datas="class_filter(data.institution_id)"
                col="12"
                class="mb-3"
                val="academic_class_id"
                val_title="class_name"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Group"
                field="group_id"
                :req="true"
                :datas="group_filter(data.institution_id)"
                col="12"
                class="mb-3"
                val="group_id"
                val_title="group_name"
              />
              <!------------ Single Input ------------>
              <SelectCustom
                title="Section"
                field="section_id"
                :req="true"
                :datas="section_filter(data.institution_id)"
                col="12"
                class="mb-3"
                val="section_id"
                val_title="section_name"
              />
            </div>
          </div>

          <!-- family info -->
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">
                <i class="bx bx-chevrons-right"></i> Family Information
              </h6>
            </div>
            <div class="card-body">
              <InputCustom
                label="Father's Name (en)"
                v-model="data.profile.fathers_name_en"
                :error="validation.firstError('data.profile.fathers_name_en')"
                inputClass="mb-3"
              />
              <InputCustom
                label="Father's Name (bn)"
                v-model="data.profile.fathers_name_bn"
                :error="validation.firstError('data.profile.fathers_name_bn')"
                inputClass="mb-3"
              />
              <InputCustom
                label="Father's Mobile"
                v-model="data.profile.fathers_mobile"
                :error="validation.firstError('data.profile.fathers_mobile')"
                inputClass="mb-3"
                type="number"
              />
              <InputCustom
                label="Father's NID/Birth Reg No"
                v-model="data.profile.fathers_nid_or_birth_reg"
                :error="
                  validation.firstError('data.profile.fathers_nid_or_birth_reg')
                "
                inputClass="mb-3"
              />
              <InputCustom
                label="Mother's Name (en)"
                v-model="data.profile.mothers_name_en"
                :error="validation.firstError('data.profile.mothers_name_en')"
                inputClass="mb-3"
              />
              <InputCustom
                label="Mother's Name (bn)"
                v-model="data.profile.mothers_name_bn"
                :error="validation.firstError('data.profile.mothers_name_bn')"
                inputClass="mb-3"
              />
              <InputCustom
                label="Mother's Mobile"
                v-model="data.profile.mothers_mobile"
                :error="validation.firstError('data.profile.mothers_mobile')"
                inputClass="mb-3"
                type="number"
              />
              <InputCustom
                label="Mother's NID/Birth Reg No"
                v-model="data.profile.mothers_nid_or_birth_reg"
                :error="
                  validation.firstError('data.profile.mothers_nid_or_birth_reg')
                "
                inputClass="mb-3"
              />
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
              <div class="col-12">
                <Label title="Profile" :req="false" />

                <div class="row mb-2">
                  <div class="col-2">
                    <img
                      class="img-responsive"
                      :src="fileSrc()"
                      alt="picture"
                      style="height: 31px; width: 40px; border-radius: 3px"
                    />
                  </div>
                  <div class="col-10">
                    <b-form-file
                      accept="image/*"
                      id="profile-file-small"
                      size="sm"
                      name="image"
                      v-on:change="showImage($event)"
                      drop-placeholder="Drop file here"
                    ></b-form-file>
                  </div>
                </div>
              </div>

              <InputCustom
                label="Roll Number"
                v-model="data.profile.roll_number"
                :error="validation.firstError('data.profile.roll_number')"
                required
                type="number"
                inputClass="mb-3"
              />
              <!------------ Single Input ------------>
              <Input
                title="Full Name (en)"
                field="name_en"
                :req="true"
                :col="12"
                class="mb-3"
              />
              <Input
                title="Full Name (bn)"
                field="name_bn"
                :col="12"
                class="mb-3"
              />
              <InputCustom
                label="NID/Birth Reg No"
                v-model="data.profile.nid_or_birth_reg"
                :error="validation.firstError('data.profile.nid_or_birth_reg')"
                inputClass="mb-3"
              />
              <div class="row">
                <!-- Single Input -->
                <div class="col-6 mb-3 pe-0">
                  <Label
                    title="DOB"
                    :req="false"
                    :condition="validation.hasError('data.profile.dob')"
                    :msg="validation.firstError('data.profile.dob')"
                  />
                  <date-picker
                    v-model="data.profile.dob"
                    valueType="format"
                    format="YYYY-MMM-DD"
                    :formatter="momentFormat"
                    placeholder="Select Date"
                  ></date-picker>
                </div>
                <div class="col-6 mb-3">
                  <Label
                    title="Disability"
                    :condition="validation.hasError('data.profile.disability')"
                    :msg="validation.firstError('data.profile.disability')"
                    class="pb-2"
                  />
                  <input
                    v-model="data.profile.disability"
                    type="radio"
                    value="Yes"
                  />
                  Yes &nbsp;&nbsp;
                  <input
                    v-model="data.profile.disability"
                    type="radio"
                    value="No"
                  />
                  No
                </div>

                <!------------ Single Input ------------>
                <div class="col-6 mb-3 pe-0">
                  <Label
                    title="Religion"
                    :req="false"
                    :condition="validation.hasError('data.profile.religion')"
                    :msg="validation.firstError('data.profile.religion')"
                  />
                  <select
                    :class="{
                      'form-invalid': validation.hasError(
                        'data.profile.religion'
                      ),
                    }"
                    v-model="data.profile.religion"
                    class="form-select form-select-sm"
                  >
                    <option :value="null">--Select Any--</option>
                    <option
                      v-for="(value, key, index) in $root.global.religions"
                      :key="index"
                      v-bind:value="value"
                    >
                      {{ value }}
                    </option>
                  </select>
                </div>
                <!------------ Single Input ------------>
                <div class="col-6 mb-3">
                  <Label
                    title="Gender"
                    :req="true"
                    :condition="validation.hasError('data.profile.gender')"
                    :msg="validation.firstError('data.profile.gender')"
                  />
                  <select
                    :class="{
                      'form-invalid': validation.hasError(
                        'data.profile.gender'
                      ),
                    }"
                    v-model="data.profile.gender"
                    class="form-select form-select-sm"
                  >
                    <option :value="null">--Select Any--</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Others">Others</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- guardian info -->
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">
                <i class="bx bx-chevrons-right"></i> Guardian Information
              </h6>
            </div>
            <div class="card-body">
              <div class="col-12 mb-3">
                <Label
                  title="Type"
                  :req="true"
                  :condition="validation.hasError('data.guardian.type')"
                  :msg="validation.firstError('data.guardian.type')"
                  class="pb-2"
                />
                <input
                  v-model="data.guardian.type"
                  type="radio"
                  value="Father"
                />
                Father &nbsp;&nbsp;
                <input
                  v-model="data.guardian.type"
                  type="radio"
                  value="Mother"
                />
                Mother &nbsp;&nbsp;
                <input
                  v-model="data.guardian.type"
                  type="radio"
                  value="Other"
                />
                Other
              </div>
              <InputCustom
                v-if="data.guardian.type == 'Other'"
                :readonly="data.guardian.type != 'Other' ? true : false"
                label="Relations"
                v-model="data.guardian.relations"
                :error="validation.firstError('data.guardian.relations')"
                required
                inputClass="mb-3"
              />
              <InputCustom
                :readonly="data.guardian.type != 'Other' ? true : false"
                label="Name (en)"
                v-model="data.guardian.name_en"
                :error="validation.firstError('data.guardian.name_en')"
                required
                inputClass="mb-3"
              />
              <InputCustom
                :readonly="data.guardian.type != 'Other' ? true : false"
                label="Name (bn)"
                v-model="data.guardian.name_bn"
                :error="validation.firstError('data.guardian.name_bn')"
                required
                inputClass="mb-3"
              />
              <InputCustom
                label="NID/Birth Reg No"
                v-model="data.guardian.nid_or_birth_reg"
                :error="validation.firstError('data.guardian.nid_or_birth_reg')"
                inputClass="mb-3"
              />
              <InputCustom
                label="Email"
                v-model="data.guardian.email"
                :error="validation.firstError('data.guardian.email')"
                inputClass="mb-3"
                type="email"
              />
              <InputCustom
                :readonly="data.guardian.type != 'Other' ? true : false"
                label="Mobile"
                v-model="data.guardian.mobile"
                :error="validation.firstError('data.guardian.mobile')"
                required
                inputClass="mb-3"
                type="number"
              />
              <InputCustom
                label="Password"
                v-model="data.guardian.password"
                :error="validation.firstError('data.guardian.password')"
                required
                inputClass="mb-3"
              />
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="col-xl-4 col-12">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">
                <i class="bx bx-chevrons-right"></i> Contact Information
              </h6>
            </div>
            <div class="card-body">
              <Input title="Email" field="email" :col="12" class="mb-3" />
              <Input
                title="Mobile"
                field="mobile"
                type="number"
                :col="12"
                class="mb-3"
              />
              <Input
                title="Password"
                field="password"
                :req="true"
                :col="12"
                class="mb-3"
              />
              <div class="col-12 mb-3">
                <Label title="Present Address" />
                <textarea
                  v-model="data.profile.present_address"
                  rows="4"
                  class="form-control"
                  placeholder="Vill: , Post: , Upazila: , Dist:"
                ></textarea>
              </div>
              <div class="col-12 mb-3">
                <Label title="Permanent Address" />
                <textarea
                  v-model="data.profile.permanent_address"
                  rows="3"
                  class="form-control"
                  placeholder="Vill: , Post: , Upazila: , Dist:"
                ></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus />
    </div>
  </form>
</template>

<script>
import Textarea from "@components/backend/elements/Form/Textarea";
import File from "@components/backend/elements/Form/File";

// define model name
const model = "student";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File, Textarea },
  data() {
    return {
      model: model,
      submitType: "",
      subjects: [],
      data: {
        status: "active",
        institution_id: null,
        institution_category_id: null,
        academic_session_id: null,
        campus_id: null,
        shift_id: null,
        medium_id: null,
        academic_class_id: null,
        group_id: null,
        section_id: null,
        password: null,
        profile: {
          profile: null,
          religion: null,
          gender: null,
          disability: "No",
        },
        guardian: {
          password: "",
          relations: "",
          name_en: "",
          name_bn: "",
          mobile: "",
          nid_or_birth_reg: "",
        },
      },
      images: { profile: "" },
    };
  },

  methods: {
    submit: function () {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          console.log(this.validation.allErrors());
          this.notify(error, "validate");
          return false;
        }

        // If there is no error
        if (res) {
          var form = document.getElementById("form");
          var formData = new FormData(form);
          formData.append("data", JSON.stringify(this.data));

          if (this.data.id) {
            this.update(this.model, formData, this.data.id, "image");
          } else {
            this.store(this.model, formData, this.submitType);
          }
        }
      });
    },

    showImage(e) {
      let files = e.target.files || e.dataTransfer.files;
      if (files.length) {
        this.data.profile.profile = URL.createObjectURL(e.target.files[0]);
      }
    },

    fileSrc() {
      return this.data.profile.profile
        ? this.data.profile.profile
        : this.$root.noimage;
    },

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      } else {
        this.data.password = "123456";
        this.data.guardian.password = "123456";
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
    },
  },

  mounted() {
    if (this.$root.institution_id) {
      this.data.institution_id = this.$root.institution_id;
    }
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
    "data.guardian.type": function (val) {
      if (val == "Father") {
        this.data.guardian.name_en = this.data.profile.fathers_name_en;
        this.data.guardian.name_bn = this.data.profile.fathers_name_bn;
        this.data.guardian.mobile = this.data.profile.fathers_mobile;
        this.data.guardian.nid_or_birth_reg =
          this.data.profile.fathers_nid_or_birth_reg;
        this.data.guardian.relations = val;
      } else if (val == "Mother") {
        this.data.guardian.name_en = this.data.profile.mothers_name_en;
        this.data.guardian.name_bn = this.data.profile.mothers_name_bn;
        this.data.guardian.mobile = this.data.profile.mothers_mobile;
        this.data.guardian.nid_or_birth_reg =
          this.data.profile.mothers_nid_or_birth_reg;
        this.data.guardian.relations = val;
      } else {
        if (!this.data.id) {
          this.data.guardian.name_en = "";
          this.data.guardian.name_bn = "";
          this.data.guardian.relations = "";
          this.data.guardian.mobile = "";
          this.data.guardian.nid_or_birth_reg = "";
        }
      }
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "data.institution_category_id": function (value = null) {
      if (!this.data.id) {
        return Validator.value(value).required(
          "Institution category is required"
        );
      }
      return Validator.value(value);
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
    "data.name_en": function (value = null) {
      return Validator.value(value).required("Name (en) is required");
    },
    "data.mobile": function (value = null) {
      return Validator.value(value)
        .digit()
        .regex("01+[0-9+-]*$", "Must start with 01.")
        .minLength(11)
        .maxLength(11);
    },
    "data.profile.roll_number": function (value = null) {
      return Validator.value(value).required("Father's Name (en) is required");
    },
    "data.profile.fathers_mobile": function (value = null) {
      return Validator.value(value)
        .digit()
        .regex("01+[0-9+-]*$", "Must start with 01.")
        .minLength(11)
        .maxLength(11);
    },
    "data.profile.mothers_mobile": function (value = null) {
      return Validator.value(value)
        .digit()
        .regex("01+[0-9+-]*$", "Must start with 01.")
        .minLength(11)
        .maxLength(11);
    },
    "data.profile.gender": function (value = null) {
      return Validator.value(value).required("Gender is required");
    },
    "data.guardian.type": function (value = null) {
      return Validator.value(value).required("Type is required");
    },
    "data.guardian.name_en": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.guardian.relations": function (value = null) {
      return Validator.value(value).required("Relations is required");
    },
    "data.guardian.mobile": function (value = null) {
      return Validator.value(value)
        .required("Mobile is required")
        .digit()
        .regex("01+[0-9+-]*$", "Must start with 01.")
        .minLength(11)
        .maxLength(11);
    },
    "data.guardian.password": function (value = null) {
      if (!this.$route.params.id) {
        return Validator.value(value).required("Password is required");
      }
    },
  },
};
</script>