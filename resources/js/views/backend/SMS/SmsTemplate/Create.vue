<template>
  <form
    v-if="!$root.spinner"
    v-on:submit.prevent="submit"
    id="form"
    class="row"
  >
    <div class="col-xl-6">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">SMS Template</h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <Input title="Template Name" field="name" :col="6" :req="true" />
            <Select
              title="SMS Type"
              field="sms_type"
              :req="true"
              :datas="$root.global.sms_types"
              :col="6"
            />
            <Radio
              title="Common Message"
              field="common_message"
              statusTitle1="YES"
              statusTitle2="NO"
              value1="1"
              value2="0"
              :col="3"
              :req="true"
            />
            <Radio
              title="Sending Status"
              field="sending_status"
              statusTitle1="ONLINE"
              statusTitle2="OFFLINE"
              value1="1"
              value2="0"
              :col="6"
              :req="true"
            />
            <div class="col-12">
              <Label
                title="SMS Body"
                :req="true"
                :condition="validation.hasError('data.sms_body')"
                :msg="validation.firstError('data.sms_body')"
              />
              <textarea
                v-model="data.sms_body"
                rows="12"
                class="form-control"
              ></textarea>
              <small>Characters: {{ data.sms_body.length }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3">
      <div class="card border">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Keywords</h6>
        </div>
        <div class="card-body">
          <div class="col-12">
            <ul>
              <li
                style="cursor: pointer"
                class="mb-3"
                v-for="attr in $root.global.sms_keywords"
                @click="addedKeyword(attr)"
                :key="attr"
              >
                {{ attr }}
              </li>
            </ul>
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
const model = "smsTemplate";

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
      data: {
        sms_type: null,
        sms_body: "",
        common_message: 0,
        sending_status: 0,
      },
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

    addedKeyword(key) {
      this.data.sms_body += key;
    },

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(this.model, this.$route.params.id, "data");
      }
      this.setBreadcrumbs(this.model, page, "SMS Template", addOrBack);
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
      return Validator.value(value).required("Template name is required");
    },
    "data.sms_type": function (value = null) {
      return Validator.value(value).required("SMS Type is required");
    },
    "data.sms_body": function (value = null) {
      return Validator.value(value).required("SMS Body is required");
    },
    "data.common_message": function (value = null) {
      return Validator.value(value).required("Common Message is required");
    },
  },
};
</script>