<template>
  <b-modal id="student-subject-choice" size="xl" title="Subject Choice">
    <div class="col-12">
      <div v-if="student && Object.keys(student).length > 0" class="row">
        <div class="card">
          <div class="card-header">
            <h6>Name: {{ student.name }}</h6>
            <h6>College Roll: {{ student.college_roll }}</h6>
          </div>
        </div>

        <!-- left side -->
        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Compulsory Subjects</h6>
            </div>
            <div class="card-body">
              <table class="table table-sm table-bordered table-striped mb-0">
                <thead class="table-light">
                  <tr>
                    <!-- <th class="text-center" style="width: 50px">#</th> -->
                    <th class="text-center" style="width: 50px"></th>
                    <th>Subject Name</th>
                  </tr>
                </thead>

                <tbody v-if="Object.keys(main_subjects).length > 0">
                  <template v-for="(item, key) in main_subjects">
                    <tr :key="key">
                      <th class="text-center">
                        <i
                          class="bx bxs-check-square text-primary"
                          style="font-size: 1.3rem !important"
                        ></i>
                      </th>
                      <td>
                        {{ item.subject ? item.subject.name_en : "" }}
                      </td>
                    </tr>
                  </template>
                </tbody>
                <tbody v-else>
                  <tr>
                    <td colspan="7" class="text-center">No subjects</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- student select subjects -->
          <div
            class="card mt-4"
            v-if="
              data.assign_subjects &&
              Object.keys(data.assign_subjects).length > 0
            "
          >
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">4th / Main Subject</h6>
            </div>
            <div class="card-body">
              <table class="table table-sm table-bordered table-striped mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width: 50px"></th>
                    <th>Subject Name</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td colspan="3" style="height: 40px" class="pt-3">
                      <b>4th Subject</b>
                    </td>
                  </tr>
                  <tr>
                    <th class="text-center">
                      <i
                        class="bx bxs-check-square text-primary"
                        style="font-size: 1.3rem !important"
                      ></i>
                    </th>
                    <td>
                      <template v-for="(item, key) in data.assign_subjects">
                        <span v-if="item.main_subject == 0" :key="key">
                          {{ item.subject ? item.subject.name_en : "" }}
                        </span>
                      </template>
                    </td>
                  </tr>

                  <tr>
                    <td colspan="3" style="height: 40px" class="pt-3">
                      <b>Main Subjects</b>
                    </td>
                  </tr>
                  <template v-for="(item, key1) in data.assign_subjects">
                    <tr v-if="item.main_subject == 1" :key="key1">
                      <th class="text-center">
                        <i
                          class="bx bxs-check-square text-primary"
                          style="font-size: 1.3rem !important"
                        ></i>
                      </th>
                      <td>
                        {{ item.subject ? item.subject.name_en : "" }}
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Right side -->
        <div class="col-6">
          <div class="card">
            <div class="card-header">
              <h6 class="mb-0 text-uppercase">Update 4th / Main Subject</h6>
            </div>
            <div class="card-body">
              <p
                style="
                  font-size: 13px;
                  color: red;
                  border-bottom: 1px solid #eee;
                  padding-bottom: 10px;
                "
              >
                {{ data.subjectAssign ? data.subjectAssign.note : "" }}
              </p>
              <!-- 4th Subject -->
              <div class="form-group mb-4">
                <select
                  v-model="forth_subject_id"
                  class="form-select form-select-sm"
                >
                  <option value="">--Select 4th Subject--</option>
                  <option
                    v-for="(sub, key) in fourth_subjects"
                    :key="key + 1"
                    :value="sub.subject_id"
                  >
                    {{ sub.subject.name_en }}
                  </option>
                </select>
              </div>

              <!-- Subjects List -->
              <label class="pb-2"><b>Select Main Subject</b></label>

              <table class="table table-sm table-bordered table-striped mb-0">
                <thead class="table-light">
                  <tr>
                    <th class="text-center" style="width: 50px"></th>
                    <th>Subject Name</th>
                  </tr>
                </thead>

                <tbody>
                  <tr v-if="subject_loader">
                    <td colspan="3" class="text-center pt-5 pb-5">loading..</td>
                  </tr>
                  <template v-else>
                    <template v-if="Object.keys(main_subjects_list).length > 0">
                      <template v-for="(item, subKey) in main_subjects_list">
                        <tr :key="subKey">
                          <th class="text-center">
                            <input
                              @click="
                                selectAdditionalSubject(item.subject_id, subKey)
                              "
                              type="checkbox"
                              v-model="item.checked"
                              value="1"
                              :id="item.subject_id + subKey + 1"
                              :disabled="
                                (!item.checked && disabled) ||
                                except_subjects.includes(item.subject_id)
                                  ? true
                                  : false
                              "
                            />
                          </th>
                          <td>
                            {{ item.subject ? item.subject.name_en : "" }}
                          </td>
                        </tr>
                      </template>

                      <!-- subject button -->
                      <tr>
                        <td colspan="4" class="mt-4 text-center">
                          <button
                            class="btn btn-success btn-md my-5"
                            @click="subjectAssign()"
                            :disabled="!$root.submit ? false : true"
                          >
                            <b v-if="!$root.submit">SUBMIT</b>
                            <div v-if="$root.submit" class="loading">
                              processing..
                            </div>
                          </button>
                        </td>
                      </tr>
                    </template>

                    <tr v-else>
                      <td colspan="7" class="text-center pt-5 pb-5">
                        No subjects
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </b-modal>
</template>

<script>
export default {
  props: {
    student: {
      type: Object,
      default: {},
    },
    data: {
      type: Object,
      default: {
        subjectAssign: { main_subject: 0, note: "", details: [] },
        assign_subjects: [],
      },
    },
  },
  data() {
    return {
      disabled: false,
      subject_loader: false,
      forth_subject_id: "",
      main_subjects_list: [],
      except_subjects: [],
    };
  },

  watch: {
    forth_subject_id: function (id) {
      if (!id) return false;
      if (!this.data.subjectAssign) return false;
      this.except_subjects = [];
      this.except_subjects.push(id);

      this.disabled = false;
      this.main_subjects_list = [];
      let subjectLists = this.data.subjectAssign.details;

      if (Object.keys(subjectLists).length > 0) {
        let find = subjectLists.find((e) => e.subject_id == id);

        this.subject_loader = true;
        let add_subjects = subjectLists.filter(
          (e) =>
            e.main_subject &&
            e.subject_id != this.forth_subject_id &&
            e.subject_id != find.except_subject_id
        );

        // checked data reset
        let subjects = add_subjects.map((e) => {
          let obj = e;
          obj.checked = false;
          return obj;
        });

        // set dataset
        setTimeout(() => {
          this.main_subjects_list = subjects;
          this.subject_loader = false;
        }, 300);
      }
    },
  },

  computed: {
    main_subjects() {
      if (!this.data.subjectAssign) return [];
      let subjects = [];
      let subjectLists = this.data.subjectAssign.details;

      if (Object.keys(subjectLists).length > 0) {
        subjects = subjectLists.filter(
          (e) => !e.fourth_subject && !e.main_subject
        );
      }
      return subjects;
    },

    fourth_subjects() {
      if (!this.data.subjectAssign) return [];
      let subjects = [];
      let subjectLists = this.data.subjectAssign.details;

      if (Object.keys(subjectLists).length > 0) {
        subjects = subjectLists.filter((e) => e.fourth_subject);
      }
      return subjects;
    },
  },

  methods: {
    subjectAssign() {
      if (!this.forth_subject_id) {
        this.notify("Please select 4th subject", "error");
        return false;
      }

      if (confirm("Are you sure want to submit this subjects ?")) {
        let data = this.main_subjects_list.filter((e) => e.checked);

        if (Object.keys(data).length == 0) {
          this.notify("Please select subject", "error");
          return false;
        }

        if (Object.keys(data).length != this.data.subjectAssign.main_subject) {
          this.notify(
            `Please select minimum ${this.data.subjectAssign.main_subject} subject`,
            "error"
          );
          return false;
        }

        data.unshift({
          main_subject: 0,
          subject_id: this.forth_subject_id,
        });

        this.$root.submit = true;

        axios
          .post("subject-assign", {
            data: data,
            student_id: this.student.id,
          })
          .then((res) => {
            if (res.status == 200) {
              this.main_subjects_list = [];
              this.forth_subject_id = "";
              this.$parent.getClasswiseSubject(this.student.id);
              this.$parent.get_paginate_data(
                this.$parent.model,
                this.$parent.search_data
              );
              this.notify(res.data.message, "success");
            }
          })
          .catch((er) => this.notify("Something went wrong", "error"))
          .finally((alw) => setTimeout(() => (this.$root.submit = false), 400));
      }
    },

    selectAdditionalSubject(id, index) {
      if (Object.keys(this.main_subjects_list).length > 0) {
        let find = this.main_subjects_list.find((e) => e.subject_id == id);

        if (find.except_subject_id) {
          if (this.except_subjects.includes(find.except_subject_id)) {
            this.except_subjects = this.except_subjects.filter(
              (e) => e !== find.except_subject_id
            );
          } else {
            this.except_subjects.push(find.except_subject_id);
          }
        }

        setTimeout(() => {
          let count = 0;
          let filter = this.main_subjects_list.filter((e) => e.checked);
          count = filter.length;

          this.disabled =
            count == this.data.subjectAssign.main_subject ? true : false;

          this.dataRender();
        }, 500);
      }
    },

    dataRender() {
      let subjectArr = [...this.main_subjects_list];
      this.main_subjects_list = [];
      this.main_subjects_list = subjectArr;
    },
  },
};
</script>