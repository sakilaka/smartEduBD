<template>
  <div class="col-lg-12">
    <div class="modal-dialog modal-success">
      <div class="modal-content">
        <div class="modal-header">
          <div class="icon-box">
            <i class="mdi mdi-check"></i>
          </div>
          <h4 class="modal-title col-12">Success!</h4>
        </div>
        <div class="modal-body">
          <p class="text-center">Your payment has been confirmed. Thank You.</p>
        </div>
        <div class="modal-footer">
          <router-link
            :to="'/member/payment-history'"
            class="btn btn-success btn-block"
            data-dismiss="modal"
          >OK</router-link>
        </div>
      </div>
    </div>
  </div>
</template>
<style>
.modal-success {
  color: #636363;
  width: 325px;
}
.modal-success .modal-content {
  padding: 20px;
  border-radius: 5px;
  border: none;
}
.modal-success .modal-header {
  border-bottom: none;
  position: relative;
}
.modal-success h4 {
  text-align: center;
  font-size: 26px;
  margin: 30px 0 -15px;
}
.modal-success .form-control,
.modal-success .btn {
  min-height: 40px;
  border-radius: 3px;
}
.modal-success .close {
  position: absolute;
  top: -5px;
  right: -5px;
}
.modal-success .modal-footer {
  border: none;
  text-align: center;
  border-radius: 5px;
  font-size: 13px;
}
.modal-success .icon-box {
  color: #fff;
  position: absolute;
  margin: 0 auto;
  left: 0;
  right: 0;
  top: -70px;
  width: 95px;
  height: 95px;
  border-radius: 50%;
  z-index: 9;
  background: #82ce34;
  padding: 15px;
  text-align: center;
  box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
}
.modal-success .icon-box i {
  font-size: 58px;
  position: relative;
  top: -10px;
}
.modal-success.modal-dialog {
  margin-top: 80px;
}
.modal-success .btn {
  color: #fff;
  border-radius: 4px;
  background: #82ce34;
  text-decoration: none;
  transition: all 0.4s;
  line-height: normal;
  border: none;
}
.modal-success .btn:hover,
.modal-success .btn:focus {
  background: #6fb32b;
  outline: none;
}
.trigger-btn {
  display: inline-block;
  margin: 100px auto;
}
</style>
