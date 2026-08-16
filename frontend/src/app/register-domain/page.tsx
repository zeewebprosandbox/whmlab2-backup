"use client";

import { useState } from "react";
import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Globe, Search, CheckCircle2, ShoppingCart } from "lucide-react";

export default function RegisterDomainPage() {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState<any[]>([]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (!query) return;
    const base = query.split(".")[0];

    setResults([
      { domain: `${base}.com`, price: "$9.99/yr", available: true },
      { domain: `${base}.io`, price: "$29.99/yr", available: true },
      { domain: `${base}.net`, price: "$12.99/yr", available: false },
      { domain: `${base}.dev`, price: "$19.99/yr", available: true },
    ]);
  };

  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
          {/* Header */}
          <div>
            <h1 className="text-2xl font-extrabold text-white tracking-tight">Register & Transfer Domains</h1>
            <p className="text-xs text-zinc-400">Search instant TLD availability, WHOIS privacy protection, and automated DNS setup.</p>
          </div>

          {/* Search Bar Card */}
          <Card className="p-6">
            <form onSubmit={handleSearch} className="flex gap-2">
              <div className="relative flex-1">
                <Globe className="w-5 h-5 text-zinc-500 absolute left-4 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={query}
                  onChange={(e) => setQuery(e.target.value)}
                  placeholder="Enter domain name (e.g. mycompany)"
                  className="w-full pl-12 pr-4 py-3 bg-zinc-900 border border-zinc-800 rounded-lg text-sm text-white focus:outline-none focus:border-indigo-500"
                />
              </div>
              <Button type="submit" size="lg" className="gap-2 px-6">
                <Search className="w-4 h-4" />
                <span>Search</span>
              </Button>
            </form>
          </Card>

          {/* Search Results Matrix */}
          {results.length > 0 && (
            <Card className="p-6 space-y-4">
              <h3 className="text-sm font-semibold text-white">Availability Results for "{query}"</h3>
              <div className="divide-y divide-zinc-800">
                {results.map((res) => (
                  <div key={res.domain} className="py-3.5 flex items-center justify-between">
                    <div className="flex items-center gap-3">
                      <span className="font-mono text-sm font-bold text-white">{res.domain}</span>
                      {res.available ? (
                        <Badge variant="success">Available</Badge>
                      ) : (
                        <Badge variant="outline">Taken</Badge>
                      )}
                    </div>

                    <div className="flex items-center gap-4">
                      <span className="font-mono font-bold text-white text-sm">{res.price}</span>
                      {res.available ? (
                        <Button size="sm" onClick={() => alert(`Added ${res.domain} to cart!`)} className="gap-1.5">
                          <ShoppingCart className="w-3.5 h-3.5" />
                          <span>Add to Cart</span>
                        </Button>
                      ) : (
                        <Button variant="outline" size="sm" disabled>Unavailable</Button>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </Card>
          )}
        </main>
      </div>
    </div>
  );
}
