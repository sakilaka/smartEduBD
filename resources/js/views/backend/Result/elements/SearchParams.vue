<template>
  <div class="card border">
    <div class="card-header">
      <h6
        @click="$parent.search_filter = !$parent.search_filter"
        class="mb-0 text-uppercase cursor-pointer"
      >
        {{ title }}

        <i class="bx bx-filter float-end" title="Filter">
          <small>FILTER</small>
        </i>
      </h6>
    </div>

    <div v-show="$parent.search_filter" class="card-body py-3">
      <div class="row g-2">
        <!------------ Single Input ------------>
        <SelectCustom
          v-show="!$root.institution_id"
          title="Institution"
          field="institution_id"
          :req="true"
          :datas="$root.global.institutions"
          col="2"
          val="id"
          val_title="name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          title="Session"
          field="academic_session_id"
          :datas="sessions_filter(category)"
          :req="true"
          col="2"
          val="id"
          val_title="name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          title="Campus"
          field="campus_id"
          :req="true"
          :datas="campuses_filter(data.institution_id)"
          col="2"
          val="id"
          val_title="name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          title="Shift"
          field="shift_id"
          :req="true"
          :datas="shift_filter(data.institution_id)"
          col="2"
          val="shift_id"
          val_title="shift_name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          title="Medium"
          field="medium_id"
          :req="true"
          :datas="medium_filter(data.institution_id)"
          col="2"
          val="medium_id"
          val_title="medium_name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          title="Academic Class"
          field="academic_class_id"
          :req="true"
          :datas="category_classes_filter(data.institution_id, category)"
          col="2"
          val="academic_class_id"
          val_title="class_name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          title="Group"
          field="group_id"
          :req="true"
          :datas="group_filter(data.institution_id)"
          col="2"
          val="group_id"
          val_title="group_name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          v-if="is_section_filter"
          title="Section"
          field="section_id"
          :req="true"
          :datas="section_filter(data.institution_id)"
          col="2"
          val="section_id"
          val_title="section_name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <SelectCustom
          title="Exam"
          field="exam_id"
          :req="true"
          :datas="$parent.exam_lists"
          :col="2"
          class="pe-0"
          val="id"
          val_title="name"
          :disable="disable"
        />
        <!------------ Single Input ------------>
        <div v-if="exam_date" class="col-2">
          <Label
            title="Exam Date"
            :req="true"
            :condition="validation.hasError('data.exam_date')"
            :msg="validation.firstError('data.exam_date')"
          />
          <date-picker
            disabled
            v-model="data.exam_date"
            valueType="format"
            format="YYYY-MMM-DD"
            :formatter="momentFormat"
            placeholder="click to select date"
          ></date-picker>
        </div>

        <!------------ Single Input ------------>
        <Radio
          v-if="certificate_bg"
          title="Marksheet Subject Type"
          field="certificate_bg"
          statusTitle1="Single"
          statusTitle2="Combined"
          value1="secondary_term_marksheet"
          value2="secondary_term_marksheet_combined"
          :req="true"
          col="2"
        />

        <!------------ Single Input ------------>
        <div v-if="subject" class="col-2 pe-0">
          <Label
            title="Subject"
            :req="true"
            :condition="validation.hasError('subject_id')"
            :msg="validation.firstError('subject_id')"
          />
          <select
            @change="$parent.selectSubject($event.target.value)"
            v-model="$parent.subject_id"
            class="form-select form-select-sm"
            :class="$parent.reqClass('subject_id')"
          >
            <option :value="null">--Select Any--</option>
            <option
              v-for="(subject, key) in $parent.subjects"
              :key="key"
              v-bind:value="subject.subject_id"
            >
              {{ subject.subject ? subject.subject.name_en : "" }}
            </option>
          </select>
        </div>

        <!------------ Single Input ------------>
        <div v-if="result_type" class="col-2 pe-0">
          <Label title="Merit/Unmerit" :req="false" />
          <select v-model="$parent.type" class="form-select form-select-sm">
            <option value>ALL</option>
            <option v-for="(type, key) in merit_types" :key="key" :value="key">
              {{ type }}
            </option>
          </select>
        </div>

        <!------------ Single Input ------------>
        <template v-if="search_keyword">
          <div class="col-1 pe-0 pt-1">
            <select
              v-model="$parent.field_name"
              class="form-select form-select-sm mt-3"
            >
              <option value>--Select One--</option>
              <template v-for="(fname, fKey) in $parent.fields_name">
                <option :value="fKey" :key="fKey">{{ fname }}</option>
              </template>
            </select>
          </div>
          <div class="col-1 px-0 pt-1">
            <input
              type="text"
              v-model="$parent.search_keyword"
              class="form-control form-control-sm mt-3"
              placeholder="Type your text"
            />
          </div>
        </template>
        <!------------ Button ------------>
        <div class="col-1 px-0 pt-1">
          <button
            type="button"
            :disabled="$root.submit || $root.tableSpinner ? true : false"
            @click="$emit('search-event')"
            class="btn btn-sm btn-success mt-3"
          >
            <span v-if="$root.submit || $root.tableSpinner">
              <span class="spinner-border spinner-border-sm"></span>
            </span>
            <span v-else><i class="bx bx-search mx-0 fw-bold"></i></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Radio from "@components/backend/elements/Form/Radio";

export default {
  components: { Radio },

  props: {
    title: { type: String, default: "Search Result" },
    data: {},
    validation: {},
    search_keyword: { type: Boolean, default: true },
    is_section_filter: { type: Boolean, default: true },
    result_type: { type: Boolean, default: false },
    subject: { type: Boolean, default: false },
    exam_date: { type: Boolean, default: false },
    certificate_bg: { type: Boolean, default: false },
    category: { type: undefined, default: 1 },
    disable: { type: Boolean, default: false },
  },

  data() {
    return {
      merit_types: {
        merit_position_in_shift: "Shift Wise Merit",
        merit_position_in_class: "Class Wise Merit",
        merit_position_in_section: "Section Wise Merit",
        unmerit: "Unmerit",
      },
    };
  },
};
</script>

<style >
.mx-input:disabled,
.mx-input.disabled {
  color: #777 !important;
}
</style>