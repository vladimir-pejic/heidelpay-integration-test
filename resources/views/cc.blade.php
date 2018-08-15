@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-8">Subscriber info</div>
            <div class="col-md-4">
                {!! Form::open(['method' => 'post', 'route' => 'cc.post']) !!}
                <input type="hidden" id="stateId" name="stateId" value="0867e93affffffff276a6c47" />
                <div class="form-group">
                </div>
                <div class="form-group">
                    {{ Form::label('account.holder', 'Card Holder') }}
                    {{ Form::text('account.holder', null, ['class' => 'form-control', 'placeholder' => 'Card Holder', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('account.number', 'Card No.') }}
                    {{ Form::text('account.number', null, ['class' => 'form-control', 'placeholder' => 'Card No.', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('account.brand', 'Card Brand') }}
                    {{ Form::select('account.brand',
                        ['AMEX' => 'American Express', 'MASTER' => 'MasterCard', 'JCB' => 'JCB', 'VISA' => 'Visa', 'DISCOVERY' => 'Discovery', 'CUP' => 'China Union Pay', 'DINERS' => 'Diners'],
                        null, ['class' => 'form-control', 'placeholder' => 'Select Card Brand'])
                    }}
                </div>
                <div class="form-group">
                    {{ Form::label('account.verification', 'CCV') }}
                    {{ Form::text('account.verification', null, ['class' => 'form-control', 'placeholder' => 'CCV', 'autocomplete' => 'off']) }}
                </div>
                <div class="">
                    {{ Form::label('account.expiry_month', 'Expiration Date') }}
                </div>
                <div class="form-inline">
                    {{ Form::selectRange('account.expiry_month', 1, 12, null, ['class' => 'form-control', 'placeholder' => 'Select Month']) }}
                    {{ Form::selectRange('account.expiry_year', \Carbon\Carbon::now()->format('Y'), \Carbon\Carbon::now()->addYear(10)->format('Y'), null, ['class' => 'form-control', 'placeholder' => 'Select Year']) }}
                </div>
                <div class="form-group">
                    {!! Form::submit('Send payment', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>


        </div>
    </div>

    <script type="text/javascript" src="{{public_path('js/creditCardFrame.js') }}"></script>

@endsection



