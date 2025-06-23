
<style>
.modal-success {
  color: #555;
  width: 325px;
}
.modal-success .modal-content {
  padding: 20px;
  border: none;
  box-shadow: 0 0 30px rgb(212 213 212);
  background: #f9bdbd;
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
  background: #ce4234;
  padding: 15px;
  text-align: center;
  box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
}
.modal-success.modal-dialog {
  margin-top: 80px;
}
.modal-success .btn {
  background: #ce4234;
  padding: 6px 30px;
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

<div class="col-lg-12 mb-5 d-flex justify-content-center">
    <div class="modal-dialog modal-success">
      <div class="modal-content">
        <div class="modal-header">
          <div class="icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" style="fill: rgb(255, 255, 255);transform: ;msFilter:;"><path d="m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"></path></svg>
          </div>
          <h4 class="modal-title col-12">Canceled!</h4>
        </div>
        <div class="modal-body">
          <p class="text-center">
            Your payment has been canceled. Please try again.
          </p>
        </div>
        <div class="text-center">
          <a href="{{ url(request('redirect')) }}" class="btn btn-success btn-block text-white"> BACK </a>
        </div>
      </div>
    </div>
  </div>