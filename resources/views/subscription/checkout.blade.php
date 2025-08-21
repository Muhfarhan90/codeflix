@extends('layouts.subscription')

@section('title', 'Payment Detail')
@section('page-title', 'Payment Detail')

@section('content')
    <div class="mt-4 text-white card bg-dark border-green">
        <div class="card-body">
            <div class="mb-3 row align-items-center">
                <div class="col-8">
                    <h5 class="mb-0">{{ $plan->title }} - {{ $plan->duration }} Hari</h5>
                </div>
                <div class="col-4 text-end">
                    <span class="fs-5">Rp.{{ number_format($plan->price, 0, ',', '.') }}</span>
                </div>
            </div>

            <hr class="border-green">

            <div class="mb-2 row">
                <div class="col-8">Subtotal</div>
                <div class="col-4 text-end">Rp.{{ number_format($plan->price, 0, ',', '.') }}</div>
            </div>

            <div class="mb-2 row">
                <div class="col-8">Ppn 12%</div>
                <div class="col-4 text-end">Rp.{{ number_format($plan->price * 0.12, 0, ',', '.') }}</div>
            </div>

            <hr class="border-green">

            <div class="mb-4 row">
                <div class="col-8">Total payment</div>
                <div class="col-4 text-end fw-bold">Rp.{{ number_format($plan->price * 1.1, 0, ',', '.') }}</div>
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" id="terms" required>
                <label class="form-check-label" for="terms">
                    By continuing the payment, you agree to our
                    <a href="#" class="text-info">Terms and Conditions</a> and
                    <a href="#" class="text-info">Privacy Policy</a>
                </label>
            </div>

            <form action={{ route('subscribe.process') }} method="POST">
                @csrf
                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                <input type="hidden" name="total_payment" value="{{ $plan->price * 0.12 }}">
                <button type="submit" class="w-100 btn btn-green" id="pay-button">Continue</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        function handlePayment(data) {
            if (data.status === 'success') {
                const validationToken = data.validation_token;

                window.snap.pay(data.snap_token, {
                    onSuccess: async function(result) {
                        try {
                            // Kirim request untuk register device
                            const response = await fetch('/transaction/success', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                },
                                body: JSON.stringify({
                                    order_id: result.order_id,
                                    validation_token: validationToken,
                                    transaction_id: result.transaction_id
                                })
                            });

                            const responseData = await response.json();

                            console.log(responseData);

                            if (responseData.status === 'success') {
                                window.location.href = responseData.redirect_url;
                            } else {
                                throw new Error(responseData.message);
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('Failed to process device registration. Please contact support.');
                            window.location.href = '/';
                        }
                    },
                    onPending: function(result) {
                        window.location.href = '/payment/pending';
                    },
                    onError: function(result) {
                        window.location.href = '/payment/error';
                    },
                    onClose: function() {
                        alert('You closed the payment window without completing the payment');
                    }
                });
            } else {
                alert('Payment failed to initialize');
            }
        }
        document.getElementById('pay-button').addEventListener('click', async function(e) {
            e.preventDefault();

            try {
                const response = await fetch('/transaction/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        plan_id: '{{ $plan->id }}',
                        amount: '{{ $plan->price * 1.1 }}'
                    })
                });

                const data = await response.json();
                handlePayment(data);
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to initialize payment');
            }
        });
    </script> --}}
@endsection
