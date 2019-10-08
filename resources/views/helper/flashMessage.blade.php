<?php
$alert_type = Session::get('alertType');
$alert_message = Session::get('alertMessage');
?>

@if ($alert_type == "success")
<div class="alert alert-success alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{!! $alert_message !!}</strong>
</div>
@endif

@if ($alert_type == "warning")
<div class="alert alert-warning alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{!! $alert_message !!}</strong>
</div>
@endif

@if ($alert_type == "error")
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{!! $alert_message !!}</strong>
</div>
@endif
