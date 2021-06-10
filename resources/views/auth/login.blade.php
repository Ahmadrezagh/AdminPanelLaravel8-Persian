@extends('layouts.auth')
@section('title')
Login
@endsection
@section('content')

<div class="login-box">
    <div class="login-logo">
      <a href="#"><b>صفحه</b> ورود </a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body " >
        <p class="login-box-msg">ورود به حساب کاربری</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
          <div class="input-group mb-3">
            <input id="email" type="email" class="form-control text-right @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="ایمیل" autofocus>

            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>

          </div>
          <div class="input-group mb-3">
            <input id="password" type="password" class="form-control text-right @error('password') is-invalid @enderror" name="password" required placeholder="رمز عبور" autocomplete="current-password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">

            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">ورود</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <p class="mb-1 text-right">
          <a href="{{ route('password.request') }}">رمز عبورم را فراموش کرده ام</a>
        </p>
        <p class="mb-0 text-right">
          <a href="{{URL::to('/')}}/register" class="text-center">ثبت نام در وبسایت</a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->
@endsection
