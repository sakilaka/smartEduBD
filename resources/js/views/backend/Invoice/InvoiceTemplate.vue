<template>
  <div class="invoice" :style="'height:' + height">
    <div
      v-if="data.institution"
      class="row pb-2"
      style="border-bottom: 2px solid #ccc"
    >
      <div class="col-2">
        <img
          :src="data.institution.logo"
          class="logo"
          style="height: 60px; width: 60px"
        />
      </div>
      <div class="col-10">
        <h3 class="document-type display-7">
          {{ data.institution.name }}
        </h3>
        <p class="text-end mb-0">
          <strong>
            {{ data.invoice_date | formatDate }}
            &nbsp;&nbsp;|&nbsp;&nbsp; #{{ data.invoice_number }}
          </strong>
        </p>
      </div>
    </div>

    <div class="row mt-2">
      <div class="col-7">
        <template v-if="data.student">
          <b>Name :</b> {{ data.student.name_en }} <br />
          <b>Roll Number :</b> {{ data.student.profile.roll_number }}
        </template>
        <br />
        <b>Campus :</b>
        {{ data.campus ? data.campus.name : "" }}
        ({{ data.shift ? data.shift.name : "" }})
        <br />
        <b>Medium/Version. :</b>
        {{ data.medium ? data.medium.name : "" }}
        ({{ data.academic_class ? data.academic_class.name : "" }})
        <br />
        <b>Group. :</b>
        {{ data.group ? data.group.name : "" }}
        ({{ data.section ? data.section.name : "" }})
        <br />
      </div>
      <div v-if="data.status == 'success'" class="col-5 text-center">
        <img
          :src="`${$root.baseurl}/images/paid.png`"
          style="height: 100px; width: 100px"
        />
      </div>
    </div>
    <h5 class="text-center mb-3 mt-2">
      <span class="invoice-text">PAYMENT INVOICE</span>
    </h5>
    <table class="table table-bordered table-striped text-center">
      <thead>
        <tr>
          <th style="width: 90px">Sl No.</th>
          <th v-if="month">Month</th>
          <th>Description</th>
          <th style="width: 130px">Amount</th>
        </tr>
      </thead>
      <slot name="account-heads">
        <tbody>
          <tr>
            <td>{{ "01" }}</td>
            <td>{{ data.head ? data.head.name_en : "" }}</td>
            <td>
              {{
                (parseFloat(data.discount_amount) + parseFloat(data.amount))
                  | currency
              }}
            </td>
          </tr>
          <tr>
            <td colspan="2" class="text-end">Discount</td>
            <td class="fw-bold">{{ data.discount_amount | currency }}</td>
          </tr>
          <tr>
            <td colspan="2" class="text-end">Paid Amount</td>
            <td class="fw-bold">{{ data.amount | currency }}</td>
          </tr>
        </tbody>
      </slot>
    </table>
    <p class="inword">Inword : {{ Number(data.amount) | inWords }} taka only</p>
    <br />

    <p style="margin-bottom: 0px" class="bottom-page text-center">
      Powered By Dynamic Host BD
    </p>
  </div>
</template>

<script>
export default {
  name: "InvoiceTemplate",
  props: ["data", "admission", "month", "height"],
};
</script>