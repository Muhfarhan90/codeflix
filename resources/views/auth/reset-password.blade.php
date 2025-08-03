@extends('layouts.auth')

@section('title', 'Reset Password')

@section('page-title', 'Reset Password')

@section('content')
    <form action="{{ route('password.update') }}" class="form" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ $request->email }}">
        {{-- <div class="mb-3 f-email">
            <input type="email" name="email" class="form-control form-email @error('email') is-invalid @enderror"
                id="InputEmail" value="{{ old('email', $request->email) }}" required>
            <label for="InputEmail" class="form-label form-label-email">Email</label>
        </div> --}}
        <div class="mb-3 f-password">
            <input type="password" name="password"
                class="form-control form-password @error('password') is-invalid @enderror" id="InputPassword" required>
            <label for="InputPassword" class="form-label form-label-password">New Password</label>
            <i class="fa fa-eye-slash toggle-password" id="togglePassword"></i>
        </div>
        <div class="mb-3 f-password">
            <input type="password" name="password_confirmation"
                class="form-control form-password @error('password_confirmation') is-invalid @enderror"
                id="InputPasswordConfirmation" required>
            <label for="InputPasswordConfirmation" class="form-label form-label-password">Confirm Password</label>
            <i class="fa fa-eye-slash toggle-password" id="togglePassword"></i>
        </div>
        <button type="submit" class="btn btn-primary btn-sign-in">Reset Password</button>
    </form>
@endsection
