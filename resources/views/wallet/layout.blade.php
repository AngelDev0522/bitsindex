@extends('layouts.app')

@section('content')
{{-- @if (\Session::has('success'))
            <div class="alert alert-success">
            <p>{{ \Session::get('success') }}</p>
</div><br />
@endif --}}

<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">{{$coinOfficialName}} Balance</div>
            <div class="panel-body">
                <div class="text-center">
                    <h1 id="balance_native">{{$nativeBalance}} {{$coinUnit}}</h1>
                    <hr>
                    <h2 id="balance_usd">{{$usdBalance}} $</h2>
                </div>
                 {{--
                        <div class="text-center">
                            <button class="btn btn-default">
                                <i class="fa fa-refresh"></i>
                            </button>
                        </div> --}}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">{{$coinOfficialName}} Address</div>
            <div class="panel-body">
                <div class="text-center">
                    <img class="" id="address_qrcode"
                        src="data:image/jpg;base64, {!! base64_encode(QrCode::format('png')->color(38, 38, 38, 0.85)->backgroundColor(255, 255, 255, 0.82)->size(120)->generate($address)) !!}">
                    <strong id="{{$coinAddress}}">{{$address}}</strong>
                </div>
                <hr>
                <div class="text-center">
                    <button class="btn btn-default" id="copy_button" onclick="copyAddress()">
                        <i class="fa fa-copy"></i>
                    </button>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#importModal">
                        Import
                    </button>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exportModal">
                        Export
                    </button>

                    {{-- <button class="btn btn-default">
                                    <i class="fa fa-qrcode"></i>
                                </button> --}}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Send {{$coinOfficialName}}</div>
            <div class="panel-body">
                <form onsubmit="return send(event)" method="post" action="{{url("wallet/$coinName/send")}}" class="form-horizontal form-signin" >
                    @csrf
                    <div class="form-group {{ $errors->has('receiver') ? 'has-error' : ''}}">
                        <div class="col-sm-12">
                            {{-- {!! Form::text('receiver', null, ['class' => 'form-control', 'id' => 'receiver_address',
                            'placeholder '=>'Receiver Address', 'required' => 'required']) !!} --}}
                            <input type="text" class="form-control" name="receiver" id="receiver_address" placeholder="Receiver Address" required>
                            {!! $errors->first('receiver', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('amount') ? 'has-error' : ''}}">
                        <div class="col-sm-12">
                            <input type="number" class="form-control" step="0.00000001" name="amount" id="send_amount" placeholder="Amount" required>
                            {{-- {!! Form::number('amount', null, ['class' => 'form-control', 'id'=>'send_amount', 'placeholder
                            '=>'Amount', 'required' => 'required']) !!} --}}
                            {!! $errors->first('amount', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    {{-- <button class="btn btn-lg btn-primary btn-block"  name="Submit" value="Login" type="Submit">Login</button> --}}
                    <hr>
                    <div class="text-center">
                        <button class="btn btn-success" name="Submit" type="Submit">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">Transaction History</div>
            <div class="panel-body">
                <table class="table table-striped table-bordered table-responsive-lg table-sm">
                    <thead>
                        <tr>
                            {{-- <th>
                                Ledger
                            </th> --}}
                            <th>
                                No
                            </th>
                            <th>
                                Time
                            </th>
                            {{-- <th>
                                Sender
                            </th>
                            <th>
                                Receiver
                            </th> --}}
                            <th>
                                Transaction ID
                            </th>
                            <th>
                                Sent/Received
                            </th>
                            <th>
                                Amount
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $index => $tx)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{ \Carbon\Carbon::parse($tx->timestamp)->format('m/d/Y h:m:s')}}</td>
                            <td>{!!\App\Http\Controllers\WalletController::genTxLink($tx->hash, $coinName, 'main')!!}</td>
                            <td>{{ isset($tx->isSent) ? ($tx->isSent ? 'Sent' : 'Received') : ($tx->sender == $address ? "Sent" : "Received")}}</td>
                            <td>{{$tx->value + 0}}</td> {{--amount--}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal inmodal" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Import Private Key</h4>
                <small class="font-bold">You may lose access to current wallet permanently. Are you sure?</small>
            </div>
            <form method="post" action="{{url("wallet/$coinName/importsecret")}}" class="form-horizontal form-signin">
            @csrf
               <div class="modal-body">
                   {{-- <p><strong>Notice</strong> Some important stuff here.</p> --}}
                    <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
                        <div class="col-sm-12">
                            <input type="password" name="password" class="form-control" placeholder="Login Password" required>
                            {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('private_key') ? 'has-error' : ''}}">
                        <div class="col-sm-12">
                            <input type="text"  name="private_key" class="form-control" id="private_key" placeholder="Private Key" required>
                            {!! $errors->first('private_key', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    {{-- <button class="btn btn-lg btn-primary btn-block"  name="Submit" value="Login" type="Submit">Login</button> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="Submit" class="btn btn-primary">Import</button>
                </div>
            </form>

        </div>
    </div>
</div>

<div class="modal inmodal" id="exportModal" tabindex="-2" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content animated flipInY">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Export Private Key</h4>
                <small class="font-bold">Are you sure?</small>
            </div>
            <form method="post" action="{{url("wallet/$coinName/exportsecret")}}" class="form-horizontal form-signin">
            @csrf
                <div class="modal-body">
                    {{-- <p><strong>Notice</strong> Some important stuff here.</p> --}}
                    <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
                        <div class="col-sm-12">
                            <input type="password" name="password" class="form-control" placeholder="Login Password" required>
                            {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    {{-- <button class="btn btn-lg btn-primary btn-block"  name="Submit" value="Login" type="Submit">Login</button> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="Submit" class="btn btn-primary">Export</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let nativeBalance = {{$nativeBalance}};
    let usdBalance = {{$usdBalance}};

    function copyAddress(){
        var element = $("#{{$coinAddress}}");
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).text()).select();
        document.execCommand("copy");
        $temp.remove();
        alert("Copied the address to clipboard!");
    }

    function send(event){
        let receiver = $('#receiver_address').val();
        let amount = $('#send_amount').val();
        if(amount > nativeBalance){
            alert('Not enough balance!');
            return event.preventDefault();
        }
        if(receiver.length < 25 || receiver.length > 35){
            alert('{{$coinOfficialName}} address has 25 ~ 35 characters!');
            return event.preventDefault();
        }
        // send
    }
</script>
@endsection
