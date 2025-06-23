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
            <Input field="title" title="Title" :req="true" :col="3" />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Institution"
              field="institution_id"
              :req="true"
              :datas="$root.global.institutions"
              val="id"
              val_title="name"
              :col="4"
            />
            <!------------ Single Input ------------>
            <SelectCustom
              title="Academic Class"
              field="academic_class_id"
              :req="true"
              :datas="$root.global.classes"
              val="id"
              val_title="name"
              :col="3"
            />
            <!------------ Single Input ------------>
            <Select
              title="Gateway Provider"
              field="provider"
              :req="true"
              :datas="$root.global.gateway ? $root.global.gateway : []"
              :col="2"
            />
            <!------------ Single Input ------------>
            <Textarea
              title="Description"
              :fixheight="false"
              field="description"
              :col="12"
            />
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h6 class="mb-0 text-uppercase">Gateway Credientials</h6>
        </div>
        <div class="card-body p-3">
          <table class="table table-sm table-bordered table-striped mb-0">
            <thead>
              <tr>
                <td>Gateway</td>
                <td>Prefix</td>
                <td>Username</td>
                <td>Password</td>
                <td>Account No</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(option, key) in data.options" :key="key">
                <td>
                  <select
                    @change="checkExistGateway($event.target.value, key)"
                    v-model="option.gateway"
                    class="form-select form-select-sm"
                  >
                    <option :value="null">--Gateway--</option>
                    <option
                      v-for="(value, key, index) in $root.global.gateway"
                      :key="index"
                      v-bind:value="key"
                    >
                      {{ value }}
                    </option>
                  </select>
                </td>
                <td>
                  <input
                    type="text"
                    v-model="option.prefix"
                    class="form-control form-control-sm"
                    placeholder="Prefix"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="option.username"
                    class="form-control form-control-sm"
                    placeholder="Username"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="option.password"
                    class="form-control form-control-sm"
                    placeholder="Password"
                  />
                </td>
                <td>
                  <input
                    type="text"
                    v-model="option.account_no"
                    class="form-control form-control-sm"
                    placeholder="Account no"
                  />
                </td>
                <td class="text-center">
                  <i
                    v-if="Object.keys(data.options).length > 1"
                    @click="data.options.splice(key, 1)"
                    class="bx bx-minus btn btn-xs btn-danger"
                  >
                  </i>
                  <i
                    v-if="Object.keys(data.options).length == key + 1"
                    @click="data.options.push({ gateway: '' })"
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

    <!-- RIGHT SIDE -->
    <div class="col-xl-3">
      <ButtonStatus />
    </div>
  </form>
</template>

<script>
// define model name
const model = "paymentGateway";

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: "Payment Gateway",
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
        status: "active",
        academic_class_id: null,
        institution_id: null,
        provider: "SSL",
        options: [{ gateway: "" }],
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

    checkExistGateway(gateway, index) {
      if (gateway) {
        this.data.options.forEach((option, key) => {
          if (key !== index && option.gateway === gateway) {
            this.notify("Gateway already exist", "error");
            this.$set(this.data.options[index], "gateway", "");
          }
        });
      }
    },

    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(this.model, page, "Payment Gateway", addOrBack);
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
    "data.title": function (value = null) {
      return Validator.value(value).required("Title is required");
    },
    "data.institution_id": function (value = null) {
      return Validator.value(value).required("Institution is required");
    },
    "data.academic_class_id": function (value = null) {
      return Validator.value(value).required("Academic Class is required");
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
    "data.provider": function (value = null) {
      return Validator.value(value).required("Provider is required");
    },
  },
};
</script>