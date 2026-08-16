"use client";

import { useState } from "react";
import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Server, Cpu, CheckCircle2, ShieldCheck, ShoppingCart, ArrowLeft } from "lucide-react";

export default function StorePage() {
  const [termMonths, setTermMonths] = useState(12);
  const [selectedPlan, setSelectedPlan] = useState<any>(null);

  const plans = [
    {
      id: "starter-cpanel",
      category: "cpanel",
      name: "Starter Cloud",
      monthlyPrice: 6.99,
      annualPrice: 4.99,
      storage: "15 GB NVMe",
      bandwidth: "500 GB",
      ram: "1 GB Shared",
    },
    {
      id: "business-cpanel",
      category: "cpanel",
      name: "Business Pro cPanel",
      monthlyPrice: 16.99,
      annualPrice: 12.99,
      storage: "50 GB NVMe",
      bandwidth: "Unlimited",
      ram: "2 GB Dedicated",
    },
    {
      id: "cloud-vps-1",
      category: "vps",
      name: "Cloud VPS - Node 01",
      monthlyPrice: 49.99,
      annualPrice: 39.99,
      storage: "200 GB NVMe",
      bandwidth: "5 TB",
      ram: "8 GB DDR5",
    },
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
              <h1 className="text-2xl font-extrabold text-white tracking-tight">Hosting Store & Plan Configurator</h1>
              <p className="text-xs text-zinc-400">Select your hosting category and configure billing cycle term lengths.</p>
            </div>

            <div className="flex items-center gap-3">
              <label className="text-xs text-zinc-400">Term Length:</label>
              <select
                value={termMonths}
                onChange={(e) => setTermMonths(Number(e.target.value))}
                className="px-3 py-1.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs font-mono text-white focus:outline-none focus:border-indigo-500"
              >
                <option value={1}>1 Month (Monthly)</option>
                <option value={12}>12 Months (20% OFF)</option>
                <option value={24}>24 Months (30% OFF)</option>
                <option value={36}>36 Months (40% OFF)</option>
              </select>
            </div>
          </div>

          {/* Category Grid */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {plans.map((plan) => {
              const price = termMonths > 1 ? plan.annualPrice : plan.monthlyPrice;

              return (
                <Card key={plan.id} className="p-6 flex flex-col justify-between hover:border-zinc-700 transition-all space-y-6">
                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <Badge variant="cyan">{plan.category.toUpperCase()}</Badge>
                      <span className="text-[11px] font-mono text-emerald-400 font-semibold">Instant Provision</span>
                    </div>

                    <div>
                      <h3 className="text-lg font-bold text-white">{plan.name}</h3>
                      <div className="text-3xl font-extrabold font-mono text-white mt-2">
                        ${price.toFixed(2)}
                        <span className="text-xs text-zinc-500 font-sans font-normal">/mo</span>
                      </div>
                    </div>

                    <ul className="space-y-2 text-xs text-zinc-300 pt-4 border-t border-zinc-800">
                      <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> {plan.storage} Storage</li>
                      <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> {plan.bandwidth} Bandwidth</li>
                      <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> {plan.ram} Memory</li>
                      <li className="flex items-center gap-2"><CheckCircle2 className="w-4 h-4 text-emerald-400" /> Automated Let's Encrypt SSL</li>
                    </ul>
                  </div>

                  <Button
                    onClick={() => setSelectedPlan(plan)}
                    className="w-full gap-2"
                  >
                    <ShoppingCart className="w-4 h-4" />
                    <span>Configure & Order</span>
                  </Button>
                </Card>
              );
            })}
          </div>

          {/* Configuration Modal */}
          {selectedPlan && (
            <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
              <Card className="max-w-md w-full p-6 space-y-4 relative">
                <div className="flex items-center justify-between">
                  <h3 className="text-base font-bold text-white">Configure {selectedPlan.name}</h3>
                  <button onClick={() => setSelectedPlan(null)} className="text-zinc-400 hover:text-white">&times;</button>
                </div>

                <div className="space-y-3 text-xs">
                  <div>
                    <label className="text-zinc-400 block mb-1">Primary Domain Name</label>
                    <input type="text" placeholder="mydomain.com" className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-white font-mono" />
                  </div>

                  <div className="p-3 bg-zinc-900 rounded-lg space-y-2 border border-zinc-800">
                    <div className="flex justify-between font-medium">
                      <span>Term: {termMonths} Months</span>
                      <span className="font-mono text-white">${((termMonths > 1 ? selectedPlan.annualPrice : selectedPlan.monthlyPrice) * termMonths).toFixed(2)}</span>
                    </div>
                  </div>
                </div>

                <div className="flex justify-end gap-2 pt-2">
                  <Button variant="outline" onClick={() => setSelectedPlan(null)}>Cancel</Button>
                  <Link href="/billing">
                    <Button onClick={() => alert("Order submitted to cart!")}>Proceed to Checkout</Button>
                  </Link>
                </div>
              </Card>
            </div>
          )}
        </main>
      </div>
    </div>
  );
}
