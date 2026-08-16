@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--lg  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Payment Type')</th>
                                    <th>@lang('Stock')</th>
                                    <th>@lang('Domain Registration')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupByCategories as $groupCategory)
                                    <tr>
                                        <td class="table--bg ct-td" colspan="100%">
                                            <div class="d-flex flex-wrap">
                                                <p class="fw-bold">{{ __($groupCategory->name) }}</p>
                                            </div>
                                        </td>
                                    </tr>

                                    @php
                                        $products = $groupCategory->products;
                                    @endphp

                                    @forelse($products as $product)
                                        <tr>
                                            <td>{{ __($product->name) }}</td>

                                            <td>
                                                <div class="d-flex flex-wrap justify-content-xl-center justify-content-end align-items-center">
                                                    <span class="me-1">{{ productType()[$product->product_type] }}</span>

                                                    @if ($product->server_group_id)
                                                        (@php echo getProductModuleLogo(@$product->serverGroup->type); @endphp)
                                                    @endif

                                                    <span class="text--primary ms-1" title="{{ @productModuleOptions()[$product->module_option] }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </span>
                                                </div>
                                            </td>

                                            <td>
                                                @php echo $product->showPaymentType; @endphp
                                            </td>

                                            <td>
                                                @php echo $product->showStock; @endphp
                                            </td>

                                            <td>
                                                @php echo $product->showDomainRegister; @endphp
                                            </td>

                                            <td>
                                                @php echo $product->showStatus; @endphp
                                            </td>

                                            <td>
                                                <div class="button--group">
                                                    @permit('admin.product.update.page')
                                                        <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.product.update.page', $product->id) }}">
                                                            <i class="la la-pencil"></i> @lang('Edit')
                                                        </a>
                                                    @else
                                                        <button class="btn btn-sm btn-outline--primary" disabled>
                                                            <i class="la la-pencil"></i> @lang('Edit')
                                                        </button> 
                                                    @endpermit
                                                    @permit('admin.product.status')
                                                        @if ($product->status == 0)
                                                            <button type="button" class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Are you sure to enable this product?')">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.product.status', $product->id) }}" data-question="@lang('Are you sure to disable this product?')">
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </button>
                                                        @endif
                                                    @else 
                                                        @if ($product->status == 0)
                                                            <button type="button" class="btn btn-sm btn-outline--success" disabled>
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </button>
                                                        @else
                                                            <button type="button" class="btn btn-sm btn-outline--danger" disabled>
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </button>
                                                        @endif
                                                    @endpermit
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">@lang('No product available in this category')</td>
                                        </tr>
                                    @endforelse
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

            </div>
        </div>
    </div>

    <x-confirmation-modal />
    <div class="modal fade" id="syncPackagesModal" tabindex="-1" role="dialog" aria-labelledby="syncPackagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post" action="{{ route('admin.product.sync.packages') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="syncPackagesModalLabel">@lang('Create Server Packages')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">
                            @lang('This will create matching server packages from active shared hosting products and attach the exact package names back to the products.')
                        </p>
                        <div class="form-group">
                            <label>@lang('Server Group')</label>
                            <select name="server_group_id" class="form-control" required>
                                <option value="" selected disabled>@lang('Select One')</option>
                                @foreach($serverGroups as $serverGroup)
                                    <option value="{{ $serverGroup->id }}">{{ __($serverGroup->name) }} ({{ __($serverGroup->getType) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <small class="text-muted d-block">
                            @lang('For cPanel, WHMLab creates WHM-safe package names automatically from the product slug or name.')
                        </small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary">@lang('Create Packages')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@permit('admin.product.add.page')
    @push('breadcrumb-plugins')
        @if($serverGroups->count())
            <button type="button" class="btn btn-sm btn-outline--info" data-bs-toggle="modal" data-bs-target="#syncPackagesModal">
                <i class="las la-sync"></i>@lang('Sync Packages')
            </button>
        @endif
        <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.product.add.page') }}">
            <i class="las la-plus"></i>@lang('Add New')
        </a>
    @endpush
@endpermit

@push('style')
    <style>
        @media (max-width: 991px) {
            .table-responsive--md tr td.ct-td {
                padding-left: 25px !important;
                text-align: left !important;
            }
        }

        @media (max-width: 1199px) {

            .table-responsive--lg tr th,
            .table-responsive--lg tr td {
                padding-left: 15px !important;
            }
        }

        .cpanel {
            color: #FF6C2C;
        }
    </style>
@endpush
