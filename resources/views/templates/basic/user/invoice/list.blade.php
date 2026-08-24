@extends($activeTemplate.'layouts.master')

@section('content')
<div class="py-8 bg-[#F8FAFC] text-slate-900 min-h-screen font-sans space-y-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Header & Balance Banner -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-2">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">@lang('Billing & Invoices')</h1>
                <p class="text-xs text-slate-500">@lang('View active subscriptions, itemized billing history, and pay outstanding invoices.')</p>
            </div>

            <!-- Credit Balance Card -->
            <div class="p-5 bg-white border border-slate-200/80 rounded-2xl flex items-center justify-between shadow-sm">
                <div>
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500">@lang('Credit Balance')</div>
                    <div class="text-2xl font-extrabold font-mono text-slate-900 mt-1">{{ showAmount(auth()->user()->balance) }}</div>
                </div>
                <a href="{{ route('user.deposit.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg shadow-sm hover:shadow transition-all flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>@lang('Add Funds')</span>
                </a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200">
            <button class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg shadow-xs">@lang('All Invoices')</button>
            <button class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-semibold rounded-lg transition-colors">@lang('Unpaid')</button>
            <button class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-semibold rounded-lg transition-colors">@lang('Paid')</button>
            <button class="px-4 py-2 bg-white border border-slate-200 text-slate-600 hover:text-slate-900 text-xs font-semibold rounded-lg transition-colors">@lang('Overdue')</button>
        </div>

        <!-- Invoice Table -->
        <div class="p-6 bg-white border border-slate-200/80 rounded-2xl space-y-4 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 rounded-l-lg">@lang('Invoice Number')</th>
                            <th class="px-4 py-3">@lang('Invoice Date')</th>
                            <th class="px-4 py-3">@lang('Amount')</th>
                            <th class="px-4 py-3">@lang('Status')</th>
                            <th class="px-4 py-3 rounded-r-lg text-right">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-sans">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-slate-50/80 transition-colors @if($invoice->status == 0) border-l-4 border-l-amber-500 @elseif($invoice->status == 2) border-l-4 border-l-rose-500 @endif">
                                <td class="px-4 py-4 font-mono font-bold text-slate-900">
                                    <a href="{{ route('user.invoice.view', $invoice->id) }}" class="hover:text-indigo-600">
                                        #{{ $invoice->getInvoiceNumber }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-500">
                                    {{ showDateTime($invoice->created, 'd/m/Y') }}
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-900 font-bold text-sm">
                                    {{ showAmount($invoice->amount) }}
                                </td>
                                <td class="px-4 py-4">
                                    @php echo $invoice->showStatus; @endphp
                                </td>
                                <td class="px-4 py-4 text-right space-x-2">
                                    @if($invoice->status == 0)
                                        <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs rounded-md shadow-xs transition-colors">
                                            @lang('Pay Now')
                                        </a>
                                    @else
                                        <a href="{{ route('user.invoice.view', $invoice->id) }}" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-md transition-colors">
                                            @lang('View Receipt')
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-slate-400">
                                    <i data-lucide="receipt" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    <p>@lang('No invoices found.')</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="pt-4 border-t border-slate-100">
                    {{ paginateLinks($invoices) }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
