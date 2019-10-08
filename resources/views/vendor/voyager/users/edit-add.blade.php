@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular)

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->display_name_singular }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <form class="form-edit-add" role="form"
              action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
              method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- PUT Method if we are editing -->
            @if(isset($dataTypeContent->id))
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-8">
                    <div class="panel panel-bordered">
                    {{-- <div class="panel"> --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name">{{ __('voyager::generic.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('voyager::generic.name') }}"
                                       value="{{ $dataTypeContent->name ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label for="email">{{ __('voyager::generic.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="{{ __('voyager::generic.email') }}"
                                       value="{{ $dataTypeContent->email ?? '' }}">
                            </div>

                            <div class="form-group">
                                <label for="password">{{ __('voyager::generic.password') }}</label>
                                @if(isset($dataTypeContent->password))
                                    <br>
                                    <small>{{ __('voyager::profile.password_hint') }}</small>
                                @endif
                                <input type="password" class="form-control" id="password" name="password" value="" autocomplete="new-password">
                            </div>

                            @can('editRoles', $dataTypeContent)
                                <div class="form-group">
                                    <label for="default_role">{{ __('voyager::profile.role_default') }}</label>
                                    @php
                                        $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                        $row     = $dataTypeRows->where('field', 'user_belongsto_role_relationship')->first();
                                        $options = $row->details;
                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>
                                <div class="form-group">
                                    <label for="additional_roles">{{ __('voyager::profile.roles_additional') }}</label>
                                    @php
                                        $row     = $dataTypeRows->where('field', 'user_belongstomany_role_relationship')->first();
                                        $options = $row->details;
                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>
                            @endcan
                            @php
                            if (isset($dataTypeContent->locale)) {
                                $selected_locale = $dataTypeContent->locale;
                            } else {
                                $selected_locale = config('app.locale', 'en');
                            }

                            @endphp
                            <div class="form-group">
                                <label for="locale">{{ __('voyager::generic.locale') }}</label>
                                <select class="form-control select2" id="locale" name="locale">
                                    @foreach (Voyager::getLocales() as $locale)
                                    <option value="{{ $locale }}"
                                    {{ ($locale == $selected_locale ? 'selected' : '') }}>{{ $locale }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <?php $coins = ['litecoin', 'peercoin', 'ripple'];
                            $keyTypes = ['address', 'secret'];
                            ?>

                            @foreach ($coins as $coin)
                                @foreach ($keyTypes as $type)
                                    <?php $walletKey = $coin.'_'.$type; ?>
                                    <div class="form-group">
                                        <label for="{{$walletKey}}">{{ ucwords($coin.' '.$type) }}</label>
                                        <input type="text" class="form-control" id="{{$walletKey}}" name="{{$walletKey}}" {{-- placeholder="{{ ucwords($coin.' '.$type) }}"--}}
                                            value="{{ $dataTypeContent->$walletKey ?? '' }}">
                                    </div>
                                @endforeach
                            @endforeach

                            <div class="form-group">
                                {{-- <label for="profile_visible">{{ __('Profile Visibility') }}</label>
                                <input type="checkbox" id="profile_visible" name="profile_visible"
                                        data-on="{{ __('voyager::bread.soft_deletes_off') }}"
                                        data-off="{{ __('voyager::bread.soft_deletes_on') }}"
                                       {{ $dataTypeContent->profile_visible ? 'checked' : '' }}> --}}
                                {{-- <input type="checkbox" id="permission-{{$perm->id}}" name="permissions[]" class="the-permission" value="{{$perm->id}}" @if(in_array($perm->key, $role_permissions)) checked @endif>
                                <label for="permission-{{$perm->id}}">{{title_case(str_replace('_', ' ', $perm->key))}}</label> --}}

                                <label for="profile_visible">{{ 'Profile Visibility' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="profile_visible"
                                    name="profile_visible"
                                    class="toggleswitch"
                                    data-on="{{ 'Public' }}"
                                    data-off="{{'Private'}}"
                                    {{ $dataTypeContent->profile_visible ? 'checked' : '' }}
                                >
                            </div>

                            <div class="form-group">
                                <label for="online">{{ 'Online Status' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="online"
                                    name="online"
                                    class="toggleswitch"
                                    data-on="{{ 'Online' }}"
                                    data-off="{{ 'Offline' }}"
                                    {{ $dataTypeContent->online ? 'checked' : '' }}
                                >
                            </div>

                            <div class="form-group">
                                <label for="activated">{{ 'Account Activation' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="activated"
                                    name="activated"
                                    class="toggleswitch"
                                    data-on="{{ 'Activated' }}"
                                    data-off="{{ 'Deactivated' }}"
                                    {{ $dataTypeContent->activated ? 'checked' : '' }}
                                >
                            </div>

                            <div class="form-group">
                                <label for="banned">{{ 'Login Banned' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="banned"
                                    name="banned"
                                    class="toggleswitch"
                                    data-on="{{ 'Banned' }}"
                                    data-off="{{ 'Allowed' }}"
                                    {{ $dataTypeContent->banned ? 'checked' : '' }}
                                >
                            </div>

                            <div class="form-group">
                                <label for="enable_chat">{{ 'Enable Chat' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="enable_chat"
                                    name="enable_chat"
                                    class="toggleswitch"
                                    data-on="{{ 'enable' }}"
                                    data-off="{{ 'disable' }}"
                                    {{ $dataTypeContent->enable_chat ? 'checked' : '' }}
                                >
                            </div>

                            <div class="form-group">
                                <label for="enable_email">{{ 'Enable Email' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="enable_email"
                                    name="enable_email"
                                    class="toggleswitch"
                                    data-on="{{ 'enable' }}"
                                    data-off="{{ 'disable' }}"
                                    {{ $dataTypeContent->enable_email ? 'checked' : '' }}
                                >
                            </div>

                            <div class="form-group">
                                <label for="enable_calendar">{{ 'Enable Calendar' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="enable_calendar"
                                    name="enable_calendar"
                                    class="toggleswitch"
                                    data-on="{{ 'enable' }}"
                                    data-off="{{ 'disable' }}"
                                    {{ $dataTypeContent->enable_calendar ? 'checked' : '' }}
                                >
                            </div>

                            <div class="form-group">
                                <label for="enable_wallet">{{ 'Enable Wallet' }}</label>
                                <br/>
                                <input
                                    type="checkbox"
                                    id="enable_wallet"
                                    name="enable_wallet"
                                    class="toggleswitch"
                                    data-on="{{ 'enable' }}"
                                    data-off="{{ 'disable' }}"
                                    {{ $dataTypeContent->enable_wallet ? 'checked' : '' }}
                                >
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="panel panel panel-bordered panel-warning">
                        <div class="panel-body">
                            <div class="form-group">
                                @if(isset($dataTypeContent->avatar))
                                    <img src="<?php echo url('/')?>\storage\{{ $dataTypeContent->avatar }}" style="width:200px; height:auto; clear:both; display:block; padding:2px; border:1px solid #ddd; margin-bottom:10px;" />
                                @endif
                                <input type="file" data-name="avatar" name="avatar">
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <button type="submit" class="btn btn-primary pull-right save">
                {{ __('voyager::generic.save') }}
            </button>
        </form>
        @if($dataTypeContent->role_id == 1)
        <div class="row">
            <div class="col-md-6">
                <h4 class="login_login">2 Factor Authentication</h4>
                <ul>
                    <li>
                        Install Google Authenticator application to your mobile phone<br><br>
                        <div class="row">
                            <div class="col-md-6 text-right">
                                <a class="appbadge" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&amp;hl=en" target="_blank">
                                    <img height="45" src="http://www.niftybuttons.com/googleplay/googleplay-button8.png" alt="Get on Google Play">
                                </a>
                            </div>
                            <div class="col-md-6 text-left">
                                <a class="appbadge" href="https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8?at=1000lc66" target="_blank">
                                    <img height="45" src="http://www.niftybuttons.com/itunes/itunesbutton1.png" alt="iTunes Button">
                                </a>
                            </div>
                        </div><br>
                    </li>
                    <li>Scan QR code.</li>
                    <li>Submit 2FA code.</li>
                </ul>

                @if (!Auth::user()->enable_2_auth)
                <button class="btn btn-primary full-width" onclick="onEnable()">Enable</button>
                @else
                <button class="btn btn-warning full-width" onclick="onDisable()">Disable</button>
                @endif
                <br><br>
            </div>
        </div>
        @endif
        <iframe id="form_target" name="form_target" style="display:none"></iframe>
        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post" enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
            {{ csrf_field() }}
            <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        </form>
    </div>
@include('profile.2fa')
@stop
@section('javascript')
    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();
        });
        const onEnable = () => {
            load2FaInfor();
        }
        const onDisable = () => {
            load2FaInfor();
        }
    </script>
@stop
