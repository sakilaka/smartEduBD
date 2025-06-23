<template>
  <div class="row">
    <div class="col-4">
      <table class="table table-sm table-bordered table-striped">
        <thead>
          <tr>
            <th colspan="2" class="text-center">ACADEMIC PAYMENT</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Service Charge</td>
            <th class="text-center">
              {{ item.total_service_charge | currency }}
            </th>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  props: ["item"],
};
</script>