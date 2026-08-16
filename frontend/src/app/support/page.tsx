"use client";

import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { LifeBuoy, Plus, MessageSquare } from "lucide-react";

export default function SupportPage() {
  const tickets = [
    { id: "T-9481", subject: "SSL Certificate AutoSSL Renewal Issue", priority: "High", status: "Answered", lastReply: "10 mins ago" },
    { id: "T-8102", subject: "PHP 8.2 Extension Support Inquiry", priority: "Low", status: "Closed", lastReply: "2 days ago" },
  ];

  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
          {/* Header */}
          <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
              <h1 className="text-2xl font-extrabold text-white tracking-tight">Support Center</h1>
              <p className="text-xs text-zinc-400">Open support tickets with senior server engineers 24/7/365.</p>
            </div>

            <Button className="gap-1.5">
              <Plus className="w-4 h-4" />
              <span>Create New Ticket</span>
            </Button>
          </div>

          {/* Tickets Table */}
          <Card className="p-6">
            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-zinc-300">
                <thead className="bg-zinc-900 text-zinc-400 uppercase font-semibold text-[11px]">
                  <tr>
                    <th className="px-4 py-3 rounded-l-lg">Ticket ID & Subject</th>
                    <th className="px-4 py-3">Priority</th>
                    <th className="px-4 py-3">Status</th>
                    <th className="px-4 py-3">Last Reply</th>
                    <th className="px-4 py-3 rounded-r-lg text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-800">
                  {tickets.map((t) => (
                    <tr key={t.id} className="hover:bg-zinc-800/30 transition-colors">
                      <td className="px-4 py-4">
                        <div className="font-mono text-indigo-400 font-bold">{t.id}</div>
                        <div className="text-sm font-semibold text-white mt-0.5">{t.subject}</div>
                      </td>
                      <td className="px-4 py-4">
                        {t.priority === "High" ? <Badge variant="destructive">High</Badge> : <Badge variant="secondary">Low</Badge>}
                      </td>
                      <td className="px-4 py-4"><Badge variant="cyan">{t.status}</Badge></td>
                      <td className="px-4 py-4 font-mono text-zinc-400">{t.lastReply}</td>
                      <td className="px-4 py-4 text-right">
                        <Button variant="outline" size="sm">View Thread</Button>
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
