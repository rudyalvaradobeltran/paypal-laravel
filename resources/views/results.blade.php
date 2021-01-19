@extends('layout')
@section('content')
    @if(session('status'))
        <div class="col-lg-6 offset-lg-3">
            <div class="card text-center">
                <div class="card-body">
                    <img class="card-img-top paypal-logo" src="images/paypal.png" alt="PayPal">
                    <div class="mt-4">
                        {{ session('status') }}
                    </div>
                    <a class="btn btn-primary mt-4" href="/">Return</a>
                </div>
            </div>
        </div>
    @else
        <script>window.location.href = "./"</script>
    @endif
@endsection