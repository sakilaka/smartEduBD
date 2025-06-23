<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-12">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">
            <i class="bx bx-chevrons-right"></i> Academic Information
          </h6>
        </div>
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <SelectCustom
              v-show="!$root.institution_id"
              title="Institution"
              field="institution_id"
              :req="true"
              :datas="$root.global.institutions"
              col="4"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Medium"
              field="medium_id"
              :req="true"
              :datas="$root.global.mediums"
              col="4"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Academic Class"
              field="academic_class_id"
              :req="true"
              :datas="classes"
              col="4"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <Textarea title="Note" :fixheight="true" field="note" :col="12" />
          </div>
        </div>
      </div>

      <div class="card border">
        <div class="card-header">
          <div class="row">
            <div class="col-6">
              <h6 class="mb-0 text-uppercase">
                <i class="bx bx-chevrons-right"></i> Subjects
              </h6>
            </div>
            <div class="col-6">
              <button
                type="button"
                :disabled="$root.submit ? true : false"
                class="btn btn-success btn-sm px-3 float-end"
                @click="submit()"
              >
                <span v-if="$root.submit">
                  <span class="spinner-border spinner-border-sm"></span>
                  <span>processing...</span>
                </span>
                <span v-else> <i class="bx bx-save"></i> Save</span>
              </button>
            </div>
          </div>
        </div>

        <div class="card-body p-3">
          <table class="table table-sm table-bordered table-striped mb-0">
            <thead>
              <tr>
                <th>Subject / Combined Subject</th>
                <th style="width: 70px" class="text-center">4th Sub.</th>
                <th class="text-center" style="width: 130px">CT Mark / Pass</th>
                <th class="text-center" style="width: 130px">CQ Mark / Pass</th>
                <th class="text-center" style="width: 130px">
                  MCQ Mark / Pass
                </th>
                <th class="text-center" style="width: 130px">
                  Prac. Mark / Pass
                </th>
                <th class="text-center" style="width: 80px">Convert Mark</th>
                <th class="text-center" style="width: 60px">Sorting</th>
                <th style="width: 80px"></th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(subject, key) in data.details" :key="key">
                <td>
                  <div class="d-flex justify-content-between">
                    <div class="w-100 me-2">
                      <v-select
                        v-model="subject.subject_id"
                        label="name"
                        :reduce="(obj) => obj.id"
                        :options="subjects"
                        placeholder="--Select One--"
                        :closeOnSelect="true"
                        :class="
                          validation.hasError('data.subject_id')
                            ? 'required'
                            : ''
                        "
                        class="mb-2"
                      ></v-select>
                    </div>
                    <div class="w-100">
                      <v-select
                        v-model="subject.combined_subject_id"
                        label="name"
                        :reduce="(obj) => obj.id"
                        :options="subjects"
                        placeholder="--Combined Subject--"
                        :closeOnSelect="true"
                        :class="
                          validation.hasError('data.combined_subject_id')
                            ? 'required'
                            : ''
                        "
                      ></v-select>
                    </div>
                  </div>
                </td>
                <td class="text-center">
                  <label for="yes">
                    <input
                      type="radio"
                      v-model="subject.fourth_subject"
                      value="1"
                      id="yes"
                    />
                    Yes
                  </label>
                  <label for="no">
                    <input
                      type="radio"
                      v-model="subject.fourth_subject"
                      value="0"
                      id="no"
                    />
                    No</label
                  >
                </td>
                <td>
                  <div class="d-flex justify-content-between">
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.ct_mark"
                    />
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.ct_pass_mark"
                    />
                  </div>
                </td>
                <td>
                  <div class="d-flex justify-content-between">
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.cq_mark"
                    />
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.cq_pass_mark"
                    />
                  </div>
                </td>
                <td>
                  <div class="d-flex justify-content-between">
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.mcq_mark"
                    />
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.mcq_pass_mark"
                    />
                  </div>
                </td>
                <td>
                  <div class="d-flex justify-content-between">
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.practical_mark"
                    />
                    <input
                      class="form-control form-control-sm text-center"
                      type="text"
                      v-model="subject.practical_pass_mark"
                    />
                  </div>
                </td>
                <td>
                  <input
                    class="form-control form-control-sm text-center"
                    type="text"
                    v-model="subject.converted_mark"
                  />
                </td>
                <td>
                  <input
                    class="form-control form-control-sm text-center"
                    type="number"
                    v-model="subject.sorting"
                  />
                </td>
                <td class="text-center">
                  <i
                    v-if="Object.keys(data.details).length > 1"
                    @click="data.details.splice(key, 1)"
                    class="bx bx-minus btn btn-xs btn-danger"
                  >
                  </i>
                  <i
                    v-if="Object.keys(data.details).length == key + 1"
                    @click="
                      data.details.push({
                        subject_id: null,
                        fourth_subject: 0,
                        ct_mark: 0,
                        ct_pass_mark: 0,
                        cq_mark: 0,
                        cq_pass_mark: 0,
                        mcq_mark: 0,
                        mcq_pass_mark: 0,
                        practical_mark: 0,
                        practical_pass_mark: 0,
                        converted_mark: 0,
                        sorting: data.details.length + 1,
                      })
                    "
                    class="bx bx-plus btn btn-xs btn-primary ml-2"
                  >
                  </i>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
// define model name
const model = "secondarySubjectAssign";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Subject Assign",
  icon: "left-arrow-alt",
};

export default {
  components: {
    Textarea: () => import("@components/backend/elements/Form/Textarea"),
  },
  data() {
    return {
      model: model,
      submitType: "",
      data: {
        institution_id: null,
        medium_id: null,
        academic_class_id: null,
        details: [
          {
            subject_id: null,
            fourth_subject: 0,
            ct_mark: 0,
            ct_pass_mark: 0,
            cq_mark: 0,
            cq_pass_mark: 0,
            mcq_mark: 0,
            mcq_pass_mark: 0,
            practical_mark: 0,
            practical_pass_mark: 0,
            converted_mark: 0,
          },
        ],
      },
      subjects: [],
    };
  },

  computed: {
    classes() {
      let classesArr = [];
      if (this.$root.global.classes) {
        classesArr = this.$root.global.classes.filter(
          (e) => e.institution_category_id == 2
        );
      }
      return classesArr;
    },
  },

  methods: {
    submit: function () {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        // subjects required
        let subjectReq = false;
        this.data.details.forEach((el) => {
          if (!el.subject_id) {
            this.notify("Please select subject", "error");
            subjectReq = true;
            return false;
          }
        });
        if (subjectReq) {
          return false;
        }

        // If there is no error
        if (res) {
          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data, this.submitType);
          }
        }
      });
    },

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(
        this.model,
        page,
        "Secondary Subject Assign",
        addOrBack
      );

      this.get_data("subject", "subjects", {
        allData: true,
        institution_category_id: 2,
      });
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
  },

  // ================== validation rule for form ==================
  validators: {
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "data.medium_id": function (value = null) {
      return Validator.value(value).required("Medium is required");
    },
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
  },
};
</script>