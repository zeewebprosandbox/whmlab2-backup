"use client";

import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Globe, Plus, RefreshCw, Network, ShieldCheck, CheckCircle2 } from "lucide-react";

export default function DomainsPage() {
  const domains = [
    { id: 1, name: "example.com", regDate: "2024-08-27", expiryDate: "2027-08-27", autoRenew: true, status: "Active" },
    { id: 2, name: "mysite.io", regDate: "2025-01-15", expiryDate: "2026-09-12", autoRenew: true, status: "Active" },
  ];

  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
          {/* Page Header */}
          <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
              <h1 className="text-2xl font-extrabold text-white tracking-tight">Domain Portfolio</h1>
              <p className="text-xs text-zinc-400">Manage registered domains, WHOIS privacy toggles, and DNS nameservers.</p>
            </div>

            <Button className="gap-1.5">
              <Plus className="w-4 h-4" />
              <span>Register New Domain</span>
            </Button>
          </div>

          {/* Bulk Toolbar */}
          <Card className="p-4 flex items-center justify-between">
            <div className="flex items-center gap-2">
              <Button variant="outline" size="sm" className="gap-1.5">
                <RefreshCw className="w-3.5 h-3.5 text-cyan-400" />
                Bulk Renew
              </Button>
              <Button variant="outline" size="sm" className="gap-1.5">
                <Network className="w-3.5 h-3.5 text-indigo-400" />
                Update Nameservers
              </Button>
            </div>

            <span className="text-xs text-zinc-400 font-mono">2 Domains Active</span>
          </Card>

          {/* Domain Table */}
          <Card className="p-6">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-zinc-300">
                <thead className="bg-zinc-900 text-zinc-400 uppercase font-semibold text-[11px]">
                  <tr>
                    <th className="px-4 py-3 rounded-l-lg">Domain Name</th>
                    <th className="px-4 py-3">Registration Date</th>
                    <th className="px-4 py-3">Expiry Date</th>
                    <th className="px-4 py-3">Auto-Renew</th>
                    <th className="px-4 py-3">Status</th>
                    <th className="px-4 py-3 rounded-r-lg text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-800">
                  {domains.map((d) => (
                    <tr key={d.id} className="hover:bg-zinc-800/30 transition-colors">
                      <td className="px-4 py-4 font-bold text-white text-sm">{d.name}</td>
                      <td className="px-4 py-4 font-mono text-zinc-400">{d.regDate}</td>
                      <td className="px-4 py-4 font-mono text-zinc-200">{d.expiryDate}</td>
                      <td className="px-4 py-4">
                        <span className="inline-flex items-center gap-1 text-emerald-400 font-semibold">
                          <CheckCircle2 className="w-3.5 h-3.5" /> ON
                        </span>
                      </td>
                      <td className="px-4 py-4"><Badge variant="success">{d.status}</Badge></td>
                      <td className="px-4 py-4 text-right">
                        <Button variant="outline" size="sm">Manage DNS</Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </Card>
        </main>
      </div>
    </div>
  );
}
