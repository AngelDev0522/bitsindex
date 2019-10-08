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
            Sent Mails (<?php echo count($outbox); ?>)
        </h2>
    </div>
        <div class="mail-box">
            <table class="table table-hover table-mail">
                <tbody>
                    <?php
                        for($i=0;$i<count($outbox);$i++)
                        {
                            if($outbox[$i]->status == '0')
                                echo "<tr class='unread'>";
                            else
                                echo "<tr class='read'>";
                            echo "<td class='mail-ontact'><a href='".url('/'.'showemail/'.$outbox[$i]->id)."'>".$outbox[$i]->receiver."</a></td>";
                            echo "<td class='mail-subject'><a href='".url('/'.'showemail/'.$outbox[$i]->id)."'>".$outbox[$i]->subject."</a></td>";
                            echo "<td class='text-right mail-date'><a href='".url('/'.'showemail/'.$outbox[$i]->id)."'>".$outbox[$i]->created_at."</td>";
                            echo "<td><a href='".url('/'.'removemail/'.$outbox[$i]->id)."'><i class='fa fa-trash'></a></td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop

@section('scripts')

@endsection
<!-- createevent.blade.php -->
