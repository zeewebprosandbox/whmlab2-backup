"use client";

import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Server, Cpu, Plus, Settings, ExternalLink, HardDrive } from "lucide-react";

export default function ServicesPage() {
  const services = [
    {
      id: 1,
      domain: "example.com",
      plan: "Business Pro cPanel",
      ip: "192.168.1.100",
      status: "Active",
      renewal: "Aug 27, 2026",
      usage: "35% Disk",
    },
    {
      id: 2,
      domain: "app.mysite.io",
      plan: "NVMe Cloud VPS",
      ip: "192.168.1.105",
      status: "Active",
      renewal: "Sep 12, 2026",
      usage: "68% RAM",
    },
    {
      id: 3,
      domain: "staging.devlab.org",
      plan: "Developer WordPress",
      ip: "192.168.1.110",
      status: "Active",
      renewal: "Oct 01, 2026",
      usage: "12% Disk",
    },
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
              <h1 className="text-2xl font-extrabold text-white tracking-tight">Hosting Services</h1>
              <p className="text-xs text-zinc-400">Manage NVMe web hosting accounts, cPanel instances, and Cloud VPS servers.</p>
            </div>

            <Button className="gap-1.5">
              <Plus className="w-4 h-4" />
              <span>Deploy New Service</span>
            </Button>
          </div>

          {/* Services Grid */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {services.map((service) => (
              <Card key={service.id} className="hover:border-zinc-700 transition-all flex flex-col justify-between">
                <CardHeader className="space-y-3">
                  <div className="flex items-center justify-between">
                    <div className="w-9 h-9 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                      <Server className="w-4 h-4" />
                    </div>
                    <Badge variant="success">{service.status}</Badge>
                  </div>
                  <div>
                    <CardTitle className="text-base truncate">{service.domain}</CardTitle>
                    <CardDescription>{service.plan}</CardDescription>
                  </div>
                </CardHeader>

                <CardContent className="space-y-4">
                  <div className="p-3 rounded-lg bg-zinc-900/60 border border-zinc-800 space-y-1 font-mono text-xs text-zinc-400">
                    <div className="flex justify-between">
                      <span>Server IP:</span>
                      <span className="text-zinc-200">{service.ip}</span>
                    </div>
                    <div className="flex justify-between">
                      <span>Renewal:</span>
                      <span className="text-zinc-200">{service.renewal}</span>
                    </div>
                    <div className="flex justify-between">
                      <span>Usage:</span>
                      <span className="text-cyan-400">{service.usage}</span>
                    </div>
                  </div>

                  <div className="flex items-center gap-2">
                    <Link href={`/services/${service.id}`} className="flex-1">
                      <Button variant="default" className="w-full">
                        Manage Console
                      </Button>
                    </Link>
                    <Button variant="outline" size="icon" title="Settings">
                      <Settings className="w-4 h-4" />
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>
        </main>
      </div>
    </div>
  );
}
