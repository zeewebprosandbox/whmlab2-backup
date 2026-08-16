"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { cn } from "@/lib/utils";
import {
  LayoutDashboard,
  Server,
  Globe,
  Receipt,
  LifeBuoy,
  Settings,
  ShieldCheck,
  Zap,
  ChevronRight,
  LogOut
} from "lucide-react";

const navItems = [
  { name: "Dashboard", href: "/dashboard", icon: LayoutDashboard },
  { name: "Services", href: "/services", icon: Server, badge: "Active" },
  { name: "Domains", href: "/domains", icon: Globe },
  { name: "Billing & Invoices", href: "/billing", icon: Receipt },
  { name: "Support Center", href: "/support", icon: LifeBuoy },
  { name: "Account Settings", href: "/settings", icon: Settings },
];

export function Sidebar() {
  const pathname = usePathname();

  return (
    <aside className="w-64 border-r border-zinc-800 bg-[#09090b] flex flex-col justify-between h-screen sticky top-0 z-30">
      <div>
        {/* Brand Header */}
        <div className="h-16 px-6 border-b border-zinc-800 flex items-center gap-3">
          <div className="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-500 to-cyan-400 p-[1px] shadow-[0_0_15px_rgba(99,102,241,0.3)]">
            <div className="w-full h-full bg-[#09090b] rounded-[7px] flex items-center justify-center text-indigo-400">
              <Server className="w-4 h-4" />
            </div>
          </div>
          <div>
            <span className="block text-sm font-bold text-white tracking-tight leading-none">WHM Platform</span>
            <span className="block text-[10px] font-semibold text-zinc-500 uppercase tracking-wider mt-1">Cloud Workspace</span>
          </div>
        </div>

        {/* Navigation Links */}
        <nav className="p-4 space-y-1.5">
          <div className="px-3 py-2 text-[10px] font-extrabold uppercase tracking-widest text-zinc-600">Main Menu</div>
          {navItems.map((item) => {
            const Icon = item.icon;
            const isActive = pathname === item.href || pathname.startsWith(item.href + "/");

            return (
              <Link
                key={item.name}
                href={item.href}
                className={cn(
                  "flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-semibold transition-all group",
                  isActive
                    ? "bg-indigo-600/10 border border-indigo-500/20 text-white shadow-sm"
                    : "text-zinc-400 hover:text-zinc-100 hover:bg-zinc-800/50"
                )}
              >
                <div className="flex items-center gap-3">
                  <Icon className={cn("w-4 h-4 transition-colors", isActive ? "text-indigo-400" : "text-zinc-500 group-hover:text-zinc-300")} />
                  <span>{item.name}</span>
                </div>
                {item.badge && (
                  <span className="px-1.5 py-0.5 rounded bg-emerald-500/10 text-emerald-400 text-[10px] font-semibold">
                    {item.badge}
                  </span>
                )}
              </Link>
            );
          })}
        </nav>
      </div>

      {/* Footer User Card */}
      <div className="p-4 border-t border-zinc-800 space-y-3">
        <div className="p-3 bg-zinc-900/60 border border-zinc-800/80 rounded-xl flex items-center justify-between">
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-cyan-400 p-[1px]">
              <div className="w-full h-full bg-zinc-900 rounded-full flex items-center justify-center text-xs font-bold text-white">
                JD
              </div>
            </div>
            <div>
              <div className="text-xs font-semibold text-white leading-tight">John Doe</div>
              <div className="text-[10px] text-zinc-500 truncate max-w-[110px]">john@example.com</div>
            </div>
          </div>
          <Link href="/login" className="text-zinc-500 hover:text-rose-400 transition-colors p-1" title="Sign out">
            <LogOut className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </aside>
  );
}
