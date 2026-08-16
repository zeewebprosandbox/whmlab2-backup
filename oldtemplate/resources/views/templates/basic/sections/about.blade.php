@php
    $about = @getContent('about.content', true);
@endphp

<div class="section-full pt-60 pb-60 bg-white">
    <div class="container">
        <div class="row gy-4 align-items-center">
            <div class="col-lg-5">
                <div class="p-4 border rounded-3 bg--light h-100">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="feature-card__icon bg--base text-white">
                            @php echo @$about->data_values->about_icon ?? '<i class="las la-server"></i>'; @endphp
                        </div>
                        <div>
                            <span class="text--base fw-semibold">@lang('ZodHost story')</span>
                            <h3 class="mb-0">{{ __(@$about->data_values->heading) }}</h3>
                        </div>
                    </div>
                    <p class="mb-0">{{ __(@$about->data_values->description) }}</p>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row gy-3">
                    <div class="col-md-4">
                        <div class="custom--card h-100">
                            <div class="card-body">
                                <span class="text--base fw-semibold">2019</span>
                                <h5 class="mt-2 mb-2">@lang('Started with support')</h5>
                                <p class="mb-0 small text-muted">@lang('ZodHost began by simplifying renewals, tickets, and service visibility for growing sites.')</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="custom--card h-100">
                            <div class="card-body">
                                <span class="text--base fw-semibold">2023</span>
                                <h5 class="mt-2 mb-2">@lang('Expanded infrastructure')</h5>
                                <p class="mb-0 small text-muted">@lang('Shared hosting, VPS, dedicated, RDP, streaming, and domain services moved into one account view.')</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="custom--card h-100">
                            <div class="card-body">
                                <span class="text--base fw-semibold">Now</span>
                                <h5 class="mt-2 mb-2">@lang('Built around clarity')</h5>
                                <p class="mb-0 small text-muted">@lang('WHMPanel integration connects provisioning, billing, support PIN checks, and client service access.')</p>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="mt-4 mb-0 text-muted">{{ __(@$about->data_values->sub_heading) }}</p>
            </div>
        </div>
    </div>
</div>
