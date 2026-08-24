@extends($activeTemplate . 'layouts.master')

@section('content')
<div class="py-8 bg-[#0A0A0B] text-[#F5F5F7] min-h-screen font-sans space-y-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Page Title -->
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">@lang('Account & Settings')</h1>
            <p class="text-xs text-neutral-400">@lang('Manage personal details, API keys, 2FA, and notification channels.')</p>
        </div>

        <!-- Profile & Avatar Form -->
        <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-6">
            <div class="flex items-center gap-4 pb-4 border-b border-white/5">
                <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 p-[2px]">
                    <div class="w-full h-full bg-[#141416] rounded-full flex items-center justify-center text-xl font-bold text-white uppercase">
                        {{ substr($user->firstname, 0, 1) }}{{ substr($user->lastname, 0, 1) }}
                    </div>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">{{ $user->fullname }}</h3>
                    <p class="text-xs text-neutral-400">{{ $user->email }}</p>
                    <button onclick="alert('Avatar crop dialog opened')" class="text-xs text-indigo-400 hover:underline mt-1 font-medium">@lang('Change Avatar')</button>
                </div>
            </div>

            <form method="POST" action="" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-neutral-300">@lang('First Name')</label>
                        <input type="text" name="firstname" value="{{ $user->firstname }}" required
                            class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-neutral-300">@lang('Last Name')</label>
                        <input type="text" name="lastname" value="{{ $user->lastname }}" required
                            class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-neutral-300">@lang('E-mail Address')</label>
                        <input class="w-full px-4 py-2.5 bg-[#1C1C1F]/50 border border-white/5 rounded-lg text-sm text-neutral-400 font-mono" value="{{ $user->email }}" readonly>
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-neutral-300">@lang('Mobile Number')</label>
                        <input class="w-full px-4 py-2.5 bg-[#1C1C1F]/50 border border-white/5 rounded-lg text-sm text-neutral-400 font-mono" value="{{ $user->mobile }}" readonly>
                    </div>
                    <div class="sm:col-span-2 space-y-1">
                        <label class="block text-xs font-medium text-neutral-300">@lang('Address')</label>
                        <input type="text" name="address" value="{{ @$user->address }}"
                            class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-neutral-300">@lang('City')</label>
                        <input type="text" name="city" value="{{ @$user->city }}"
                            class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-neutral-300">@lang('Zip Code')</label>
                        <input type="text" name="zip" value="{{ @$user->zip }}"
                            class="w-full px-4 py-2.5 bg-[#1C1C1F] border border-white/10 rounded-lg text-sm text-white focus:border-indigo-500 focus:outline-none">
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-glow-accent transition-all">
                        @lang('Save Changes')
                    </button>
                </div>
            </form>
        </div>

        <!-- API Keys Card -->
        <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white">@lang('API Keys & Tokens')</h3>
                    <p class="text-xs text-neutral-400">@lang('Programmatic access to server management APIs.')</p>
                </div>
                <button onclick="alert('Generated new API Key: whm_live_9481028491028')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg">
                    + @lang('Generate Key')
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-neutral-300">
                    <thead class="bg-[#1C1C1F] text-neutral-400 uppercase text-[11px] font-semibold">
                        <tr>
                            <th class="px-4 py-2.5 rounded-l-lg">Token Name</th>
                            <th class="px-4 py-2.5">Key Prefix</th>
                            <th class="px-4 py-2.5">Permissions</th>
                            <th class="px-4 py-2.5">Last Used</th>
                            <th class="px-4 py-2.5 rounded-r-lg text-right">Revoke</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 font-mono">
                        <tr>
                            <td class="px-4 py-3 text-white font-medium">CLI Deploy Bot</td>
                            <td class="px-4 py-3 text-indigo-400">whm_live_94...</td>
                            <td class="px-4 py-3"><span class="px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-400">Full Access</span></td>
                            <td class="px-4 py-3 text-neutral-400">2 hours ago</td>
                            <td class="px-4 py-3 text-right"><button class="text-rose-400 hover:underline">Revoke</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notification Preferences Matrix -->
        <div class="p-6 bg-[#141416] border border-white/10 rounded-2xl space-y-4">
            <h3 class="text-base font-bold text-white">@lang('Notification Preferences')</h3>
            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 bg-[#1C1C1F] rounded-lg border border-white/5">
                    <div>
                        <strong class="text-white">@lang('Invoice Due & Reminders')</strong>
                        <p class="text-neutral-400 text-[11px]">@lang('Receive invoice notifications 7 days prior to expiry.')</p>
                    </div>
                    <input type="checkbox" checked class="w-4 h-4 rounded bg-[#0A0A0B] border-white/20 text-indigo-600 focus:ring-0">
                </div>
                <div class="flex items-center justify-between p-3 bg-[#1C1C1F] rounded-lg border border-white/5">
                    <div>
                        <strong class="text-white">@lang('Service Suspension Warnings')</strong>
                        <p class="text-neutral-400 text-[11px]">@lang('Critical alerts when server quota exceeds limit.')</p>
                    </div>
                    <input type="checkbox" checked class="w-4 h-4 rounded bg-[#0A0A0B] border-white/20 text-indigo-600 focus:ring-0">
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
