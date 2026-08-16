"use client";

import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Search, Sparkles, BookOpen } from "lucide-react";

export default function AnnouncementsPage() {
  const articles = [
    { id: 1, category: "cPanel", title: "How to Configure Let's Encrypt Wildcard SSL in WHM", date: "Aug 12, 2026" },
    { id: 2, category: "DNS", title: "Setting up Custom Nameservers for Reseller Accounts", date: "Aug 05, 2026" },
    { id: 3, category: "PHP", title: "Migrating from PHP 8.1 to PHP 8.2 with Zero Downtime", date: "Jul 28, 2026" },
  ];

  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-5xl mx-auto w-full">
          <div>
            <h1 className="text-2xl font-extrabold text-white tracking-tight">Announcements & Knowledge Base</h1>
            <p className="text-xs text-zinc-400">Technical documentation, release notes, and server configuration guides.</p>
          </div>

          <Card className="p-4 relative">
            <Search className="w-4 h-4 text-zinc-500 absolute left-7 top-1/2 -translate-y-1/2" />
            <input
              type="text"
              placeholder="Search knowledge base articles..."
              className="w-full pl-10 pr-4 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white"
            />
          </Card>

          <div className="space-y-4">
            {articles.map((item) => (
              <Card key={item.id} className="p-5 hover:border-zinc-700 transition-all flex items-center justify-between">
                <div className="space-y-1">
                  <Badge variant="cyan">{item.category}</Badge>
                  <h3 className="text-sm font-bold text-white hover:text-indigo-400 cursor-pointer">{item.title}</h3>
                  <p className="text-[11px] font-mono text-zinc-500">Published: {item.date}</p>
                </div>
                <BookOpen className="w-4 h-4 text-zinc-500" />
              </Card>
            ))}
          </div>
        </main>
      </div>
    </div>
  );
}
