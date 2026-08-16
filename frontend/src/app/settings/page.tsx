"use client";

import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Settings, Key, Shield, Bell, User } from "lucide-react";

export default function SettingsPage() {
  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">
          {/* Page Header */}
          <div>
            <h1 className="text-2xl font-extrabold text-white tracking-tight">Account Settings</h1>
            <p className="text-xs text-zinc-400">Manage profile details, API tokens, 2FA security, and notification preferences.</p>
          </div>

          {/* Profile Form Card */}
          <Card className="p-6 space-y-6">
            <CardTitle className="text-sm font-semibold flex items-center gap-2">
              <User className="w-4 h-4 text-indigo-400" />
              Profile Details
            </CardTitle>

            <form className="space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="space-y-1">
                  <label className="text-xs text-zinc-400">First Name</label>
                  <input type="text" defaultValue="John" className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white" />
                </div>
                <div className="space-y-1">
                  <label className="text-xs text-zinc-400">Last Name</label>
                  <input type="text" defaultValue="Doe" className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white" />
                </div>
                <div className="space-y-1">
                  <label className="text-xs text-zinc-400">Email Address</label>
                  <input type="email" defaultValue="john@example.com" disabled className="w-full px-3 py-2 bg-zinc-900/50 border border-zinc-800 rounded-lg text-xs text-zinc-500 font-mono" />
                </div>
                <div className="space-y-1">
                  <label className="text-xs text-zinc-400">Phone</label>
                  <input type="tel" defaultValue="+1 (555) 000-0000" className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white" />
                </div>
              </div>

              <div className="pt-2 flex justify-end">
                <Button size="sm">Save Changes</Button>
              </div>
            </form>
          </Card>

          {/* API Tokens Card */}
          <Card className="p-6 space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <CardTitle className="text-sm font-semibold">API Tokens</CardTitle>
                <CardDescription>Programmatic API access keys for server provisioning</CardDescription>
              </div>
              <Button size="sm">+ Generate Key</Button>
            </div>

            <div className="p-3 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-between font-mono text-xs">
              <div>
                <div className="text-white font-bold">CLI Deploy Token</div>
                <div className="text-zinc-500">whm_live_948102...</div>
              </div>
              <Badge variant="cyan">Full Scope</Badge>
            </div>
          </Card>
        </main>
      </div>
    </div>
  );
}
