@extends('layout')
@section('content')
    <div class="col-lg-6 offset-lg-3">
        <div class="card text-center">
            <div class="card-body">
                <img class="card-img-top paypal-logo" src="images/paypal.png" alt="PayPal">
                <div class="list-group mt-4">
                    <a href="#" class="list-group-item list-group-item-action active">
                        <h5 class="float-left mb-0">Checkout</h5>
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <span class="float-left">Basic Service</span>
                        <span class="float-right">3.99</span>
                    </a>
                </div>
                <form action="/paypal/pay" method="get">
                    @csrf
                    <input class="btn btn-primary mt-4" type="submit" value="Pay Now">
                </form>
            </div>
        </div>
    </div>
@endsection