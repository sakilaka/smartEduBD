<template>
  <div class="col-lg-12">
    <div class="modal-dialog modal-fail">
      <div class="modal-content">
        <div class="modal-header">
          <div class="icon-box">
            <i class="mdi mdi-close"></i>
          </div>
          <h4 class="modal-title col-12">Failed!</h4>
        </div>
        <div class="modal-body">
          <p class="text-center">
            Your payment has been Failed. Please try again.
          </p>
        </div>
        <div class="modal-footer">
          <router-link
            :to="'/'"
            class="btn btn-success btn-block"
            data-dismiss="modal"
            >OK</router-link
          >
        </div>
      </div>
    </div>
  </div>
</template>