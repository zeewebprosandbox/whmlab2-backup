<?php

namespace App\Http\Controllers\WhmPanel;

use App\Http\Controllers\Controller;
use App\Models\WhmPanelAccount;
use App\Models\WhmPanelNode;
use App\Models\WhmPanelSsoToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WebController extends Controller
{
    public function dashboard()
    {
        $nodes = WhmPanelNode::withCount('accounts')->latest()->get();
        $accounts = WhmPanelAccount::with('node', 'websites')->latest()->limit(10)->get();
        $primaryNode = $nodes->first();
        $pageTitle = 'WHMPanel';

        return view('whmpanel.dashboard', compact('pageTitle', 'nodes', 'accounts', 'primaryNode'));
    }

    public function sso(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        $tokens = WhmPanelSsoToken::whereNull('used_at')
            ->where('expires_at', '>', now())
            ->with('account.node')
            ->latest()
            ->get();

        foreach ($tokens as $token) {
            if (Hash::check($request->token, $token->token_hash)) {
                $token->used_at = now();
                $token->save();

                session(['whmpanel_account_id' => $token->account_id]);
                return to_route('whmpanel.dashboard');
            }
        }

        abort(403, 'Invalid or expired WHMPanel SSO token');
    }
}
