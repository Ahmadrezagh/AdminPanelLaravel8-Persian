@extends('layouts.auth')
@section('title')
    register
@endsection
@section('content')

    <div class="register-box">
        <div class="register-logo">
            <a href="#"><b>صفحه</b> ثبت نام </a>
        </div>

        <div class="card">
            <div class="card-body register-card-body">
                <p class="login-box-msg">عضویت در وبسایت</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="input-group mb-3">
                        <input id="name" type="text" class="form-control text-right @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="نام و نام خانوادگی" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="email" type="email" class="form-control text-right @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required placeholder="ایمیل" autocomplete="email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control text-right @error('password') is-invalid @enderror" name="password" required placeholder="رمز عبور" autocomplete="new-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="password-confirm" type="password" class="form-control text-right"  name="password_confirmation" required placeholder="تکرار رمز عبور" autocomplete="new-password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input onclick="validate()" type="checkbox" id="agreeTerms" name="terms" value="agree">
                                <label for="agreeTerms">
                                    را قبول دارم <a href="#" type="button" data-toggle="modal" data-target="#exampleModal">قوانین</a>
                                </label>
                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">

                                                <h5 class="modal-title" id="exampleModalLongTitle"></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                {!! setting('terms') !!}
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">بستن</button>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button id="btnSubmit" type="submit" disabled class="btn btn-primary btn-block dsible">ثبت نام</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>


                <div class="text-right mt-3">
                    <a href="{{URL::to('/')}}/login" class="text-center">قبلا ثبت نام کرده ام</a>

                </div>

            </div>
            <!-- /.form-box -->
        </div><!-- /.card -->
    </div>

    <!-- /.register-box -->
    <script>
        function validate() {
            if ($('#agreeTerms').is(':checked')) {
                $('#btnSubmit').removeAttr("disabled");
            } else {
                $("#btnSubmit").attr("disabled", true);
            }
        }
    </script>
@endsection
