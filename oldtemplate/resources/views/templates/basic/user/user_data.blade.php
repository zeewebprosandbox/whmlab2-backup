@extends($activeTemplate.'layouts.frontend')

@section('content')
<div class="pt-60 pb-60 bg--light section-full">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card custom--card style-two">
                    <div class="card-header">
                        <h6 class="card-title text-center">{{ __($pageTitle) }}</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('user.data.submit') }}">
                            @csrf
                            <div class="row gy-3">
                                <div class="col-12 text-center">
                                    <p class="mb-0 text-muted">@lang('Add your phone number to finish account setup.')</p>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Phone Number')</label>
                                        <input type="tel" name="mobile" value="{{ old('mobile', $user->mobile) }}" class="form-control form--control h-45" placeholder="@lang('Phone number')" required>
                                    </div>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
