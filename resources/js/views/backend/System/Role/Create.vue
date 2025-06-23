<template>
  <form v-if="!$root.spinner" v-on:submit.prevent="submit" id="form">
    <div class="card border">
      <div class="card-body p-3">
        <div class="row g-3">
          <!------------ Single Input ------------>
          <Input title="Name" field="name" type="text" :col="4" :req="true" />

          <div class="col-2 pt-4 text-center">
            <input type="checkbox" value="1" v-model="checkAll" />
            <b>Select All Permissions</b>
          </div>

          <div class="col-4"></div>
          <div class="col-2 text-center pt-2">
            <Button title="Save" process="" />
          </div>
        </div>
      </div>
    </div>

    <!-- permissions -->
    <div class="row">
      <div
        v-for="(permission, index) in permissions"
        :key="index"
        class="col-2"
      >
        <div class="card border">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">
              <b-form-checkbox
                :id="`checkbox-${permission.id}`"
                :name="`checkbox-${permission.id}`"
                :value="checkedAllProcess(permission)"
                v-model="permission.checked"
                :unchecked-value="false"
                @change="checkAllProcess(permission)"
              >
                {{
                  permission.name.replace("Controller", "") | camelcaseToWord
                }}
              </b-form-checkbox>
            </h6>
          </div>
          <div class="card-body p-2" style="height: 165px; overflow-y: scroll">
            <ul class="m-0 ps-2">
              <li
                v-for="(process, index2) in permission.children"
                :key="index2"
                style="list-style: none"
              >
                <input
                  type="checkbox"
                  :value="process.id"
                  v-model="data.permissions"
                />
                {{ process.name | camelcaseToWord }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
// define model name
const model = "role";

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
      childrenCheck: "",
      checkAll: "",
      data: { status: "active", permissions: [] },
      permissions: [],
      errors: {},
    };
  },

  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },

    checkAll: function (val, oldval) {
      this.data.permissions = [];
      if (val) {
        this.permissions.forEach((permission) => {
          permission.children.forEach((process) => {
            this.data.permissions.push(process.id);
          });
        });
      }
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

        // If there is no error
        if (res) {
          if (this.data.id) {
            this.update(this.model, this.data, this.data.id);
          } else {
            this.store(this.model, this.data);
          }
        }
      });
    },

    // Get Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
      this.get_data("get-permissions", "permissions");
    },

    // check all process
    checkAllProcess(permission) {
      let permissions = this.data.permissions;

      if (permission.checked) {
        permission.children.forEach((process) => {
          permissions.push(process.id);
        });
      } else {
        let removeData = permission.children.map((e) => e.id);
        let removeProcess = permissions.filter(
          (value, index) => !removeData.includes(value)
        );
        this.data.permissions = removeProcess;
      }
    },

    checkedAllProcess(permission) {
      let dataset = this.data.permissions;
      let existsValue = permission.children.map((e) => e.id);
      let exists = existsValue.every((value) => dataset.includes(value));
      permission.checked = exists;

      return true;
    },
  },

  // ================== validation rule for form ==================
  validators: {
    "data.name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
  },
};
</script>