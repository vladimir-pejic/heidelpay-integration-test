@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                {!! Form::open(['method' => 'post', 'route' => 'wirecard.post']) !!}
                <div class="form-group">
                    {{ Form::label('amount', 'Amount') }}
                    {{ Form::text('amount', 10.04, ['class' => 'form-control', 'placeholder' => 'Card No.', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('card', 'Card No.') }}
                    {{ Form::text('card', '5413330300002004', ['class' => 'form-control', 'placeholder' => 'Card No.', 'autocomplete' => 'off']) }}
                </div>
                <div class="form-group">
                    {{ Form::label('cvv', 'CCV') }}
                    {{ Form::text('cvv', 004, ['class' => 'form-control', 'placeholder' => 'CCV', 'autocomplete' => 'off']) }}
                </div>
                <div class="">
                    {{ Form::label('expiry-month', 'Expiration Date') }}
                </div>
                <div class="form-inline">
                    {{ Form::selectRange('expiry-month', 1, 12, 01, ['class' => 'form-control', 'placeholder' => 'Select Month']) }}
                    {{ Form::selectRange('expiry-year', \Carbon\Carbon::now()->format('Y'), \Carbon\Carbon::now()->addYear(10)->format('Y'), 2019, ['class' => 'form-control', 'placeholder' => 'Select Year']) }}
                </div>
                <div class="form-group">
                    {!! Form::submit('Send payment', ['class' => 'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>


@endsection