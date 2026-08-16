"use client";

import { useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { useAuth } from "@/lib/auth-context";
import { Server, ShieldCheck, Zap, Lock, ArrowRight, Sparkles } from "lucide-react";

export default function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const { login } = useAuth();
  const router = useRouter();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!email) return;
    login(email);
    router.push("/dashboard");
  };

  return (
    <div className="min-h-screen bg-[#09090b] text-[#f4f4f5] flex flex-col lg:flex-row overflow-hidden font-sans">
      {/* Left 55%: Mesh Animation & Branding */}
      <div className="lg:w-[55%] relative flex flex-col justify-between p-8 lg:p-16 overflow-hidden border-b lg:border-b-0 lg:border-r border-zinc-800 bg-[#0c0c0e]">
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
          <Badge variant="default" className="gap-2">
            <span className="w-2 h-2 rounded-full bg-cyan-400 orb-pulse" />
            Orbital Platform v2.0
          </Badge>
          <h1 className="text-3xl lg:text-5xl font-extrabold text-white leading-[1.15]">
            High performance hosting, simplified.
          </h1>
          <p className="text-zinc-400 text-sm leading-relaxed">
            Manage NVMe cloud servers, domains, SSL certificates, and automated billing through a high-speed unified console.
          </p>

          <div className="grid grid-cols-2 gap-4 pt-6">
            <div className="flex items-center gap-3 p-3 rounded-xl bg-zinc-900/60 border border-zinc-800">
              <div className="w-8 h-8 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-400">
                <ShieldCheck className="w-4 h-4" />
              </div>
              <div>
                <div className="text-xs font-semibold text-white">99.99% Uptime</div>
                <div className="text-[10px] text-zinc-500">Automated failover</div>
              </div>
            </div>

            <div className="flex items-center gap-3 p-3 rounded-xl bg-zinc-900/60 border border-zinc-800">
              <div className="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center text-cyan-400">
                <Zap className="w-4 h-4" />
              </div>
              <div>
                <div className="text-xs font-semibold text-white">NVMe Speed</div>
                <div className="text-[10px] text-zinc-500">Sub-10ms latency</div>
              </div>
            </div>
          </div>
        </div>

        <div className="relative z-10 flex items-center justify-between text-xs text-zinc-500 pt-6 border-t border-zinc-800">
          <span>&copy; {new Date().getFullYear()} WHM Platform.</span>
          <div className="flex items-center gap-1.5 text-zinc-400">
            <Lock className="w-3.5 h-3.5 text-cyan-400" />
            <span>256-bit SSL Encrypted</span>
          </div>
        </div>
      </div>

      {/* Right 45%: Clean Form Panel */}
      <div className="lg:w-[45%] flex flex-col justify-center items-center p-8 lg:p-16 bg-[#18181b]/40 relative z-10">
        <div className="w-full max-w-md space-y-6">
          <div className="space-y-2">
            <h2 className="text-2xl font-bold text-white">Welcome back</h2>
            <p className="text-xs text-zinc-400">Sign in to access your servers and billing console.</p>
          </div>

          <form className="space-y-4" onSubmit={handleSubmit}>
            <div className="space-y-1">
              <label className="text-xs font-medium text-zinc-300">Username or Email</label>
              <input
                type="text"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="name@example.com"
                required
                className="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
              />
            </div>

            <div className="space-y-1">
              <div className="flex items-center justify-between">
                <label className="text-xs font-medium text-zinc-300">Password</label>
                <Link href="#" className="text-xs text-indigo-400 hover:underline">Forgot password?</Link>
              </div>
              <input
                type="password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                required
                className="w-full px-4 py-2.5 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white placeholder-zinc-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all"
              />
            </div>

            <div className="flex items-center justify-between pt-1">
              <label className="flex items-center gap-2 cursor-pointer text-xs text-zinc-400">
                <input type="checkbox" className="rounded bg-zinc-900 border-zinc-800 text-indigo-600 focus:ring-0" />
                Remember this device
              </label>

              <button type="button" onClick={() => alert("Magic link sent to your email!")} className="text-xs text-cyan-400 hover:underline inline-flex items-center gap-1">
                <Sparkles className="w-3 h-3" />
                Magic Link
              </button>
            </div>

            <Button type="submit" size="lg" className="w-full gap-2">
              <span>Sign In to Console</span>
              <ArrowRight className="w-4 h-4" />
            </Button>
          </form>
        </div>
      </div>
    </div>
  );
}
