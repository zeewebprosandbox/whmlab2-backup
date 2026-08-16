"use client";

import { useState } from "react";
import Link from "next/link";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Server,
  Search,
  Zap,
  ShieldCheck,
  Globe,
  Cpu,
  ArrowRight,
  CheckCircle2,
  Lock,
  Layers,
  Sparkles,
  MessageSquare,
  ChevronRight
} from "lucide-react";

export default function LandingPage() {
  const [domainQuery, setDomainQuery] = useState("");
  const [isAnnual, setIsAnnual] = useState(true);
  const [searchResult, setSearchResult] = useState<{ domain: string; price: string } | null>(null);

  const handleDomainSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (!domainQuery) return;
    const clean = domainQuery.toLowerCase().trim();
    const domain = clean.includes(".") ? clean : `${clean}.com`;
    setSearchResult({
      domain,
      price: domain.endsWith(".io") ? "$29.99/yr" : "$9.99/yr",
    });
  };

  return (
    <div className="min-h-screen bg-[#09090b] text-[#f4f4f5] font-sans selection:bg-indigo-500 selection:text-white">
      {/* Public Header Navigation */}
      <header className="h-20 border-b border-zinc-800/80 bg-[#09090b]/80 backdrop-blur-md sticky top-0 z-50">
        <div className="max-w-7xl mx-auto h-full px-6 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-3 group">
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-cyan-400 p-[1px] shadow-[0_0_20px_rgba(99,102,241,0.35)]">
              <div className="w-full h-full bg-[#09090b] rounded-[11px] flex items-center justify-center text-indigo-400 group-hover:text-cyan-400 transition-colors">
                <Server className="w-5 h-5" />
              </div>
            </div>
            <div>
              <span className="block text-lg font-extrabold text-white tracking-tight leading-none">WHM Platform</span>
              <span className="block text-[10px] font-bold text-zinc-500 uppercase tracking-widest mt-1">Cloud Infrastructure</span>
            </div>
          </Link>

          {/* Navigation Links */}
          <nav className="hidden md:flex items-center gap-8 text-xs font-semibold text-zinc-400">
            <Link href="/store" className="hover:text-white transition-colors">Hosting Store</Link>
            <Link href="/register-domain" className="hover:text-white transition-colors">Domain Search</Link>
            <Link href="/announcements" className="hover:text-white transition-colors">Knowledge Base</Link>
            <Link href="/contact" className="hover:text-white transition-colors">Contact Sales</Link>
          </nav>

          {/* Action CTAs */}
          <div className="flex items-center gap-3">
            <Link href="/login">
              <Button variant="ghost" size="sm" className="text-xs">Sign In</Button>
            </Link>
            <Link href="/dashboard">
              <Button size="sm" className="gap-1.5">
                <span>Client Console</span>
                <ArrowRight className="w-3.5 h-3.5" />
              </Button>
            </Link>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative pt-16 pb-24 px-6 overflow-hidden">
        {/* Glow Gradients */}
        <div className="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[350px] bg-indigo-600/15 blur-[120px] rounded-full pointer-events-none" />
        <div className="absolute top-1/3 right-10 w-[400px] h-[300px] bg-cyan-400/10 blur-[100px] rounded-full pointer-events-none" />

        <div className="max-w-5xl mx-auto text-center space-y-6 relative z-10">
          <Badge variant="cyan" className="gap-2 py-1 px-3">
            <span className="w-2 h-2 rounded-full bg-cyan-400 orb-pulse" />
            Next-Gen NVMe Cloud Architecture • All Nodes Operational
          </Badge>

          <h1 className="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-[1.1]">
            Ultra-fast cloud web hosting <br />
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-cyan-300 to-indigo-500">
              built for modern developers & businesses.
            </span>
          </h1>

          <p className="text-sm sm:text-base text-zinc-400 max-w-2xl mx-auto leading-relaxed">
            Deploy NVMe SSD cPanel accounts, WordPress instances, and high-memory VPS clusters with automated Let's Encrypt SSL, sub-10ms response times, and 24/7 expert support.
          </p>

          {/* Instant Domain Search Box */}
          <div className="max-w-2xl mx-auto pt-6">
            <form onSubmit={handleDomainSearch} className="p-2 bg-zinc-900/90 border border-zinc-800 rounded-2xl shadow-2xl backdrop-blur-md flex flex-col sm:flex-row items-center gap-2">
              <div className="relative flex-1 w-full">
                <Globe className="w-5 h-5 text-zinc-500 absolute left-4 top-1/2 -translate-y-1/2" />
                <input
                  type="text"
                  value={domainQuery}
                  onChange={(e) => setDomainQuery(e.target.value)}
                  placeholder="Find your perfect domain name (e.g. mycompany.com)"
                  className="w-full pl-12 pr-4 py-3 bg-transparent text-sm text-white placeholder-zinc-500 focus:outline-none"
                />
              </div>
              <Button type="submit" size="lg" className="w-full sm:w-auto px-6 gap-2">
                <Search className="w-4 h-4" />
                <span>Search Domain</span>
              </Button>
            </form>

            {/* Search Result Instant Display */}
            {searchResult && (
              <div className="mt-3 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl flex items-center justify-between text-xs animate-in fade-in">
                <div className="flex items-center gap-2">
                  <CheckCircle2 className="w-4 h-4 text-emerald-400" />
                  <span className="text-white font-bold font-mono">{searchResult.domain}</span>
                  <span className="text-emerald-400 font-semibold">is available!</span>
                </div>
                <div className="flex items-center gap-3">
                  <span className="text-white font-mono font-bold">{searchResult.price}</span>
                  <Link href="/register-domain">
                    <Button size="sm" variant="cyan">Register Now</Button>
                  </Link>
                </div>
              </div>
            )}

            {/* TLD Badges */}
            <div className="flex flex-wrap items-center justify-center gap-3 mt-4 text-xs font-mono text-zinc-400">
              <span className="px-2.5 py-1 rounded-md bg-zinc-900 border border-zinc-800"><strong className="text-white">.com</strong> $9.99</span>
              <span className="px-2.5 py-1 rounded-md bg-zinc-900 border border-zinc-800"><strong className="text-cyan-400">.io</strong> $29.99</span>
              <span className="px-2.5 py-1 rounded-md bg-zinc-900 border border-zinc-800"><strong className="text-white">.net</strong> $12.99</span>
              <span className="px-2.5 py-1 rounded-md bg-zinc-900 border border-zinc-800"><strong className="text-white">.dev</strong> $19.99</span>
            </div>
          </div>
        </div>
      </section>

      {/* Interactive Pricing Tier Section */}
      <section className="py-20 px-6 border-t border-zinc-800/80 bg-[#0c0c0e]">
        <div className="max-w-7xl mx-auto space-y-12">
          <div className="text-center space-y-3">
            <h2 className="text-3xl font-extrabold text-white tracking-tight">Transparent NVMe Cloud Pricing</h2>
            <p className="text-xs text-zinc-400 max-w-lg mx-auto">Instant automated cPanel setup. No hidden bandwidth charges.</p>

            {/* Annual vs Monthly Toggle */}
            <div className="inline-flex items-center gap-3 p-1.5 rounded-full bg-zinc-900 border border-zinc-800 mt-4">
              <button
                onClick={() => setIsAnnual(false)}
                className={`px-4 py-1.5 rounded-full text-xs font-semibold transition-all ${!isAnnual ? "bg-zinc-800 text-white" : "text-zinc-400"}`}
              >
                Monthly Billing
              </button>
              <button
                onClick={() => setIsAnnual(true)}
                className={`px-4 py-1.5 rounded-full text-xs font-semibold transition-all flex items-center gap-1.5 ${isAnnual ? "bg-indigo-600 text-white" : "text-zinc-400"}`}
              >
                <span>Annual Billing</span>
                <span className="px-1.5 py-0.5 rounded bg-cyan-400 text-black text-[10px] font-bold">20% OFF</span>
              </button>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* Starter Plan */}
            <Card className="p-8 space-y-6 flex flex-col justify-between hover:border-zinc-700 transition-all">
              <div className="space-y-4">
                <Badge variant="outline">Starter Cloud</Badge>
                <div className="space-y-1">
                  <div className="text-4xl font-extrabold text-white font-mono">
                    {isAnnual ? "$4.99" : "$6.99"}<span className="text-xs text-zinc-500 font-sans font-normal">/mo</span>
                  </div>
                  <p className="text-xs text-zinc-400">Ideal for personal websites & blogs.</p>
                </div>
                <ul className="space-y-2.5 text-xs text-zinc-300 pt-4 border-t border-zinc-800">
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> 1 Website Account</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> 15 GB NVMe Storage</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> 500 GB Bandwidth</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> Free Wildcard AutoSSL</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> Direct cPanel Access</li>
                </ul>
              </div>
              <Link href="/store">
                <Button variant="outline" className="w-full">Choose Starter</Button>
              </Link>
            </Card>

            {/* Business Pro Plan */}
            <Card className="p-8 space-y-6 flex flex-col justify-between border-indigo-500/50 shadow-[0_0_30px_rgba(99,102,241,0.15)] relative">
              <div className="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 rounded-full bg-indigo-600 text-white text-[10px] font-bold uppercase tracking-wider">
                Most Popular
              </div>
              <div className="space-y-4">
                <Badge variant="default">Business Pro</Badge>
                <div className="space-y-1">
                  <div className="text-4xl font-extrabold text-white font-mono">
                    {isAnnual ? "$12.99" : "$16.99"}<span className="text-xs text-zinc-500 font-sans font-normal">/mo</span>
                  </div>
                  <p className="text-xs text-zinc-400">For growing eCommerce & SaaS applications.</p>
                </div>
                <ul className="space-y-2.5 text-xs text-zinc-300 pt-4 border-t border-zinc-800">
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-cyan-400" /> Unlimited Websites</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-cyan-400" /> 50 GB NVMe Storage</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-cyan-400" /> Unlimited Bandwidth</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-cyan-400" /> Daily Automated Backups</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-cyan-400" /> PHP 8.2 / 8.1 Selector</li>
                </ul>
              </div>
              <Link href="/store">
                <Button className="w-full">Deploy Business Pro</Button>
              </Link>
            </Card>

            {/* Enterprise VPS */}
            <Card className="p-8 space-y-6 flex flex-col justify-between hover:border-zinc-700 transition-all">
              <div className="space-y-4">
                <Badge variant="cyan">Enterprise VPS</Badge>
                <div className="space-y-1">
                  <div className="text-4xl font-extrabold text-white font-mono">
                    {isAnnual ? "$39.99" : "$49.99"}<span className="text-xs text-zinc-500 font-sans font-normal">/mo</span>
                  </div>
                  <p className="text-xs text-zinc-400">Dedicated CPU resources & root SSH access.</p>
                </div>
                <ul className="space-y-2.5 text-xs text-zinc-300 pt-4 border-t border-zinc-800">
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> 4 vCPU Cores (AMD EPYC)</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> 8 GB DDR5 RAM</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> 200 GB High Speed Storage</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> Dedicated IP Address</li>
                  <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> Full Root SSH Access</li>
                </ul>
              </div>
              <Link href="/store">
                <Button variant="outline" className="w-full">Deploy Enterprise</Button>
              </Link>
            </Card>
          </div>
        </div>
      </section>

      {/* Trust & Footer */}
      <footer className="border-t border-zinc-800 py-12 px-6 bg-[#09090b] text-xs text-zinc-500">
        <div className="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
          <div className="flex items-center gap-3">
            <div className="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-xs">
              W
            </div>
            <span>&copy; {new Date().getFullYear()} WHM Platform. All rights reserved.</span>
          </div>

          <div className="flex items-center gap-6">
            <Link href="/announcements" className="hover:text-white transition-colors">Knowledge Base</Link>
            <Link href="/contact" className="hover:text-white transition-colors">Contact Sales</Link>
            <span className="flex items-center gap-1.5 text-emerald-400 font-semibold">
              <span className="w-2 h-2 rounded-full bg-emerald-400" />
              All Systems Operational
            </span>
          </div>
        </div>
      </footer>
    </div>
  );
}
