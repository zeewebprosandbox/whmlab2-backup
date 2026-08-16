"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { useAuth } from "@/lib/auth-context";
import { Server, ShieldCheck, Zap, Lock, ArrowRight, UserCheck } from "lucide-react";

export default function RegisterPage() {
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    password: "",
    country: "United States",
    termsAccepted: false,
  });

  const { login } = useAuth();
  const router = useRouter();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!formData.email || !formData.termsAccepted) return;
    login(formData.email);
    router.push("/dashboard");
  };

  return (
    <div className="min-h-screen bg-[#09090b] text-[#f4f4f5] flex flex-col lg:flex-row overflow-hidden font-sans">
      {/* Left 50%: Branding & Highlights */}
      <div className="lg:w-[50%] relative flex flex-col justify-between p-8 lg:p-16 overflow-hidden border-b lg:border-b-0 lg:border-r border-zinc-800 bg-[#0c0c0e]">
        <div className="relative z-10">
          <Link href="/" className="inline-flex items-center gap-3">
            <div className="w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-500 to-cyan-400 p-[1px] shadow-[0_0_20px_rgba(99,102,241,0.3)]">
              <div className="w-full h-full bg-[#09090b] rounded-[7px] flex items-center justify-center text-indigo-400">
                <Server className="w-5 h-5" />
              </div>
            </div>
            <div>
              <span className="block text-lg font-bold text-white leading-none">WHM Platform</span>
              <span className="block text-[10px] font-semibold text-zinc-500 uppercase tracking-wider mt-1">Cloud Workspace</span>
            </div>
          </Link>
        </div>

        <div className="relative z-10 my-12 max-w-xl space-y-4">
          <Badge variant="cyan" className="gap-2">
            <span className="w-2 h-2 rounded-full bg-cyan-400 orb-pulse" />
            Instant Account Activation
          </Badge>
          <h1 className="text-3xl lg:text-5xl font-extrabold text-white leading-[1.15]">
            Create your cloud account in seconds.
          </h1>
          <p className="text-zinc-400 text-sm leading-relaxed">
            Get instant access to NVMe web hosting, automated Let's Encrypt SSL, DNS management, and 24/7 technical support.
          </p>

          <div className="grid grid-cols-2 gap-4 pt-6">
            <div className="flex items-center gap-3 p-3 rounded-xl bg-zinc-900/60 border border-zinc-800">
              <div className="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                <ShieldCheck className="w-4 h-4" />
              </div>
              <div>
                <div className="text-xs font-semibold text-white">Free $10 Credit</div>
                <div className="text-[10px] text-zinc-500">For new signups</div>
              </div>
            </div>

            <div className="flex items-center gap-3 p-3 rounded-xl bg-zinc-900/60 border border-zinc-800">
              <div className="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400">
                <Zap className="w-4 h-4" />
              </div>
              <div>
                <div className="text-xs font-semibold text-white">No Credit Card</div>
                <div className="text-[10px] text-zinc-500">Required to start</div>
              </div>
            </div>
          </div>
        </div>

        <div className="relative z-10 flex items-center justify-between text-xs text-zinc-500 pt-6 border-t border-zinc-800">
          <span>&copy; {new Date().getFullYear()} WHM Platform.</span>
          <div className="flex items-center gap-1.5 text-zinc-400">
            <Lock className="w-3.5 h-3.5 text-cyan-400" />
            <span>256-bit SSL Secured</span>
          </div>
        </div>
      </div>

      {/* Right 50%: Registration Form */}
      <div className="lg:w-[50%] flex flex-col justify-center items-center p-8 lg:p-16 bg-[#18181b]/40 relative z-10">
        <div className="w-full max-w-md space-y-6">
          <div className="space-y-2">
            <h2 className="text-2xl font-bold text-white">Get started now</h2>
            <p className="text-xs text-zinc-400">Already have an account? <Link href="/login" className="text-indigo-400 hover:underline">Sign in here</Link></p>
          </div>

          <form className="space-y-4" onSubmit={handleSubmit}>
            <div className="grid grid-cols-2 gap-4">
              <div className="space-y-1">
                <label className="text-xs font-medium text-zinc-300">First Name</label>
                <input
                  type="text"
                  required
                  value={formData.firstName}
                  onChange={(e) => setFormData({ ...formData, firstName: e.target.value })}
                  placeholder="John"
                  className="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500"
                />
              </div>
              <div className="space-y-1">
                <label className="text-xs font-medium text-zinc-300">Last Name</label>
                <input
                  type="text"
                  required
                  value={formData.lastName}
                  onChange={(e) => setFormData({ ...formData, lastName: e.target.value })}
                  placeholder="Doe"
                  className="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500"
                />
              </div>
            </div>

            <div className="space-y-1">
              <label className="text-xs font-medium text-zinc-300">Email Address</label>
              <input
                type="email"
                required
                value={formData.email}
                onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                placeholder="name@example.com"
                className="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500"
              />
            </div>

            <div className="space-y-1">
              <label className="text-xs font-medium text-zinc-300">Password</label>
              <input
                type="password"
                required
                value={formData.password}
                onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                placeholder="Create password (min. 8 chars)"
                className="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500"
              />
            </div>

            <div className="space-y-1">
              <label className="text-xs font-medium text-zinc-300">Country / Region</label>
              <select
                value={formData.country}
                onChange={(e) => setFormData({ ...formData, country: e.target.value })}
                className="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white focus:outline-none focus:border-indigo-500"
              >
                <option value="United States">United States</option>
                <option value="United Kingdom">United Kingdom</option>
                <option value="Canada">Canada</option>
                <option value="Germany">Germany</option>
                <option value="Australia">Australia</option>
              </select>
            </div>

            <div className="pt-2">
              <label className="flex items-center gap-2 cursor-pointer text-xs text-zinc-400">
                <input
                  type="checkbox"
                  required
                  checked={formData.termsAccepted}
                  onChange={(e) => setFormData({ ...formData, termsAccepted: e.target.checked })}
                  className="rounded bg-zinc-900 border-zinc-800 text-indigo-600 focus:ring-0"
                />
                <span>I agree to the <Link href="#" className="text-indigo-400 hover:underline">Terms of Service</Link> & <Link href="#" className="text-indigo-400 hover:underline">Privacy Policy</Link></span>
              </label>
            </div>

            <Button type="submit" size="lg" className="w-full gap-2 mt-2">
              <UserCheck className="w-4 h-4" />
              <span>Create Account & Activate Console</span>
            </Button>
          </form>
        </div>
      </div>
    </div>
  );
}
