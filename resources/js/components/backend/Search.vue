<template>
  <div class="search-bar flex-grow-1">
    <div class="position-relative search-bar-box">
      <input
        type="text"
        v-model="key"
        class="form-control search-control"
        placeholder="Type to search..."
      />
      <span class="position-absolute top-50 search-show translate-middle-y"
        ><i class="bx bx-search"></i
      ></span>
      <span class="position-absolute top-50 search-close translate-middle-y"
        ><i class="bx bx-x"></i
      ></span>

      <div v-if="searchbox" class="search-result">
        <span class="float-end close-searbox">
          <i @click="searchbox = false" class="bx bx-x bx-sm"></i>
        </span>

        <ul v-if="Object.keys(results).length > 0 && !spinner">
          <template v-for="(result, key) in results">
            <li v-if="$root.checkPermission(result.route_name)" :key="key">
              <i class="bx bxs-right-arrow-alt fw-bold"></i>

              <router-link
                :to="{
                  name: result.route_name,
                  params: { slug: result.params },
                }"
                class="search-result-click"
              >
                {{ result.menu_name }}
              </router-link>
            </li>
          </template>
        </ul>

        <div v-else-if="spinner" class="py-4 text-center">
          <span
            style="height: 1rem; width: 1rem; font-size: 11px"
            class="spinner-border text-success"
          ></span>
          <span class="processing">processing...</span>
        </div>
        <div v-else class="py-4 text-center">No result found :)</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchbox: false,
      spinner: true,
      key: "",
      results: {},
    };
  },
  watch: {
    key: function (val) {
      this.searchbox = true;
      this.spinner = true;
      if (val) {
        axios.get("/search-result", { params: { key: val } }).then((res) => {
          this.results = res.data;
          setTimeout(() => (this.spinner = false), 100);
        });
      } else {
        this.results = {};
        this.spinner = false;
      }
    },
  },
  updated() {
    let vm = this;
    $(".search-result-click").click(function () {
      vm.searchbox = false;
    });
  },
};
</script>