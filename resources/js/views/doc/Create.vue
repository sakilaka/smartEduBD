<template>
  <div>
    <!-- Form -->
    <form
      v-if="!$root.spinner"
      v-on:submit.prevent="submit"
      id="form"
      class="row"
      enctype="multipart/form-data"
    >
      <div class="col-xl-9">
        <div class="card border">
          <div class="card-body p-3">
            <div class="row g-3">
              <!------------ Single Input ------------>
              <Input title="First Name" field="fname" :req="true" />
              <!------------ Single Input ------------>
              <div class="col-4">
                <Label
                  title="Last Name"
                  :req="false"
                  :condition="validation.hasError('data.lname')"
                  :msg="validation.firstError('data.lname')"
                />
                <input
                  class="form-control form-control-sm"
                  v-model="data.lname"
                  type="text"
                  name="lname"
                  placeholder="Last Name"
                  :class="reqClass('data.lname')"
                />
                <!-- <span class="help-block-error">
                  {{ validation.firstError("data.name") }}
                </span> -->
              </div>
              <Input
                title="Sorting"
                field="sorting"
                type="number"
                :req="true"
                col="2"
              />
              <!-- Single Input -->
              <div class="col-4">
                <Label
                  title="Datepicker"
                  :req="false"
                  :condition="validation.hasError('data.date')"
                  :msg="validation.firstError('data.date')"
                />
                <date-picker
                  v-model="data.date"
                  valueType="format"
                  format="YYYY-MMM-DD"
                  :formatter="momentFormat"
                  placeholder="Select Date"
                ></date-picker>
                <b-form-datepicker
                  v-model="data.date"
                  id="datepicker1"
                  size="sm"
                  :date-format-options="{
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    weekday: 'short',
                  }"
                  placeholder="Select Date"
                ></b-form-datepicker>
              </div>
              <Input title="Time" field="time" type="time" col="3" />
              <!------------ Single Select ------------>
              <SelectCustom
                title="Select Menu"
                field="menu"
                :req="true"
                :datas="$root.menus ? $root.menus : []"
                val="id"
                val_title="menu_name"
                col="3"
              />
              <Select
                title="Select Title"
                field="select_id"
                :req="true"
                :datas="$root.global.status ? $root.global.status : []"
                col="3"
              />

              <!------------ Single Input ------------>
              <div class="col-6 d-none">
                <Label
                  title="Status Custom"
                  :req="true"
                  :condition="validation.hasError('data.status')"
                  :msg="validation.firstError('data.status')"
                />
                <select
                  v-model="data.status"
                  class="form-control form-control-sm"
                  :class="reqClass('data.status')"
                >
                  <option value>Select Status</option>
                  <option
                    v-for="(value, key, index) in $root.global.status"
                    :key="index"
                    v-bind:value="key"
                  >
                    {{ value }}
                  </option>
                </select>
              </div>
              <!------------ Single Input ------------>
              <Radio
                title="Radio"
                field="radio"
                statusTitle1="Yes"
                statusTitle2="No"
                value1="1"
                value2="0"
              />
              <!------------ Single Input ------------>
              <div class="col-3">
                <Label
                  title="Checkbox"
                  :req="false"
                  :condition="validation.hasError('data.lname')"
                  :msg="validation.firstError('data.lname')"
                />
                <div class="form-check">
                  <input
                    v-model="data.checkbox"
                    class="form-check-input"
                    type="checkbox"
                  />Yes
                </div>
              </div>

              <!-- if you use file, please add field_name in your form data object,
                and add this images:{} object after data object -->
              <!------------ Single Input ------------>
              <File
                title="Logo"
                field="logo"
                mime="img"
                :req="true"
                fileClassName="file1"
              />

              <!------------ Single Input ------------>
              <div class="col-12">
                <label>Description</label>
                <div class="col-12 p-0">
                  <editor :editorModel="'description'" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <Meta />
      </div>

      <!-- RIGHT SIDE -->
      <div class="col-xl-3">
        <!-- <ButtonStatus /> -->

        <div class="card border publish">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">Publish</h6>
          </div>
          <div class="card-body p-3">
            <Button title="Save" process="" /> &nbsp;&nbsp;

            <button
              v-if="!$route.params.id && !$root.submit"
              type="button"
              :disabled="$root.submit ? true : false"
              class="btn btn-success btn-sm px-3"
              @click="(submitType = 'edit'), submit()"
            >
              <i class="bx bx-check-circle"></i>Save & Edit
            </button>
          </div>
        </div>

        <!-- STATUS -->
        <div v-if="data.status" class="card border">
          <div class="card-header">
            <h6 class="mb-0 text-uppercase">STATUS</h6>
          </div>
          <div class="card-body p-3">
            <select
              v-model="data['status']"
              name="status"
              class="form-control form-control-sm col-12"
              :class="reqClass()"
            >
              <option :value="null">--Select Any--</option>
              <slot v-if="$root.global.status">
                <option
                  v-for="(value, name, index) in $root.global.status"
                  :key="index"
                  v-bind:value="name"
                >
                  {{ value }}
                </option>
              </slot>
            </select>
          </div>
        </div>
      </div>
    </form>

    <!-- SINGLE FORM -->
    <!-- SINGLE FORM -->
    <div class="row">
      <div class="col-xl-7 mx-auto">
        <h6 class="mb-0 text-uppercase">User Registration</h6>
        <hr />
        <div class="card border-top border-0">
          <div class="card-body p-4">
            <!-- <div class="card-title d-flex align-items-center">
              <div><i class="bx bxs-user me-1 font-22 text-success"></i></div>
              <h5 class="mb-0 text-success">User Registration</h5>
            </div>
            <hr /> -->
            <form
              v-if="!$root.spinner"
              v-on:submit.prevent="submit"
              id="form1"
              class="row g-3"
            >
              <!------------ Single Input ------------>
              <Input title="First Name" field="fname" :req="true" />
              <!------------ Single Input ------------>
              <Input title="Last Name" field="lname" />
              <!------------ Single Input ------------>
              <Datepicker title="Date" field="date" :req="true" />
              <!------------ Single Select ------------>
              <SelectCustom
                v-if="$root.menus"
                title="Select Menu"
                field="menu"
                :req="true"
                :datas="$root.menus"
                val="id"
                val_title="menu_name"
              />
              <!------------ Single Input ------------>
              <Radio
                title="Radio"
                field="radio"
                statusTitle1="Yes"
                statusTitle2="No"
                value1="1"
                value2="0"
                :req="true"
                col="3"
              />

              <!-------------- button -------------->
              <div class="col-12 mt-4">
                <Button title="Submit" process="" />
              </div>
              <!-------------- button -------------->
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
/*--Promise--*/
import Promise from "bluebird"; //when use validation custom

/*--Component--*/
import Crop from "./../../components/backend/elements/Form/FileCrop";
import File from "./../../components/backend/elements/Form/File";
import Meta from "./../../components/backend/elements/Form/Meta";
import Radio from "./../../components/backend/elements/Form/Radio";
import Textarea from "./../../components/backend/elements/Form/Textarea";
import editor from "./../../components/backend/elements/CKEditor";

// define model name
const model = "admin";

// set breadcrumb
const breadcrumb = [{ route: "module.create", title: "Module Create" }];

// Add Or Back
const addOrBack = {
  route: model + ".index",
  title: model,
  icon: "left-arrow-alt",
};

export default {
  components: { File, Crop, Textarea, editor, Meta, Radio },
  //  components: {
  //     Textarea: () =>
  //       import("./../../../components/backend/elements/Form/Textarea"),
  //   },

  data() {
    return {
      model: model,
      submitType: "",
      customBreadcrumb: { breadcrumb: breadcrumb, addOrBack: addOrBack },
      data: {
        menu: null,
        logo: null,
        date: "",
        select_id: "active",
        status: "active",
        radio: 0,
        sorting: 1,
      },
      images: {},
      errors: {},
    };
  },

  methods: {
    submit() {
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

          // if upload an image
          var form = document.getElementById("form");
          var formData = new FormData(form);

          let desc = this.data.description ? this.data.description : "";
          formData.append("description", desc);

          if (this.data.id) {
            this.update(this.model, formData, this.data.id, "image");
          } else {
            this.store(this.model, formData);
          }
        }
      });
    },

    // Custom Request Submit
    axiosReq() {
      this.$validate().then((res) => {
        const error = this.validation.countErrors();
        // If there is an error
        if (error > 0) {
          this.notify(error, "validate");
          return false;
        }

        if (res) {
          this.$root.spinner = true;
          axios
            .post("/module/create", this.data)
            .then((res) => {
              this.notify("Module Create Successfully", "success");
              this.$router.push({ name: this.model + ".index" });
            })
            .catch((err) => this.notify("Something went wrong", "error"))
            .then((alw) => setTimeout(() => (this.$root.spinner = false), 200));
        }
      });
    },

    dateValue(val) {
      console.log(val);
    },
    callBack() {
      this.notify("Delete Successfully", "success");
    },
    reqClass(field) {
      if (this.validation.hasError(field)) {
        return "form-invalid";
      } else if (this.data[field]) {
        return "form-valid";
      }
    },
    // Async Data
    asyncData() {
      let page = "create";
      if (this.$route.params.id) {
        page = "edit";
        this.get_data(`${this.model}/${this.$route.params.id}`);
      } else {
        this.get_sorting("Admin");
      }
      this.setBreadcrumbs(this.model, page, null, addOrBack);
      // breadcrumbs.dispatch("setBreadcrumbs", this.customBreadcrumb); // Set Custom Breadcrumbs
      // setTimeout(() => (this.$root.spinner = false), 200); // custom spinner off
    },
  },
  watch: {
    $route: {
      handler: "asyncData",
      immediate: true,
    },
  },

  created() {},
  mounted() {
    // this.notify("Delete", "confirm");
  },

  /*-----validation rule for form-----*/
  validators: {
    "data.fname": function (value = null) {
      return Validator.value(value)
        .required("First Name is required")
        .maxLength(100);
    },
    "data.lname": function (value = null) {
      // return Validator.value(value).required("L Name is required");
    },
    "data.menu": function (value = null) {
      return Validator.value(value).required("is required");
    },
    "data.date": function (value = null) {
      return Validator.value(value).required("Date is required");
    },
    "data.status": function (value = null) {
      return Validator.value(value).required("Status is required");
    },
    "data.logo": function (value = null) {
      return Validator.value(value).required("is required");
    },
    "data.sorting": function (value = null) {
      return Validator.value(value).required("is required").digit();
    },
  },
};
</script>
