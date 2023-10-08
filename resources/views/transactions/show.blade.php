
@extends('layouts.app')

@section('content')
    <div class="container">

        @if(session('error'))
        <div class="alert alert-danger mt-4">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-6">

        <form method="POST" action="{{ route('deposits.store') }}" class="mt-4">
            @csrf
            <div class="input-group mb-3">
                <label for="amount">Deposit:&nbsp;&nbsp;</label>
                <input type="number" class="form-control" name="amount" placeholder="Enter deposit amount" id="amount" aria-describedby="submitBtn1" required>
                <button type="submit" class="btn btn-primary" id="submitBtn1">Add Deposit</button>
            </div>
        </form>
        </div>
        <div class="col-6">

        <form method="POST" action="{{ route('withdrawals.store') }}" class="mt-4">
            @csrf
            <div class="input-group mb-3">
                <label for="amount">Withdrawal:&nbsp;&nbsp;</label>
                <input type="number" class="form-control" name="amount" placeholder="Enter withdrawal amount" id="amount" aria-describedby="submitBtn" required>
                @error('amount')
                    <div class="invalid-feedback">
                        {{ $message1 }}
                    </div>
                @enderror
                <button type="submit" class="btn btn-primary" id="submitBtn">Withdraw</button>
            </div>
        </form>
        </div>
    </div>
        

        

        <h2>Current Balance: ৳{{ $currentBalance }}</h2>

        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Transaction Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->date }}</td>
                        <td>৳{{ $transaction->amount }}</td>
                        <td>{{ $transaction->transaction_type }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
