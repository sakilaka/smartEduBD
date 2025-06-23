<template>
  <div>
    <!-- Publish -->
    <div class="card border publish">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">Publish</h6>
      </div>
      <div class="card-body p-3 text-center">
        <Button :title="btnTitle" process="" /> &nbsp;&nbsp;

        <button
          v-if="!$route.params.id && !$root.submit && save_and_edit"
          type="button"
          :disabled="$root.submit ? true : false"
          class="btn btn-success btn-sm px-3"
          @click="($parent.submitType = 'edit'), $parent.submit()"
        >
          <i class="bx bx-check-circle"></i>Save & Edit
        </button>
      </div>
    </div>

    <!-- STATUS -->
    <div v-if="$parent.data.status" class="card border">
      <div class="card-header">
        <h6 class="mb-0 text-uppercase">STATUS</h6>
      </div>
      <div class="card-body p-3">
        <select
          v-model="$parent.data['status']"
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
</template>

<script>
export default {
  props: {
    btnTitle: {
      default: "Save",
    },
    save_and_edit: {
      default: true,
    },
  },
  methods: {
    reqClass() {
      if (this.$parent.validation.hasError("data.status")) {
        return "form-invalid";
      } else if (this.$parent.data.status) {
        return "form-valid";
      }
    },
  },
};
</script>