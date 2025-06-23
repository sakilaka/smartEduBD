<template>
  <div v-if="!$root.spinner">
    <div class="col-12 position-absolute top-50 start-50 translate-middle">
      <div class="d-flex justify-content-center">
        <div class="col-12 col-xl-4 col-md-6 col-sm-6">
          <div class="card p-3">
            <div class="web-block my-3">
              <img :src="login_logo" class="card-img-top" />
            </div>
            <div class="card-body text-center">
              <h5 class="card-title ms-1">Guardian Login</h5>
              <p class="card-text mb-5 ms-1">
                Click to <b>go now</b> button and login your panel
              </p>

              <a :href="$root.guardian_url" class="btn btn-primary mb-1 mt-1">
                GO NOW
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      login_logo: `${this.$root.baseurl}/images/login.png`,
      data: {},
    };
  },
  mounted: function () {},
};
</script>
