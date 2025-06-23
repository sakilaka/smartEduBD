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
            <Select
              v-if="allMenus"
              title="Parent Menu"
              field="parent_id"
              :req="false"
              :datas="allMenus"
            />
            <!------------ Single Input ------------>
            <Input
              title="Menu Name"
              field="menu_name"
              type="text"
              :req="true"
            />
            <!------------ Single Input ------------>
            <Input title="Menu Icon" field="icon" type="text" :req="false" />
            <!------------ Single Input ------------>
            <Input title="Sorting" field="sorting" type="number" :req="true" />

            <!------------ Single Input ------------>
            <div class="col-12 col-xl-6">
              <Label
                title="Route Name"
                :req="false"
                :condition="validation.hasError('data.route_name')"
                :msg="validation.firstError('data.route_name')"
              />
              <select
                v-model="data.route_name"
                class="form-select form-select-sm"
              >
                <option :value="null">Select parent menu</option>
                <option
                  v-for="(value, id, index) in $root.permissions"
                  :key="index"
                  v-bind:value="value"
                >
                  {{ value }}
                </option>
              </select>
            </div>
            <!------------ Single Input ------------>
            <Select
              title="Menu Look type"
              field="looks_type"
              :req="false"
              :datas="menu_look_types"
            />

            <!------------ Single Input ------------>
            <Input title="Params" field="params" type="text" :req="false" />
            <!------------ Single Input ------------>
            <Radio
              title="Show Dashboard"
              field="show_dasboard"
              statusTitle1="Yes"
              statusTitle2="No"
              value1="1"
              value2="0"
              :req="true"
              col="2"
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
const model = "menu";

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
      allMenus: [],
      menu_look_types: {
        normal: "Normal",
        mega: "Mega",
      },
      data: {
        parent_id: null,
        route_name: null,
        looks_type: "normal",
        sorting: 0,
        icon: "<i class='bx bx-right-arrow-alt'></i>",
        show_dasboard: 0,
      },
      errors: {},
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
  },
  created() {
    let page = "create";
    if (this.$route.params.id) {
      page = "edit";
      this.get_data(`${this.model}/${this.$route.params.id}`);
    } else {
      this.get_sorting("System-Menu");
    }
    this.setBreadcrumbs(this.model, page, "Menu", addOrBack);
    this.get_data(`get-menus/menu`, "allMenus");
  },

  // ================== validation rule for form ==================
  validators: {
    "data.menu_name": function (value = null) {
      return Validator.value(value).required("Name is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).digit().required("sorting is required");
    },
  },
};
</script>