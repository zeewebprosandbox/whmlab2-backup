"use client";

import { useState, useEffect } from "react";
import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { useAuth } from "@/lib/auth-context";
import { apiClient, ServerStats } from "@/lib/api";
import {
  Activity,
  Server,
  Globe,
  Receipt,
  LifeBuoy,
  Plus,
  Cpu,
  RefreshCw,
  ChevronRight,
  Command
} from "lucide-react";

export default function DashboardPage() {
  const { user } = useAuth();
  const [stats, setStats] = useState<ServerStats>({
    nodeName: "US-East-Node-01",
    status: "online",
    cpuPercent: 32,
    memoryPercent: 68,
    diskPercent: 41,
    bandwidthPercent: 18,
    activeAccounts: 3,
  });

  useEffect(() => {
    apiClient.getServerStats().then((res) => {
      if (res) setStats(res);
    });
  }, []);

  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
          {/* Hero Header */}
          <div className="p-6 lg:p-8 rounded-2xl bg-gradient-to-r from-zinc-900 via-zinc-900 to-[#18181b] border border-zinc-800 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
            <div className="space-y-2 relative z-10">
              <Badge variant="cyan" className="gap-1.5">
                <span className="w-1.5 h-1.5 rounded-full bg-cyan-400 orb-pulse" />
                Console Live • {stats.nodeName}
              </Badge>
              <h1 className="text-2xl lg:text-3xl font-extrabold text-white tracking-tight">
                Welcome back, <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-cyan-400">{user?.name || "John"}</span> 👋
              </h1>
              <p className="text-xs text-zinc-400 max-w-xl">
                All server cluster nodes operational. You have <strong className="text-white">{stats.activeAccounts} active services</strong> and <strong className="text-white">2 registered domains</strong>.
              </p>
            </div>

            <div className="flex flex-wrap items-center gap-2 relative z-10">
              <Link href="/billing">
                <Button variant="outline" size="sm" className="gap-1.5">
                  <Receipt className="w-3.5 h-3.5 text-cyan-400" />
                  <span>View Invoices</span>
                </Button>
              </Link>
              <Link href="/support">
                <Button variant="outline" size="sm" className="gap-1.5">
                  <LifeBuoy className="w-3.5 h-3.5 text-indigo-400" />
                  <span>Open Ticket</span>
                </Button>
              </Link>
              <Link href="/services">
                <Button size="sm" className="gap-1.5">
                  <Plus className="w-3.5 h-3.5" />
                  <span>Deploy Instance</span>
                </Button>
              </Link>
            </div>
          </div>

          {/* Server Health Cluster Card */}
          <Card className="border-zinc-800">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <div>
                <CardTitle className="text-sm font-semibold flex items-center gap-2">
                  <Activity className="w-4 h-4 text-indigo-400" />
                  Server Health Overview
                </CardTitle>
                <CardDescription>Real-time cluster telemetry from {stats.nodeName}</CardDescription>
              </div>
              <span className="text-[11px] font-mono text-zinc-500">Latency: 8ms</span>
            </CardHeader>

            <CardContent>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                {/* CPU */}
                <div className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 space-y-2">
                  <div className="flex items-center justify-between text-xs">
                    <span className="text-zinc-400">CPU Load</span>
                    <span className="font-mono font-bold text-cyan-400">{stats.cpuPercent}%</span>
                  </div>
                  <div className="w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                    <div className="h-full bg-cyan-400 rounded-full" style={{ width: `${stats.cpuPercent}%` }} />
                  </div>
                  <div className="text-[11px] text-zinc-500 font-mono">8 Cores • 3.4 GHz AMD EPYC</div>
                </div>

                {/* RAM */}
                <div className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 space-y-2">
                  <div className="flex items-center justify-between text-xs">
                    <span className="text-zinc-400">RAM Usage</span>
                    <span className="font-mono font-bold text-amber-400">{stats.memoryPercent}%</span>
                  </div>
                  <div className="w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                    <div className="h-full bg-amber-400 rounded-full" style={{ width: `${stats.memoryPercent}%` }} />
                  </div>
                  <div className="text-[11px] text-zinc-500 font-mono">21.7 GB / 32 GB DDR5</div>
                </div>

                {/* NVMe */}
                <div className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 space-y-2">
                  <div className="flex items-center justify-between text-xs">
                    <span className="text-zinc-400">NVMe Disk</span>
                    <span className="font-mono font-bold text-cyan-400">{stats.diskPercent}%</span>
                  </div>
                  <div className="w-full h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                    <div className="h-full bg-cyan-400 rounded-full" style={{ width: `${stats.diskPercent}%` }} />
                  </div>
                  <div className="text-[11px] text-zinc-500 font-mono">205 GB / 500 GB Storage</div>
                </div>
              </div>
            </CardContent>
          </Card>

          {/* 4 Stats Cards Grid */}
          <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <Link href="/services">
              <Card className="p-5 hover:border-zinc-700 group transition-all">
                <div className="flex items-center justify-between mb-3">
                  <div className="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400 group-hover:scale-110 transition-transform">
                    <Server className="w-4 h-4" />
                  </div>
                  <span className="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Services</span>
                </div>
                <div className="text-2xl font-bold font-mono text-white">{stats.activeAccounts}</div>
                <div className="text-xs text-zinc-400 mt-1 flex items-center gap-1">
                  <span>Active instances</span>
                  <ChevronRight className="w-3 h-3 text-zinc-600" />
                </div>
              </Card>
            </Link>

            <Link href="/domains">
              <Card className="p-5 hover:border-zinc-700 group transition-all">
                <div className="flex items-center justify-between mb-3">
                  <div className="w-9 h-9 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400 group-hover:scale-110 transition-transform">
                    <Globe className="w-4 h-4" />
                  </div>
                  <span className="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Domains</span>
                </div>
                <div className="text-2xl font-bold font-mono text-white">2</div>
                <div className="text-xs text-zinc-400 mt-1 flex items-center gap-1">
                  <span>DNS & Registrations</span>
                  <ChevronRight className="w-3 h-3 text-zinc-600" />
                </div>
              </Card>
            </Link>

            <Link href="/billing">
              <Card className="p-5 hover:border-zinc-700 group transition-all">
                <div className="flex items-center justify-between mb-3">
                  <div className="w-9 h-9 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-400 group-hover:scale-110 transition-transform">
                    <Receipt className="w-4 h-4" />
                  </div>
                  <span className="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Invoices</span>
                </div>
                <div className="text-2xl font-bold font-mono text-white">1</div>
                <div className="text-xs text-amber-400 mt-1 flex items-center gap-1">
                  <span>1 Unpaid Invoice ($24.00)</span>
                  <ChevronRight className="w-3 h-3 text-zinc-600" />
                </div>
              </Card>
            </Link>

            <Link href="/support">
              <Card className="p-5 hover:border-zinc-700 group transition-all">
                <div className="flex items-center justify-between mb-3">
                  <div className="w-9 h-9 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-transform">
                    <LifeBuoy className="w-4 h-4" />
                  </div>
                  <span className="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Tickets</span>
                </div>
                <div className="text-2xl font-bold font-mono text-white">0</div>
                <div className="text-xs text-zinc-400 mt-1 flex items-center gap-1">
                  <span>All resolved</span>
                  <ChevronRight className="w-3 h-3 text-zinc-600" />
                </div>
              </Card>
            </Link>
          </div>

          {/* Main Content Split Grid */}
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* Left 2 Cols: Services Quick Grid */}
            <div className="lg:col-span-2 space-y-4">
              <Card>
                <CardHeader className="flex flex-row items-center justify-between">
                  <div>
                    <CardTitle className="text-sm font-semibold">Active Hosting Instances</CardTitle>
                    <CardDescription>Your running NVMe Web Hosting & cPanel accounts</CardDescription>
                  </div>
                  <Link href="/services">
                    <Button variant="ghost" size="sm" className="text-xs text-indigo-400">View All →</Button>
                  </Link>
                </CardHeader>
                <CardContent className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {/* Service Card 1 */}
                  <div className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 space-y-3">
                    <div className="flex items-center justify-between">
                      <div className="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <Cpu className="w-4 h-4" />
                      </div>
                      <Badge variant="success">Active</Badge>
                    </div>
                    <div>
                      <h4 className="text-sm font-bold text-white tracking-tight">example.com</h4>
                      <p className="text-xs text-zinc-400">Business Pro cPanel • 50GB NVMe</p>
                    </div>
                    <div className="pt-2 border-t border-zinc-800 flex items-center justify-between">
                      <Link href="/services/1">
                        <Button variant="secondary" size="sm">Manage Console</Button>
                      </Link>
                      <span className="text-[11px] font-mono text-zinc-500">Renews in 14d</span>
                    </div>
                  </div>

                  {/* Service Card 2 */}
                  <div className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 space-y-3">
                    <div className="flex items-center justify-between">
                      <div className="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                        <Server className="w-4 h-4" />
                      </div>
                      <Badge variant="success">Active</Badge>
                    </div>
                    <div>
                      <h4 className="text-sm font-bold text-white tracking-tight">app.mysite.io</h4>
                      <p className="text-xs text-zinc-400">VPS Node • 4 vCPU, 8GB RAM</p>
                    </div>
                    <div className="pt-2 border-t border-zinc-800 flex items-center justify-between">
                      <Link href="/services/2">
                        <Button variant="secondary" size="sm">Manage Console</Button>
                      </Link>
                      <span className="text-[11px] font-mono text-zinc-500">Renews in 28d</span>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </div>

            {/* Right Col: Balance & Support PIN */}
            <div className="space-y-6">
              {/* Credit Balance Card */}
              <Card className="p-5 space-y-3">
                <div className="flex items-center justify-between text-xs">
                  <span className="text-zinc-400">Account Balance</span>
                  <Link href="/billing">
                    <span className="text-xs text-indigo-400 font-semibold hover:underline">+ Add Funds</span>
                  </Link>
                </div>
                <div className="text-2xl font-bold font-mono text-white">{user?.balance || "$145.00"}</div>
                <div className="w-full bg-zinc-800 h-1.5 rounded-full overflow-hidden">
                  <div className="h-full bg-gradient-to-r from-indigo-500 to-cyan-400 rounded-full" style={{ width: "75%" }} />
                </div>
                <div className="flex items-center justify-between text-[11px] text-zinc-400">
                  <span>Auto-Renew: <strong className="text-emerald-400">ON</strong></span>
                  <span>Next billing: Aug 27</span>
                </div>
              </Card>

              {/* Support PIN Card */}
              <Card className="p-5 space-y-3">
                <div className="flex items-center justify-between text-xs">
                  <span className="text-zinc-400">Support PIN</span>
                  <span className="text-zinc-500">Expires in 2h</span>
                </div>
                <div className="p-3 rounded-xl bg-zinc-900 border border-zinc-800 flex items-center justify-between">
                  <span className="text-xl font-bold font-mono text-cyan-400 tracking-wider">{user?.supportPin || "849-201"}</span>
                  <button onClick={() => alert("PIN Regenerated")} className="p-1.5 rounded bg-zinc-800 hover:bg-zinc-700 text-zinc-300">
                    <RefreshCw className="w-4 h-4" />
                  </button>
                </div>
                <p className="text-[11px] text-zinc-500">Share this 6-digit PIN with customer support representatives for account verification.</p>
              </Card>
            </div>
          </div>
        </main>
      </div>
    </div>
  );
}
