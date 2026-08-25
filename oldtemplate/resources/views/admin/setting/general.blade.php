@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-lg-12 col-md-12 mb-30">
            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group ">
                                    <label> @lang('Site Title')</label>
                                    <input class="form-control" type="text" name="site_name" required value="{{gs('site_name')}}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency')</label>
                                    <input class="form-control" type="text" name="cur_text" required value="{{gs('cur_text')}}">
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6">
                                <div class="form-group ">
                                    <label>@lang('Currency Symbol')</label>
                                    <input class="form-control" type="text" name="cur_sym" required value="{{gs('cur_sym')}}">
                                </div>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required"> @lang('Timezone')</label>
                                <select class="select2 form-control" name="timezone" >
                                    @foreach($timezones as $key => $timezone)
                                    <option value="{{ @$key}}" @selected(@$key == $currentTimezone)>{{ __($timezone) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6">
                                <label class="required"> @lang('Site Base Color')</label>
                                <div class="input-group">
                                    <span class="input-group-text p-0 border-0">
                                        <input type='text' class="form-control colorPicker" value="{{gs('base_color')}}">
                                    </span>
                                    <input type="text" class="form-control colorCode" name="base_color" value="{{ gs('base_color') }}">
                                </div>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6">
                                <label> @lang('Record to Display Per page')</label>
                                <select class="select2 form-control" name="paginate_number" data-minimum-results-for-search="-1">
                                    <option value="20" @selected(gs('paginate_number') == 20 )>@lang('20 items per page')</option>
                                    <option value="50" @selected(gs('paginate_number') == 50 )>@lang('50 items per page')</option>
                                    <option value="100" @selected(gs('paginate_number') == 100 )>@lang('100 items per page')</option>
                                </select>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6 ">
                                <label class="required"> @lang('Currency Showing Format')</label>
                                <select class="select2 form-control" name="currency_format" data-minimum-results-for-search="-1">
                                    <option value="1" @selected(gs('currency_format') == Status::CUR_BOTH)>@lang('Show Currency Text and Symbol Both')</option>
                                    <option value="2" @selected(gs('currency_format') == Status::CUR_TEXT)>@lang('Show Currency Text Only')</option>
                                    <option value="3" @selected(gs('currency_format') == Status::CUR_SYM)>@lang('Show Currency Symbol Only')</option>
                                </select>
                            </div>
                            <div class="form-group col-xl-3 col-sm-6">
                                <label>@lang('Client Live Chat')</label>
                                <div class="form-check form-switch form--switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="show_livechat_user_panel" value="1" id="show_livechat_user_panel" @checked(gs('show_livechat_user_panel'))>
                                    <label class="form-check-label" for="show_livechat_user_panel">@lang('Show on user panel')</label>
                                </div>
                                <small class="text-muted">@lang('Uses the active live chat extension, such as Tawk.to.')</small>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <label> @lang('Invoice Starting')</label>
                                <div class="input-group">
                                    <span class="input-group-text">#</span>
                                    <input class="form-control" type="number" min="1" name="invoice_start" required value="{{ gs('invoice_start') }}">
                                </div> 
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label> @lang('Invoice Incrementation')</label>
                                <input class="form-control" type="number" min="1" name="invoice_increment" required value="{{ gs('invoice_increment') }}">
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label> @lang('Tax Setup')</label>
                                <div class="input-group">
                                    <span class="input-group-text">{{ gs('cur_sym') }}</span>
                                    <input class="form-control" type="number" name="tax" required value="{{ gs('tax') }}">
                                    <span class="input-group-text">%</span>
                                </div> 
                            </div>

                        </div>

                        <!-- Telegram Bot Real-Time Notification Settings -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <h5 class="text--primary m-0 d-flex align-items-center gap-2">
                                        <i class="fab fa-telegram-plane"></i> @lang('Telegram Real-Time Channel Notifications')
                                    </h5>
                                    @if(gs('telegram_bot_token') && gs('telegram_chat_id'))
                                        <button type="button" class="btn btn-sm btn--dark" onclick="document.getElementById('testTelegramForm').submit();">
                                            <i class="fas fa-paper-plane"></i> @lang('Send Test Alert to Channel')
                                        </button>
                                    @endif
                                </div>
                                <small class="text-muted">@lang('Receive instant live alerts in your Telegram channel for new customer registrations, orders, payments, server provisioning, support tickets, and contact messages.')</small>
                            </div>

                            <div class="col-xl-5 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Telegram Bot Token')</label>
                                    <input class="form-control" type="password" name="telegram_bot_token" placeholder="123456789:ABCdefGHIjklMNOpqrSTUvwxYZ" value="{{ gs('telegram_bot_token') }}">
                                    <small class="text-muted">@lang('Create a bot via @BotFather on Telegram and paste the token here.')</small>
                                </div>
                            </div>

                            <div class="col-xl-4 col-sm-6">
                                <div class="form-group">
                                    <label>@lang('Telegram Channel / Chat ID')</label>
                                    <input class="form-control" type="text" name="telegram_chat_id" placeholder="@your_channel_name or -100123456789" value="{{ gs('telegram_chat_id') }}">
                                    <small class="text-muted">@lang('Add your bot as Admin in your channel with post message permission.')</small>
                                </div>
                            </div>

                            <div class="col-xl-3 col-sm-12">
                                <div class="form-group">
                                    <label>@lang('Live Notifications')</label>
                                    <div class="form-check form-switch form--switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="telegram_notification" value="1" id="telegram_notification" @checked(gs('telegram_notification'))>
                                        <label class="form-check-label" for="telegram_notification">@lang('Enable Channel Alerts')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                        </div>

                    </form>

                    @if(gs('telegram_bot_token') && gs('telegram_chat_id'))
                        <form id="testTelegramForm" action="{{ route('admin.setting.general.telegram.test') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script-lib')
<script src="{{ asset('assets/admin/js/spectrum.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/spectrum.css') }}">
@endpush

@push('script')
    <script>
        (function ($) {
            "use strict";


            $('.colorPicker').spectrum({
                color: $(this).data('color'),
                change: function (color) {
                    $(this).parent().siblings('.colorCode').val(color.toHexString().replace(/^#?/, ''));
                }
            });

            $('.colorCode').on('input', function () {
                var clr = $(this).val();
                $(this).parents('.input-group').find('.colorPicker').spectrum({
                    color: clr,
                });
            });
        })(jQuery);

    </script>
@endpush
