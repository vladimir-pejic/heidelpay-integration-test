@extends('layouts.app')

@section('content')

    <div class="container">
        {!! Form::open(['method' => 'post', 'route' => 'wirecard.post']) !!}
        <div class="row">
            <div class="col-md-4">

                <div class="form-group">
                    {{ Form::label('amount', 'Amount') }}
                    {{ Form::text('amount', 10.04, ['class' => 'form-control', 'placeholder' => 'Card No.', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('card', 'Card No.') }}
                    {{ Form::text('card', '4012000300001003', ['class' => 'form-control', 'placeholder' => 'Card No.', 'autocomplete' => 'off']) }}
                </div>
                <div class="">
                    {{ Form::label('expiry-month', 'Expiration Date') }}
                </div>
                <div class="form-inline">
                    {{ Form::selectRange('expiry_month', 1, 12, 01, ['class' => 'form-control', 'placeholder' => 'Select Month']) }}
                    {{ Form::selectRange('expiry_year', \Carbon\Carbon::now()->format('Y'), \Carbon\Carbon::now()->addYear(10)->format('Y'), 2019, ['class' => 'form-control', 'placeholder' => 'Select Year']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('cvv', 'CCV') }}
                    {{ Form::text('cvv', '003', ['class' => 'form-control', 'placeholder' => 'CCV', 'autocomplete' => 'off']) }}
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    {{ Form::label('first_name', 'First Name') }}
                    {{ Form::text('first_name', 'Vladimir', ['class' => 'form-control', 'placeholder' => 'First Name', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('last_name', 'Last Name') }}
                    {{ Form::text('last_name', 'Pejic', ['class' => 'form-control', 'placeholder' => 'Last Name', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('street_address', 'Street Address') }}
                    {{ Form::text('street_address', 'Vlascika 27', ['class' => 'form-control', 'placeholder' => 'Street Address', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('city', 'City') }}
                    {{ Form::text('city', 'Banja Luka', ['class' => 'form-control', 'placeholder' => 'City', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('country', 'Country') }}
                    {{ Form::text('country', 'Bosnia and Hercegovina', ['class' => 'form-control', 'placeholder' => 'Country', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {!! Form::submit('Send payment', ['class' => 'btn btn-success']) !!}
                </div>

            </div>
        </div>
        {!! Form::close() !!}
    </div>

@endsection