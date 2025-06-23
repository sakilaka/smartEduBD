<template>
  <div>
    <b-modal id="bulk-delete" size="lg" title="Select Student">
      <div class="row py-2">
        <div class="col-5">
          <input
            type="text"
            v-model="keyword"
            placeholder="Software ID /  Name / Mobile"
            class="form-control form-control-sm"
          />
        </div>
        <div class="col-4">
          <small>
            Selected :
            <b>{{ Object.keys(selectedItems).length }}</b>
          </small>
        </div>
        <div class="col-3 text-end">
          <button
            v-if="Object.keys(selectedItems).length"
            type="button"
            class="btn btn-danger btn-sm"
            :disabled="$root.spinner ? true : false"
            @click="destroy()"
          >
            <span
              v-if="$root.spinner"
              class="spinner-border spinner-border-sm"
            ></span>
            <span v-else><i class="bx bx-trash me-0"></i> DELETE</span>
          </button>
        </div>
      </div>
      <div class="table-fixed printArea">
        <table class="table table-bordered table-striped table-hover table-sm">
          <thead>
            <tr>
              <th class="text-center">
                <input type="checkbox" v-model="check_all" :value="true" />
              </th>
              <th class="text-center">#</th>
              <th>Software ID</th>
              <th>Student Name</th>
              <th>Mobile</th>
              <th>College Roll</th>
            </tr>
          </thead>
          <tbody v-if="Object.keys(filteredLists).length > 0">
            <tr v-for="(row, key) in filteredLists" :key="key">
              <td class="text-center">
                <input
                  type="checkbox"
                  :value="row.id"
                  :id="row.id"
                  v-model="row.checked"
                />
              </td>
              <td class="text-center">{{ key + 1 }}</td>
              <td>{{ row.student_id }}</td>
              <td>{{ row.name }}</td>
              <td>{{ row.mobile }}</td>
              <td>{{ row.college_roll }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </b-modal>
  </div>
</template>

<script>
export default {
  props: ["lists"],
  data() {
    return {
      check_all: false,
      keyword: "",
    };
  },

  watch: {
    check_all(val) {
      if (Object.keys(this.filteredLists).length > 0) {
        this.filteredLists.map((el) => {
          let std = el;
          std.checked = val;
          return std;
        });
      }
    },
  },

  computed: {
    filteredLists() {
      return this.lists.filter((item) => {
        // let college_roll = parseInt(item.college_roll) ? item.college_roll : "";
        // ||(college_roll && college_roll.includes(searchLower))

        const searchLower = this.keyword.toLowerCase();
        return (
          item.name_en.toLowerCase().includes(searchLower) ||
          item.mobile.includes(searchLower)
        );
      });
    },
    selectedItems() {
      let filterData = this.filteredLists.filter((e) => e.checked);
      return filterData.map((e) => e.id);
    },
  },
  methods: {
    destroy() {
      if (confirm("Are you sure want to delete ?")) {
        this.$root.spinner = true;
        let id = this.selectedItems[0];
        axios
          .delete("/" + this.$parent.model + "/" + id, {
            params: { bulk_id: JSON.stringify(this.selectedItems) },
          })
          .then((res) => {
            this.$bvModal.hide("bulk-delete");
            this.$parent.get_paginate_data(
              this.$parent.model,
              this.$parent.search_data
            );
            if (res.data.message) {
              this.notify(res.data.message, "success");
            } else if (res.data.error) {
              this.notify(res.data.error, "error");
            }
          })
          .catch((error) => console.log(error))
          .finally((alw) =>
            setTimeout(() => (this.$root.spinner = false), 200)
          );
      }
    },
  },
};
</script>

<style scoped>
.table td {
  padding: 3px 5px !important;
  font-size: 12px;
}
</style>