<div class="modal inmodal" id="2famodal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated bounceInRight">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <i class="fa fa-qrcode modal-icon"></i>
        <h4 class="modal-title">2 Factor Authentication</h4>
        {{-- <small class="font-bold">QR code and key.</small> --}}
      </div>

      @if (!Auth::user()->enable_2_auth)
      <form id="verifyForm">
        <div class="modal-body">
          <h5 class="text-center">
            <img src="" alt="qrcode_img" id = "qrcode_img">
            <span class="text-center text-danger" id="secretkey" style="font-size: 1.2rem;"></span>
          </h5>
          <h4 class="text-center">2FA Code</h4>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-mobile font-size-20"></i></span>
            <input name="verifyCode" min="0" max="999999" step="1" class="form-control border-right-0" id="verifyCode" type="number" placeholder="6 digit number" autocomplete="off" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
      @else
      <form id="verifyForm">
        <div class="modal-body">
          <h4 class="text-center">2FA Code</h4>
          <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-mobile font-size-20"></i></span>
            <input name="verifyCode" min="0" max="999999" step="1" class="form-control border-right-0" id="verifyCode" type="number" placeholder="6 digit number" autocomplete="off" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
      @endif
    </div>
  </div>
</div>

<script>
const load2FaInfor = () => {
  $.ajax({
      type: "POST",
      cache: false,
      url : "{{url('/get2FACode')}}",
      data: { "_token": "{{ csrf_token() }}" },
      success: function(data) {
        data = JSON.parse(data);
        $("#qrcode_img").attr("src", data.qrCodeUrl);
        $("#secretkey").html(data.secret);
        $("#2famodal").modal("show");
      }
  });
}

window.onload = function() {
  $("#verifyForm").bind("submit", ($e) => {
    $e.preventDefault();
    const verifyCode = $("#verifyCode").val();

    $.ajax({
        type: "POST",
        cache: false,
        url : "{{url('/verify2FACode')}}",
        data: { "_token": "{{ csrf_token() }}", verifyCode },
        success: function(data) {
          data = JSON.parse(data);
          if (data.result) {
            window.location.reload();
          } else showToast("warning", "Invalid 2FA Code!");
        }
    });
    // showToast("success", "asdfdsf");
    return false;
  });
};

const showToast = (type, message) => {
  toastr.options.timeOut = 10000;
  $("#toastrOptions").text("Command: toastr["
          + type
          + "](\""
          + ''
          + (message ? "\", \"" + message : '')
          + "\")\n\ntoastr.options = "
          + JSON.stringify(toastr.options, null, 2)
  );
  var $toast = toastr[type]('', message);
}
</script>
