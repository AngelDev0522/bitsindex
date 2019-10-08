@extends('layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div id="chatSection">
            <div class="ibox chat-view">
                <div class="ibox-title">
                    {{-- <small class="pull-right text-muted">Last message:  Mon Jan 26 2015 - 18:39:23</small> --}}
                    Chat room panel
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-3">
                            <?php
                                // $imageSrc = '/storage/'.$default_image;
                                // if(Auth::user()->avatar != 'null') {
                                    $imageSrc = '/storage/'.Auth::user()->avatar;
                                // } else {
                                    // $imageSrc = '/storage/'.$default_image;
                                // }
                            ?>
                            <input type="hidden" value="{{ Auth::user()->name }}" id="user_name">
                            <input type="hidden" value="{{ Auth::user()->id }}" id="authId">
                            <input type="hidden" value="{{ $imageSrc }}" id="default_image">
                            <input type="hidden" value="{{ url('') }}" id="base_url">
                            <div class="chat-users">
                                <div class="users-list">
                                    <user-log :users="users" v-on:getcurrentuser="getCurrentUser"></user-log>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="chat-info">
                                <h2>
                                    <span class="label label-success">Welcome to the chat room</span>
                                </h2>
                                <p>
                                    <span class="label label-default">Choose one of you friend from left sidebar to make a conversion.</span>
                                </p>
                            </div>
                            <div class="activate-chat">
                                <chat-log :messages="messages"></chat-log>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="activate-chat">
                                <chat-composer v-on:messagesent="addMessage" :user-id="userId"></chat-composer>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- trick to eliminate VueJS conflict --}}
<script src="{{ URL::asset('/js/app.js ') }}"></script>
@stop

@section('scripts')
<script src="{{ URL::asset('/js/sweetalert.min.js ') }}"></script>
<script src="{{ URL::asset('/js/moment.min.js ') }}"></script>
@endsection
