@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">Subscription</div>

                <div class="card-body">
                    @if($user->subscription)
                        <div class="form-group row">
                            <div class="col-md-4">Package: <br>{{ $user->subscription->name }}</div>
                            <div class="col-md-2"><br>{{ $user->subscription->months }} Months</div>
                            <div class="col-md-4">Active for period: <br>Months from invoice</div>
                            <div class="col-md-2">
                                {{ Form::open(['route' => 'subscription.cancel']) }}
                                    {{ Form::submit('Cancel', ['class' => 'btn btn-danger']) }}
                                {{ Form::close() }}
                            </div>
                        </div>
                    @else
                        <div class="form-group row">
                            <div class="col-md-6">
                                <b>Subscription</b>
                            </div>
                            <div class="col-md-2">
                                <b>Months</b>
                            </div>
                            <div class="col-md-2">
                                <b>Price</b>
                            </div>
                            <div class="col-md-2">

                            </div>
                        </div>
                        @foreach($subscriptions as $subscription)
                            {{ Form::open(['route' => ['subscription.store', $subscription->id]]) }}
                            <div class="form-group row">
                                <div class="col-md-6">
                                    {{ $subscription->name }}
                                </div>
                                <div class="col-md-2">
                                    {{ $subscription->months }}
                                </div>
                                <div class="col-md-2">
                                    {{ $subscription->price }} €
                                </div>
                                <div class="col-md-2">
                                    {{ Form::submit('Subscribe', ['class' => 'btn btn-primary']) }}
                                </div>
                            </div>
                            {{ Form::close() }}
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">Invoices</div>

                <div class="card-body">

                </div>
            </div>


        </div>
    </div>
</div>
@endsection
