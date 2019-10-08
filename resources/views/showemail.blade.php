@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="ibox float-e-margins">
            <div class="ibox-content mailbox-content">
                <div class="file-manager">
                    <a class="btn btn-block btn-primary compose-mail" href="{{url('compose')}}">Compose Mail</a>
                    <div class="space-25"></div>
                    <h5>Folders</h5>
                    <ul class="folder-list m-b-md" style="padding: 0">
                        <li><a href="{{url('inbox')}}"> <i class="fa fa-inbox "></i> Inbox
                            <span class="label label-warning pull-right"><?php echo count($inbox); ?></span>
                        </a></li>
                        <li><a href="{{url('sentmail')}}">
                            <i class="fa fa-envelope-o"></i> Sent Mail
                            <span class="label label-warning pull-right"><?php echo count($outbox); ?></span>
                        </a></li>
                    </ul>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9 animated fadeInRight">
    <div class="mail-box-header">
        <h2>
            View Mail
        </h2>
        <div class="mail-tools tooltip-demo m-t-md">


            <h3>
                <span class="font-normal">Subject: </span><?php echo $email->subject; ?>
            </h3>
            <h5>
                <span class="pull-right font-normal"><?php echo $email->created_at; ?></span>
                <?php
                    if($email->receiver == Auth::user()->email)
                        echo '<span class="font-normal">From: </span>'.$email->email;
                    else
                        echo '<span class="font-normal">To: </span>'.$email->receiver;
                ?>
            </h5>
        </div>
    </div>
        <div class="mail-box">


        <div class="mail-body">
        <?php echo $email->content; ?>
        </div>
    </div>
    </div>
</div>

@stop

@section('scripts')

@endsection
<!-- createevent.blade.php -->
