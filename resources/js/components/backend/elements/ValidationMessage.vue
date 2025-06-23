<template>
  <div>
    <!-- modal for password  -->
    <b-modal
      id="validate-error"
      size="lg"
      title="You need to fill empty mandatory fields"
    >
      <div
        v-if="
          $root.validation_errors &&
          Object.keys($root.validation_errors).length > 0
        "
        class="col-12 py-2 mb-3"
        style="background: #fddede"
      >
        <div class="error">
          <span
            v-for="(err, errIndex) in $root.validation_errors"
            :key="errIndex"
            class="text-danger"
          >
            {{ err[0] }}
            <br />
          </span>
        </div>
      </div>

      <!-- exception_errors -->
      <div
        v-if="
          $root.exception_errors &&
          Object.keys($root.exception_errors).length > 0
        "
        class="col-12 py-2 mb-3"
        style="background: #fddede"
      >
        <div class="error">
          <slot v-if="typeof $root.exception_errors === 'object'">
            <slot v-for="(err, errIndex) in $root.exception_errors">
              <span
                v-if="typeof err === 'string'"
                :key="errIndex"
                class="text-danger"
              >
                {{ err }}
                <br />
              </span>
            </slot>
          </slot>
          <slot v-else>
            {{ $root.exception_errors }}
          </slot>
        </div>
      </div>
    </b-modal>
  </div>
</template>