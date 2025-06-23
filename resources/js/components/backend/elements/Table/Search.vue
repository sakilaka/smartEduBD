<template>
  <div class="row col-12 pe-0" :class="search_field ? 'col-xl-4' : 'col-xl-1'">
    <div v-if="search_field" class="col-4 col-xl-4 pe-0">
      <select
        v-model="$parent.search_data.field_name"
        class="form-select form-select-sm"
      >
        <option
          v-for="(value, name, index) in fields_name"
          :key="index"
          v-bind:value="name"
        >
          {{ value }}
        </option>
      </select>
    </div>
    <div v-if="search_field" class="col-5 col-xl-6 px-0">
      <input
        type="text"
        v-model="$parent.search_data.value"
        class="form-control form-control-sm"
        placeholder="Type your text"
      />
    </div>
    <div
      v-if="$parent.search_data.pagination"
      class="col-2 px-0"
      :class="search_field ? 'col-xl-1' : 'col-xl-7'"
    >
      <select
        @change="$parent.search"
        v-model="$parent.search_data.pagination"
        class="form-control form-control-sm text-center"
      >
        <option disabled>Showing per page</option>
        <option>10</option>
        <option>25</option>
        <option>50</option>
        <option>100</option>
        <option>200</option>
        <option>500</option>
        <option>1000</option>
        <option>1500</option>
        <option>2000</option>
        <option>5000</option>
        <option>8000</option>
        <option>10000</option>
      </select>
    </div>
    <div class="col-1 px-0" :class="search_field ? 'col-1' : 'col-5'">
      <button
        type="submit"
        :disabled="$root.tableSpinner ? true : false"
        class="btn btn-sm btn-success"
      >
        <span
          v-if="$root.tableSpinner"
          style="height: 1rem; width: 1rem; font-size: 11px"
          class="spinner-border text-white"
        ></span>

        <i v-else class="bx bx-search mx-0 fw-bold"></i>
      </button>
    </div>

    <!-- PDF / PRINT / EXCEL -->
    <div v-if="print_excel" class="dropdown printExcel">
      <a
        title="PDF / Print / Excel"
        class="dropdown-toggle dropdown-toggle-nocaret"
        href="#"
        data-bs-toggle="dropdown"
        ><i class="bx bx-dots-horizontal-rounded font-22 text-option"></i>
      </a>
      <ul class="dropdown-menu">
        <li>
          <a
            class="dropdown-item"
            @click="print('printArea', $parent.model)"
            href="javascript:;"
          >
            - PRINT
          </a>
        </li>
        <li>
          <a class="dropdown-item" @click="generatePdf()" href="javascript:;">
            - Download PDF
          </a>
        </li>
        <li>
          <download-excel
            v-if="$parent.table.datas"
            class="dropdown-item"
            :data="$parent.table.datas"
            :fields="$parent.json_fields"
            :name="$parent.model + '.xls'"
            style="cursor: pointer"
          >
            - Export as Excel
          </download-excel>
        </li>
        <slot> </slot>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    fields_name: null,
    search_field: {
      type: Boolean,
      default: true,
    },
    print_excel: {
      type: Boolean,
      default: true,
    },
  },
  methods: {
    generatePdf() {
      const doc = new jsPDF();
      $(".action").css("display", "none");
      autoTable(doc, { html: "#pdf-table" });
      doc.save(this.$parent.model + ".pdf");
      setTimeout(() => $(".action").show(), 300);
    },
  },
};
</script>