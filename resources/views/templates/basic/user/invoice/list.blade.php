@extends($activeTemplate.'layouts.master')

@section('content')
<div class="col-12 space-y-5">
    <!-- Header & Balance Banner -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 p-5 sm:p-6 bg-white border border-slate-200/80 rounded-2xl flex flex-col justify-between shadow-xs">
            <div class="space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">@lang('Billing & Payments')</span>
                <h1 class="text-xl font-bold tracking-tight text-slate-900 font-display">@lang('Invoices')</h1>
                <p class="text-xs text-slate-500 max-w-xl">@lang('View active subscriptions, itemized billing history, and pay outstanding invoices.')</p>
            </div>
        </div>

        <!-- Credit Balance Card -->
        <div class="p-5 bg-white border border-slate-200/80 rounded-2xl flex items-center justify-between shadow-xs">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">@lang('Credit Balance')</div>
                <div class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ showAmount(auth()->user()->balance) }}</div>
            </div>
            <a href="{{ route('user.deposit.index') }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-all flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                <span>@lang('Add Funds')</span>
            </a>
        </div>
    </div>

    <!-- Invoice Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50/80 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="px-4 py-3.5">@lang('Invoice Number')</th>
                        <th class="px-4 py-3.5">@lang('Invoice Date')</th>
                        <th class="px-4 py-3.5">@lang('Amount')</th>
                        <th class="px-4 py-3.5">@lang('Status')</th>
                        <th class="px-4 py-3.5 text-right">@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-4 py-3.5 font-mono font-bold text-slate-900">
                                <a href="{{ route('user.invoice.view', $invoice->id) }}" class="hover:text-indigo-600 transition-colors">
                                    #{{ $invoice->getInvoiceNumber }}
                                </a>
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-500">
                                {{ showDateTime($invoice->created, 'd/m/Y') }}
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-900 font-bold text-xs sm:text-sm">
                                {{ showAmount($invoice->amount) }}
                            </td>
                            <td class="px-4 py-3.5">
                                @php echo $invoice->showStatus; @endphp
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                @if($invoice->status == 0)
                                    <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-lg shadow-xs transition-colors">
                                        @lang('Pay Now')
                                    </a>
                                @else
                                    <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-slate-700 font-semibold text-xs rounded-lg transition-colors">
                                        @lang('View Invoice')
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                <p class="text-xs text-slate-500">@lang('No invoices found.')</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ paginateLinks($invoices) }}
            </div>
        @endif
    </div>
</div>
@endsection
