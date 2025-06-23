<template>
  <div>
    <a v-if="excel" class="btn btn-xs btn-info float-end" href="javascript:;">
      <download-excel
        v-if="$parent.data.details"
        :data="$parent.data.details"
        :fields="$parent.json_fields"
        :header="$parent.excel_header"
        name="result.xls"
        style="cursor: pointer"
      >
        <i class="bx bx-list-ul"></i> EXCEL
      </download-excel>
    </a>
    <a
      class="btn btn-xs btn-success float-end me-3"
      @click="print('printArea', 'Result')"
      href="javascript:;"
    >
      <i class="bx bx-printer"></i> PRINT
    </a>
    <a
      v-if="download_tabulation"
      class="btn btn-xs btn-info float-end me-3"
      @click="$parent.downloadPDF()"
      href="javascript:;"
    >
      <div v-if="$root.submit" class="loading">processing..</div>
      <span v-else> <i class="bx bx-download"></i> DOWNLOAD</span>
    </a>
    <a
      v-if="download_marksheet"
      class="btn btn-xs btn-info float-end me-3"
      @click="$parent.downloadBulkMarksheet()"
      href="javascript:;"
    >
      <div v-if="$root.submit" class="loading">processing..</div>
      <span v-else> <i class="bx bx-download"></i> DOWNLOAD MARKSHEET</span>
    </a>
  </div>
</template>

<script>
export default {
  props: {
    download_marksheet: { default: false },
    excel: { default: true },
    download_tabulation: { default: false },
  },
};
</script>