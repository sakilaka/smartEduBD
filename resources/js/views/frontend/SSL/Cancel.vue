<template>
  <div class="col-lg-12">
    <div class="modal-dialog modal-cancel">
      <div class="modal-content">
        <div class="modal-header">
          <div class="icon-box">
            <i class="fas fa-exclamation"></i>
          </div>
          <h4 class="modal-title col-12">Canceled!</h4>
        </div>
        <div class="modal-body">
          <p class="text-center">
            Your payment has been canceled. Please try again.
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