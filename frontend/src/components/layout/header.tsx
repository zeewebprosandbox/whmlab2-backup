"use client";

import Link from "next/link";
import { Search, Bell, Shield, Sparkles, Plus, LifeBuoy } from "lucide-react";
import { Button } from "@/components/ui/button";

export function Header() {
  return (
    <header className="h-16 border-b border-zinc-800 bg-[#09090b]/80 backdrop-blur-md px-6 flex items-center justify-between sticky top-0 z-20">
      {/* Search Input */}
      <div className="relative w-80">
        <Search className="w-4 h-4 text-zinc-500 absolute left-3 top-1/2 -translate-y-1/2" />
        <input
          type="text"
          placeholder="Search domains, servers, invoices... (⌘K)"
          className="w-full pl-9 pr-4 py-1.5 bg-zinc-900/60 border border-zinc-800 rounded-lg text-xs text-zinc-200 placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all font-sans"
        />
      </div>

      {/* Quick Action Right Bar */}
      <div className="flex items-center gap-3">
        {/* Support PIN Chip */}
        <div className="hidden sm:flex items-center gap-2 px-3 py-1 bg-zinc-900 border border-zinc-800 rounded-lg text-xs">
          <Shield className="w-3.5 h-3.5 text-cyan-400" />
          <span className="text-zinc-400">Support PIN:</span>
          <span className="font-mono font-bold text-white tracking-wider">849-201</span>
        </div>

        {/* Notification Bell */}
        <button className="p-2 text-zinc-400 hover:text-white bg-zinc-900/60 border border-zinc-800 rounded-lg transition-colors relative">
          <Bell className="w-4 h-4" />
          <span className="w-2 h-2 rounded-full bg-cyan-400 absolute top-1.5 right-1.5 orb-pulse"></span>
        </button>

        {/* Deploy Service Button */}
        <Link href="/services">
          <Button size="sm" className="gap-1.5">
            <Plus className="w-3.5 h-3.5" />
            <span>Deploy Service</span>
          </Button>
        </Link>
      </div>
    </header>
  );
}
