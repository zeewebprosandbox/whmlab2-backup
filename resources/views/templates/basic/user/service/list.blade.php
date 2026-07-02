@extends($activeTemplate.'layouts.master_side_bar')

@section('content')
<div class="col-lg-9">
    <div class="whm-service-page-head">
        <div>
            <span>@lang('Service groups')</span>
            <h3>@lang('My Services')</h3>
            <p>@lang('See hosting, VPS, mail, RDP, and other services grouped by the panel or server type that powers them.')</p>
        </div>
        <a href="{{ route('service.category') }}?all" class="btn btn--base btn--sm">
            <i data-lucide="plus-circle"></i> @lang('Order New Service')
        </a>
    </div>

    <div class="whm-service-filter-grid mb-4">
        <a href="{{ route('user.service.list') }}" class="whm-service-filter {{ !$selectedServiceGroup ? 'active' : '' }}">
            <i data-lucide="layers-3"></i>
            <span>@lang('All services')</span>
            <strong>{{ $services->total() }}</strong>
        </a>
        @foreach($serviceGroups as $group)
            <a href="{{ route('user.service.list', ['service' => $group['key']]) }}" class="whm-service-filter {{ $selectedServiceGroup === $group['key'] ? 'active' : '' }}">
                <i data-lucide="{{ $group['key'] === 'vps' ? 'server-cog' : ($group['key'] === 'mail' ? 'mail' : 'box') }}"></i>
                <span>{{ __($group['label']) }}</span>
                <strong>{{ $group['count'] }}</strong>
            </a>
        @endforeach
    </div>

    <div class="whm-service-list">
        @forelse($services as $service)
            @php
                $server = $service->server;
                $serverGroup = @$server->group;
                $fallbackRole = \App\Models\Server::roleForProduct($service->product);
                $role = $server ? $server->serviceRoleLabel() : (\App\Models\Server::serviceRoles()[$fallbackRole] ?? ucfirst($fallbackRole));
            @endphp
            <div class="whm-service-card">
                <div class="whm-service-card__main">
                    <div class="whm-service-icon">
                        <i data-lucide="{{ str_contains(strtolower($role), 'vps') ? 'server-cog' : (str_contains(strtolower($role), 'mail') ? 'mail' : 'hard-drive') }}"></i>
                    </div>
                    <div>
                        <h5>{{ __(@$service->product->name ?: @$service->product->serviceCategory->name) }}</h5>
                        <p>{{ __(@$service->domain ?: @$service->product->serviceCategory->name) }}</p>
                        <div class="whm-service-meta">
                            <span>{{ __($role) }}</span>
                            @if($serverGroup)
                                <span>{{ __($serverGroup->getType) }} / {{ __($serverGroup->name) }}</span>
                            @endif
                            @if($server)
                                <span>{{ __($server->name) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="whm-service-card__billing">
                    <span>@lang('Recurring')</span>
                    <strong>{{ showAmount($service->recurring_amount) }}</strong>
                    <small>{{ @billingCycle($service->billing_cycle, true)['showText'] }}</small>
                </div>

                <div class="whm-service-card__status">
                    @php echo $service->showStatus; @endphp
                    <small>
                        @if($service->billing_cycle != 0)
                            @lang('Renews') {{ showDateTime($service->next_due_date, 'd/m/Y') }}
                        @else
                            @lang('One-time service')
                        @endif
                    </small>
                </div>

                <a href="{{ route('user.service.details', $service->id) }}" class="btn btn--base btn--sm">
                    <i data-lucide="monitor-cog"></i> @lang('Manage')
                </a>
            </div>
        @empty
            <div class="card custom--card">
                <div class="card-body text-center p-5">
                    <i data-lucide="box" class="mb-3 text--secondary"></i>
                    <h5>@lang('No services found')</h5>
                    <p class="text-muted">@lang('Order a hosting, VPS, mail, or server plan to see it grouped here.')</p>
                    <a href="{{ route('service.category') }}?all" class="btn btn--base btn--sm">@lang('Browse Services')</a>
                </div>
            </div>
        @endforelse
    </div>

    @if($services->hasPages())
        <div class="mt-4">
            {{ paginateLinks($services) }}
        </div>
    @endif
</div>
@endsection

@push('style')
<style>
    .whm-service-page-head {
        align-items: center;
        display: flex;
        gap: 16px;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .whm-service-page-head span,
    .whm-service-filter span,
    .whm-service-card__billing span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .03em;
        text-transform: uppercase;
    }

    .whm-service-page-head h3 {
        color: #0f172a;
        font-weight: 800;
        margin: 3px 0;
    }

    .whm-service-page-head p {
        color: #64748b;
        margin: 0;
    }

    .whm-service-filter-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(auto-fit, minmax(165px, 1fr));
    }

    .whm-service-filter,
    .whm-service-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        color: #0f172a;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease, background-color .2s ease;
    }

    .whm-service-filter {
        padding: 14px;
    }

    .whm-service-filter:hover,
    .whm-service-filter.active,
    .whm-service-card:hover {
        background: #f8fbff;
        border-color: #2563eb;
        box-shadow: 0 12px 26px rgba(15, 23, 42, .07);
        color: #0f172a;
        transform: translateY(-1px);
    }

    .whm-service-filter i {
        color: #2563eb;
        height: 20px;
        margin-bottom: 10px;
        width: 20px;
    }

    .whm-service-filter strong {
        display: block;
        font-size: 22px;
        font-weight: 800;
        margin-top: 3px;
    }

    .whm-service-list {
        display: grid;
        gap: 12px;
    }

    .whm-service-card {
        align-items: center;
        display: grid;
        gap: 16px;
        grid-template-columns: minmax(0, 1.7fr) minmax(130px, .5fr) minmax(130px, .55fr) auto;
        padding: 16px;
    }

    .whm-service-card__main {
        align-items: center;
        display: flex;
        gap: 14px;
        min-width: 0;
    }

    .whm-service-icon {
        align-items: center;
        background: #eff6ff;
        border-radius: 14px;
        color: #2563eb;
        display: flex;
        flex: 0 0 auto;
        height: 46px;
        justify-content: center;
        width: 46px;
    }

    .whm-service-card h5 {
        color: #0f172a;
        font-weight: 800;
        margin-bottom: 2px;
    }

    .whm-service-card p,
    .whm-service-card small {
        color: #64748b;
        margin: 0;
    }

    .whm-service-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 8px;
    }

    .whm-service-meta span {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 7px;
    }

    .whm-service-card__billing strong {
        color: #0f172a;
        display: block;
        font-size: 18px;
        font-weight: 800;
    }

    .whm-service-card__status {
        display: grid;
        gap: 6px;
    }

    @media (max-width: 991px) {
        .whm-service-page-head,
        .whm-service-card {
            display: block;
        }

        .whm-service-page-head .btn,
        .whm-service-card .btn {
            margin-top: 14px;
            width: 100%;
        }

        .whm-service-card__billing,
        .whm-service-card__status {
            margin-top: 14px;
        }
    }
</style>
@endpush
