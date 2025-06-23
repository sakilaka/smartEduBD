<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-9">
      <div class="card border">
        <div class="card-body p-3">
          <div class="row g-3">
            <!------------ Single Input ------------>
            <Input title="Exam Name" field="name" :req="true" />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Institution Category"
              field="institution_category_id"
              :req="true"
              :datas="$root.global.institution_categories"
              col="6"
              val="id"
              val_title="name"
            />
            <!------------ Single Input ------------>
            <Radio
              title="Exam Type"
              field="exam_type"
              statusTitle1="Term Exam"
              statusTitle2="Class Test"
              value1="term"
              value2="ct"
              col="6"
              :req="true"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              v-if="data.exam_type == 'term'"
              title="Class Test"
              field="class_test_exam_id"
              :req="false"
              :datas="class_lists"
              col="6"
              val="id"
              val_title="name"
            />
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
import Radio from "@components/backend/elements/Form/Radio";

// define model name
const model = "exam";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { Radio },

  data() {
    return {
      model: model,
      submitType: "",
      data: { class_test_exam_id: null, institution_category_id: null },
      class_lists: [],
    };
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
      this.setBreadcrumbs(this.model, page, null, addOrBack);

      this.get_data("exam", "class_lists", { allData: true, exam_type: "ct" });
    },
  },
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.institution_category_id": function (value = null) {
      return Validator.value(value).required(
        "Institution category is required"
      );
    },
    "data.exam_type": function (value = null) {
      return Validator.value(value).required("Exam type is required");
    },
  },
};
</script>