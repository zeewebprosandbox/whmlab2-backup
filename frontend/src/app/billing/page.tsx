"use client";

import { useState } from "react";
import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { useAuth } from "@/lib/auth-context";
import { Receipt, CreditCard, DollarSign, Download, Plus, CheckCircle2, ShieldCheck, X, Sparkles } from "lucide-react";

export default function BillingPage() {
  const { user } = useAuth();
  const [balance, setBalance] = useState(145.0);
  const [showDepositModal, setShowDepositModal] = useState(false);
  const [depositAmount, setDepositAmount] = useState<number>(50);
  const [paymentMethod, setPaymentMethod] = useState("card");
  const [selectedInvoice, setSelectedInvoice] = useState<any>(null);

  const [invoices, setInvoices] = useState([
    {
      id: "INV-2026-0891",
      date: "Aug 01, 2026",
      dueDate: "Aug 15, 2026",
      items: [
        { desc: "Business Pro cPanel NVMe Hosting (12 Months)", price: 155.88 },
        { desc: "Let's Encrypt Wildcard AutoSSL License", price: 0.00 },
        { desc: "Automated Daily Cloud Backups (1 Year)", price: 24.00 },
      ],
      amount: 179.88,
      status: "unpaid",
    },
    {
      id: "INV-2026-0742",
      date: "Jul 01, 2026",
      dueDate: "Jul 15, 2026",
      items: [
        { desc: "Cloud VPS - Node 01 (1 Month)", price: 49.99 },
        { desc: "Dedicated IP Address IPv4", price: 3.00 },
      ],
      amount: 52.99,
      status: "paid",
    },
    {
      id: "INV-2026-0610",
      date: "Jun 01, 2026",
      dueDate: "Jun 15, 2026",
      items: [
        { desc: "Domain Registration: example.com (1 Year)", price: 9.99 },
        { desc: "WHOIS Privacy Protection", price: 0.00 },
      ],
      amount: 9.99,
      status: "paid",
    },
  ]);

  const handleDeposit = () => {
    setBalance((prev) => prev + Number(depositAmount));
    setShowDepositModal(false);
    alert(`Successfully added $${depositAmount}.00 to your account credit balance!`);
  };

  const handlePayInvoice = (id: string, amount: number) => {
    if (balance < amount) {
      alert("Insufficient balance! Please add funds to pay this invoice.");
      setShowDepositModal(true);
      return;
    }
    setBalance((prev) => prev - amount);
    setInvoices((prev) =>
      prev.map((inv) => (inv.id === id ? { ...inv, status: "paid" } : inv))
    );
    setSelectedInvoice(null);
    alert(`Invoice ${id} paid successfully!`);
  };

  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
          {/* Page Header */}
          <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
              <h1 className="text-2xl font-extrabold text-white tracking-tight">Billing & Invoices</h1>
              <p className="text-xs text-zinc-400">Manage account credit balance, automated billing, and itemized invoice receipts.</p>
            </div>

            <Button onClick={() => setShowDepositModal(true)} className="gap-2">
              <Plus className="w-4 h-4" />
              <span>Add Credit Funds</span>
            </Button>
          </div>

          {/* Credit Balance Card */}
          <Card className="p-6 bg-gradient-to-r from-zinc-900 via-zinc-900 to-[#18181b] flex flex-col md:flex-row items-start md:items-center justify-between gap-6 border-zinc-800">
            <div className="space-y-1">
              <div className="text-[11px] font-bold uppercase tracking-wider text-zinc-500">Available Account Credit</div>
              <div className="text-4xl font-extrabold text-white font-mono tracking-tight">${balance.toFixed(2)}</div>
              <p className="text-xs text-zinc-400">Account credit is automatically applied to upcoming recurring renewals.</p>
            </div>

            <div className="flex flex-wrap items-center gap-3">
              <Button onClick={() => setShowDepositModal(true)} variant="cyan" size="lg" className="gap-2">
                <DollarSign className="w-4 h-4" />
                <span>Deposit Funds</span>
              </Button>
            </div>
          </Card>

          {/* Invoices Table */}
          <Card className="p-6 space-y-4">
            <div className="flex items-center justify-between">
              <div>
                <CardTitle className="text-sm font-semibold">Invoice History</CardTitle>
                <CardDescription>Itemized billing records and payment breakdown</CardDescription>
              </div>
              <span className="text-xs text-zinc-400 font-mono">{invoices.length} Total Invoices</span>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left text-xs text-zinc-300">
                <thead>
                  <tr className="border-b border-zinc-800 text-[10px] font-extrabold uppercase tracking-wider text-zinc-500">
                    <th className="pb-3">Invoice Number</th>
                    <th className="pb-3">Date</th>
                    <th className="pb-3">Due Date</th>
                    <th className="pb-3">Amount</th>
                    <th className="pb-3">Status</th>
                    <th className="pb-3 text-right">Actions</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-zinc-800">
                  {invoices.map((inv) => (
                    <tr key={inv.id} className="hover:bg-zinc-900/40 transition-colors">
                      <td className="py-4 font-mono font-bold text-white">{inv.id}</td>
                      <td className="py-4 font-mono text-zinc-400">{inv.date}</td>
                      <td className="py-4 font-mono text-zinc-400">{inv.dueDate}</td>
                      <td className="py-4 font-mono font-bold text-white">${inv.amount.toFixed(2)}</td>
                      <td className="py-4">
                        {inv.status === "paid" ? (
                          <Badge variant="success">Paid</Badge>
                        ) : (
                          <Badge variant="amber">Unpaid</Badge>
                        )}
                      </td>
                      <td className="py-4 text-right space-x-2">
                        <Button variant="outline" size="sm" onClick={() => setSelectedInvoice(inv)}>
                          View Invoice
                        </Button>
                        {inv.status === "unpaid" && (
                          <Button size="sm" onClick={() => handlePayInvoice(inv.id, inv.amount)}>
                            Pay Now
                          </Button>
                        )}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </Card>

          {/* Deposit Funds Modal */}
          {showDepositModal && (
            <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
              <Card className="max-w-md w-full p-6 space-y-5 relative">
                <div className="flex items-center justify-between">
                  <h3 className="text-base font-bold text-white flex items-center gap-2">
                    <DollarSign className="w-5 h-5 text-cyan-400" />
                    Deposit Credit Funds
                  </h3>
                  <button onClick={() => setShowDepositModal(false)} className="text-zinc-400 hover:text-white">
                    <X className="w-4 h-4" />
                  </button>
                </div>

                <div className="space-y-4 text-xs">
                  <div>
                    <label className="text-zinc-400 block mb-1">Select Preset Amount ($ USD)</label>
                    <div className="grid grid-cols-4 gap-2">
                      {[25, 50, 100, 250].map((amt) => (
                        <button
                          key={amt}
                          type="button"
                          onClick={() => setDepositAmount(amt)}
                          className={`py-2 rounded-lg font-mono font-bold text-xs border transition-all ${depositAmount === amt ? "bg-indigo-600 border-indigo-500 text-white" : "bg-zinc-900 border-zinc-800 text-zinc-400 hover:text-white"}`}
                        >
                          ${amt}
                        </button>
                      ))}
                    </div>
                  </div>

                  <div>
                    <label className="text-zinc-400 block mb-1">Custom Amount ($)</label>
                    <input
                      type="number"
                      min={10}
                      value={depositAmount}
                      onChange={(e) => setDepositAmount(Number(e.target.value))}
                      className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-sm text-white font-mono focus:outline-none focus:border-indigo-500"
                    />
                  </div>

                  <div>
                    <label className="text-zinc-400 block mb-1">Payment Method</label>
                    <div className="space-y-2">
                      <label className="flex items-center justify-between p-3 rounded-lg bg-zinc-900 border border-zinc-800 cursor-pointer">
                        <div className="flex items-center gap-2">
                          <input type="radio" name="method" checked={paymentMethod === "card"} onChange={() => setPaymentMethod("card")} />
                          <CreditCard className="w-4 h-4 text-indigo-400" />
                          <span className="text-white font-medium">Credit / Debit Card (Stripe)</span>
                        </div>
                        <span className="text-[10px] text-zinc-500">Instant</span>
                      </label>
                      <label className="flex items-center justify-between p-3 rounded-lg bg-zinc-900 border border-zinc-800 cursor-pointer">
                        <div className="flex items-center gap-2">
                          <input type="radio" name="method" checked={paymentMethod === "paypal"} onChange={() => setPaymentMethod("paypal")} />
                          <span className="text-cyan-400 font-bold">PayPal</span>
                          <span className="text-white font-medium">PayPal Checkout</span>
                        </div>
                        <span className="text-[10px] text-zinc-500">Instant</span>
                      </label>
                    </div>
                  </div>
                </div>

                <div className="flex justify-end gap-2 pt-2 border-t border-zinc-800">
                  <Button variant="outline" onClick={() => setShowDepositModal(false)}>Cancel</Button>
                  <Button onClick={handleDeposit} className="gap-1.5">
                    <CheckCircle2 className="w-4 h-4" />
                    <span>Pay & Add Credit (${depositAmount}.00)</span>
                  </Button>
                </div>
              </Card>
            </div>
          )}

          {/* View Invoice Modal */}
          {selectedInvoice && (
            <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
              <Card className="max-w-xl w-full p-6 space-y-6 relative max-h-[90vh] overflow-y-auto">
                <div className="flex items-start justify-between border-b border-zinc-800 pb-4">
                  <div>
                    <Badge variant={selectedInvoice.status === "paid" ? "success" : "amber"}>
                      {selectedInvoice.status.toUpperCase()}
                    </Badge>
                    <h2 className="text-xl font-bold text-white font-mono mt-1">{selectedInvoice.id}</h2>
                    <p className="text-xs text-zinc-400">Issued: {selectedInvoice.date} • Due: {selectedInvoice.dueDate}</p>
                  </div>
                  <button onClick={() => setSelectedInvoice(null)} className="text-zinc-400 hover:text-white">
                    <X className="w-5 h-5" />
                  </button>
                </div>

                <div className="space-y-4 text-xs">
                  <div className="grid grid-cols-2 gap-4 p-4 rounded-xl bg-zinc-900/60 border border-zinc-800">
                    <div>
                      <div className="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Billed To</div>
                      <div className="text-white font-bold mt-1">{user?.name || "John Doe"}</div>
                      <div className="text-zinc-400">{user?.email || "john@example.com"}</div>
                    </div>
                    <div className="text-right">
                      <div className="text-zinc-500 font-bold uppercase tracking-wider text-[10px]">Pay To</div>
                      <div className="text-white font-bold mt-1">WHM Platform Inc.</div>
                      <div className="text-zinc-400">Ashburn, Virginia, USA</div>
                    </div>
                  </div>

                  {/* Line Items Table */}
                  <table className="w-full text-left">
                    <thead>
                      <tr className="border-b border-zinc-800 text-zinc-500 text-[10px] uppercase tracking-wider font-bold">
                        <th className="pb-2">Item Description</th>
                        <th className="pb-2 text-right">Price</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-zinc-800/60">
                      {selectedInvoice.items.map((item: any, idx: number) => (
                        <tr key={idx}>
                          <td className="py-2.5 text-zinc-300">{item.desc}</td>
                          <td className="py-2.5 text-right font-mono text-white">${item.price.toFixed(2)}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>

                  <div className="pt-3 border-t border-zinc-800 space-y-1 text-right font-mono">
                    <div className="text-zinc-400">Subtotal: ${selectedInvoice.amount.toFixed(2)}</div>
                    <div className="text-zinc-400">Tax (0%): $0.00</div>
                    <div className="text-lg font-bold text-white">Total: ${selectedInvoice.amount.toFixed(2)}</div>
                  </div>
                </div>

                <div className="flex items-center justify-between pt-4 border-t border-zinc-800">
                  <Button variant="outline" size="sm" onClick={() => alert("Downloading PDF Invoice...")} className="gap-1.5">
                    <Download className="w-4 h-4" />
                    <span>Download PDF</span>
                  </Button>

                  {selectedInvoice.status === "unpaid" && (
                    <Button size="sm" onClick={() => handlePayInvoice(selectedInvoice.id, selectedInvoice.amount)} className="gap-1.5">
                      <CheckCircle2 className="w-4 h-4" />
                      <span>Pay Invoice (${selectedInvoice.amount.toFixed(2)})</span>
                    </Button>
                  )}
                </div>
              </Card>
            </div>
          )}
        </main>
      </div>
    </div>
  );
}
