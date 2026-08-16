"use client";

import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Mail, Phone, MapPin, Send } from "lucide-react";

export default function ContactPage() {
  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-4xl mx-auto w-full">
          <div>
            <h1 className="text-2xl font-extrabold text-white tracking-tight">Contact Sales & Support</h1>
            <p className="text-xs text-zinc-400">Have questions about dedicated hosting or enterprise clusters? Talk to our team.</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Card className="p-5 space-y-2">
              <Mail className="w-5 h-5 text-indigo-400" />
              <h3 className="text-xs font-bold text-white">Sales Department</h3>
              <p className="text-[11px] text-zinc-400 font-mono">sales@whmplatform.com</p>
            </Card>

            <Card className="p-5 space-y-2">
              <Phone className="w-5 h-5 text-cyan-400" />
              <h3 className="text-xs font-bold text-white">24/7 Hotline</h3>
              <p className="text-[11px] text-zinc-400 font-mono">+1 (800) 555-0199</p>
            </Card>

            <Card className="p-5 space-y-2">
              <MapPin className="w-5 h-5 text-amber-400" />
              <h3 className="text-xs font-bold text-white">Datacenter Location</h3>
              <p className="text-[11px] text-zinc-400">Ashburn, Virginia, USA</p>
            </Card>
          </div>

          <Card className="p-6 space-y-4">
            <h3 className="text-sm font-semibold text-white">Send Inquiry</h3>
            <form className="space-y-4" onSubmit={(e) => { e.preventDefault(); alert("Inquiry sent!"); }}>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="text-xs text-zinc-400 block mb-1">Your Name</label>
                  <input type="text" required className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white" />
                </div>
                <div>
                  <label className="text-xs text-zinc-400 block mb-1">Email Address</label>
                  <input type="email" required className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white" />
                </div>
              </div>

              <div>
                <label className="text-xs text-zinc-400 block mb-1">Message</label>
                <textarea rows={4} required className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white" placeholder="Describe your hosting requirements..." />
              </div>

              <div className="flex justify-end">
                <Button type="submit" className="gap-2">
                  <Send className="w-4 h-4" />
                  <span>Send Message</span>
                </Button>
              </div>
            </form>
          </Card>
        </main>
      </div>
    </div>
  );
}
