<style>
    .modal-success {
        color: #555;
        width: 325px;
    }

    .modal-success .modal-content {
        padding: 20px;
        border: none;
        box-shadow: 0 0 30px rgb(212 213 212);
        background: #dbf9bd;
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
        background: #82ce34;
        padding: 15px;
        text-align: center;
        box-shadow: 0px 2px 2px rgba(0, 0, 0, 0.1);
    }

    .modal-success.modal-dialog {
        margin-top: 80px;
    }

    .modal-success .btn {
        background: #82ce34;
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24"><path fill="currentColor" d="m10 15.586l-3.293-3.293l-1.414 1.414L10 18.414l9.707-9.707l-1.414-1.414z"/></svg>
                </div>
                <h4 class="modal-title col-12">Success!</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">Your payment has been confirmed. Thank You.</p>
            </div>
            <div class="text-center">
                <a href="{{ url(request('redirect')) }}" class="btn btn-success btn-block text-white"> BACK </a>
            </div>
        </div>
    </div>
</div>