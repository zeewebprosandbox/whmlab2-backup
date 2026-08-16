"use client";

import { useState } from "react";
import Link from "next/link";
import { Sidebar } from "@/components/layout/sidebar";
import { Header } from "@/components/layout/header";
import { Button } from "@/components/ui/button";
import { Card, CardHeader, CardTitle, CardDescription, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import {
  Server,
  Globe,
  Database,
  Mail,
  ShieldCheck,
  Cpu,
  HardDrive,
  Copy,
  ExternalLink,
  Plus,
  RefreshCw,
  CheckCircle2,
  Folder,
  Network,
  Terminal,
  Settings,
  Lock,
  ArrowLeft,
  LayoutDashboard,
  X
} from "lucide-react";

export default function ServiceDetailsPage({ params }: { params: { id: string } }) {
  const [activeTab, setActiveTab] = useState("overview");
  const [copied, setCopied] = useState(false);
  const [phpVersion, setPhpVersion] = useState("8.2");

  // Modals state
  const [showDbModal, setShowDbModal] = useState(false);
  const [showMailModal, setShowMailModal] = useState(false);
  const [showDnsModal, setShowDnsModal] = useState(false);

  // Form states
  const [newDbName, setNewDbName] = useState("");
  const [newMailName, setNewMailName] = useState("");
  const [newMailPass, setNewMailPass] = useState("a9K#mQ2$xP8!");
  const [newDnsType, setNewDnsType] = useState("A");
  const [newDnsHost, setNewDnsHost] = useState("");
  const [newDnsValue, setNewDnsValue] = useState("");

  const [databases, setDatabases] = useState([
    { name: "db_wp_app_prod", size: "42.8 MB", user: "app_user" },
  ]);

  const [mailboxes, setMailboxes] = useState([
    { email: "admin@example.com", quota: "145 MB / 1000 MB (14.5%)", status: "Active" },
  ]);

  const [dnsRecords, setDnsRecords] = useState([
    { type: "A", host: "@", value: "192.168.1.100", ttl: 14400 },
    { type: "CNAME", host: "www", value: "example.com", ttl: 14400 },
    { type: "MX", host: "@", value: "mail.example.com", ttl: 14400 },
    { type: "TXT", host: "@", value: "v=spf1 a mx ~all", ttl: 14400 },
  ]);

  const copyDomain = () => {
    navigator.clipboard.writeText("example.com");
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  const handleCreateDb = () => {
    if (!newDbName) return;
    setDatabases([...databases, { name: `db_${newDbName}`, size: "0.1 MB", user: `${newDbName}_usr` }]);
    setNewDbName("");
    setShowDbModal(false);
    alert("Database created successfully!");
  };

  const handleCreateMail = () => {
    if (!newMailName) return;
    setMailboxes([...mailboxes, { email: `${newMailName}@example.com`, quota: "0 MB / 1000 MB (0%)", status: "Active" }]);
    setNewMailName("");
    setShowMailModal(false);
    alert("Mailbox created successfully!");
  };

  const handleAddDns = () => {
    if (!newDnsHost || !newDnsValue) return;
    setDnsRecords([...dnsRecords, { type: newDnsType, host: newDnsHost, value: newDnsValue, ttl: 14400 }]);
    setNewDnsHost("");
    setNewDnsValue("");
    setShowDnsModal(false);
    alert("DNS Record added successfully!");
  };

  return (
    <div className="flex min-h-screen bg-[#09090b]">
      <Sidebar />

      <div className="flex-1 flex flex-col min-w-0">
        <Header />

        <main className="p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full">
          {/* Breadcrumb */}
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-2 text-xs text-zinc-400 font-medium">
              <Link href="/dashboard" className="hover:text-white transition-colors">Dashboard</Link>
              <span>/</span>
              <Link href="/services" className="hover:text-white transition-colors">Services</Link>
              <span>/</span>
              <span className="text-white font-mono">example.com</span>
            </div>

            <Link href="/services">
              <Button variant="ghost" size="sm" className="gap-1.5 text-xs text-zinc-400">
                <ArrowLeft className="w-3.5 h-3.5" />
                Back to Services
              </Button>
            </Link>
          </div>

          {/* Hero Banner (cPanel Replacement Header) */}
          <Card className="p-6 lg:p-8 space-y-6 relative overflow-hidden">
            <div className="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
              <div className="space-y-2">
                <div className="flex items-center gap-3">
                  <Badge variant="success" className="gap-1.5">
                    <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 orb-pulse" />
                    ACTIVE
                  </Badge>
                  <span className="px-2.5 py-0.5 rounded-full bg-zinc-900 border border-zinc-800 text-zinc-300 text-xs font-mono">
                    IP: 192.168.1.100
                  </span>
                  <span className="text-xs text-zinc-400 flex items-center gap-1">
                    🇺🇸 US-East Datacenter (Virginia)
                  </span>
                </div>

                {/* Domain Name Copy to Clipboard */}
                <div className="flex items-center gap-3">
                  <h1 className="text-2xl lg:text-4xl font-extrabold text-white tracking-tight">
                    example.com
                  </h1>
                  <button onClick={copyDomain} className="p-2 rounded-lg bg-zinc-900 hover:bg-zinc-800 text-zinc-400 hover:text-white transition-colors">
                    <Copy className="w-4 h-4" />
                  </button>
                  {copied && <span className="text-xs text-cyan-400 font-semibold">Copied!</span>}
                </div>
                <p className="text-xs text-zinc-400">Business Pro cPanel • NVMe Web Hosting Instance</p>
              </div>

              {/* Primary CTA Bar */}
              <div className="flex flex-wrap items-center gap-3">
                <Button className="gap-2">
                  <ExternalLink className="w-4 h-4" />
                  <span>Open cPanel Direct</span>
                </Button>
                <Button variant="outline" className="gap-2">
                  <Mail className="w-4 h-4 text-cyan-400" />
                  <span>Webmail</span>
                </Button>
                <Button variant="outline" className="gap-2">
                  <Folder className="w-4 h-4 text-amber-400" />
                  <span>File Manager</span>
                </Button>
              </div>
            </div>

            {/* Tabbed Navigation Bar */}
            <div className="border-t border-zinc-800 pt-4">
              <div className="flex items-center gap-2 overflow-x-auto pb-2">
                <Button
                  variant={activeTab === "overview" ? "default" : "outline"}
                  size="sm"
                  onClick={() => setActiveTab("overview")}
                  className="gap-2"
                >
                  <LayoutDashboard className="w-4 h-4" />
                  Overview
                </Button>
                <Button
                  variant={activeTab === "files" ? "default" : "outline"}
                  size="sm"
                  onClick={() => setActiveTab("files")}
                  className="gap-2"
                >
                  <Database className="w-4 h-4" />
                  Files & DBs
                </Button>
                <Button
                  variant={activeTab === "email" ? "default" : "outline"}
                  size="sm"
                  onClick={() => setActiveTab("email")}
                  className="gap-2"
                >
                  <Mail className="w-4 h-4" />
                  Email Accounts
                </Button>
                <Button
                  variant={activeTab === "dns" ? "default" : "outline"}
                  size="sm"
                  onClick={() => setActiveTab("dns")}
                  className="gap-2"
                >
                  <Globe className="w-4 h-4" />
                  Domains & DNS
                </Button>
                <Button
                  variant={activeTab === "security" ? "default" : "outline"}
                  size="sm"
                  onClick={() => setActiveTab("security")}
                  className="gap-2"
                >
                  <ShieldCheck className="w-4 h-4" />
                  SSL & Security
                </Button>
                <Button
                  variant={activeTab === "advanced" ? "default" : "outline"}
                  size="sm"
                  onClick={() => setActiveTab("advanced")}
                  className="gap-2"
                >
                  <Settings className="w-4 h-4" />
                  Advanced PHP
                </Button>
              </div>
            </div>
          </Card>

          {/* 1. OVERVIEW TAB */}
          {activeTab === "overview" && (
            <div className="space-y-6">
              {/* Circular Progress Gauges Grid */}
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                <Card className="p-5 text-center space-y-3">
                  <div className="text-xs font-semibold text-zinc-400 uppercase tracking-wider">CPU Usage</div>
                  <div className="relative inline-flex items-center justify-center">
                    <svg className="w-20 h-20 transform -rotate-90">
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" className="text-zinc-800" fill="transparent" />
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" strokeDasharray="28, 100" className="text-cyan-400" fill="transparent" />
                    </svg>
                    <span className="absolute text-sm font-bold font-mono text-white">28%</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 font-mono">0.28 / 1.0 Cores</div>
                </Card>

                <Card className="p-5 text-center space-y-3">
                  <div className="text-xs font-semibold text-zinc-400 uppercase tracking-wider">RAM Usage</div>
                  <div className="relative inline-flex items-center justify-center">
                    <svg className="w-20 h-20 transform -rotate-90">
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" className="text-zinc-800" fill="transparent" />
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" strokeDasharray="64, 100" className="text-amber-400" fill="transparent" />
                    </svg>
                    <span className="absolute text-sm font-bold font-mono text-white">64%</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 font-mono">1.28 GB / 2.0 GB</div>
                </Card>

                <Card className="p-5 text-center space-y-3">
                  <div className="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Disk Storage</div>
                  <div className="relative inline-flex items-center justify-center">
                    <svg className="w-20 h-20 transform -rotate-90">
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" className="text-zinc-800" fill="transparent" />
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" strokeDasharray="35, 100" className="text-indigo-400" fill="transparent" />
                    </svg>
                    <span className="absolute text-sm font-bold font-mono text-white">35%</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 font-mono">17.5 GB / 50 GB</div>
                </Card>

                <Card className="p-5 text-center space-y-3">
                  <div className="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Bandwidth</div>
                  <div className="relative inline-flex items-center justify-center">
                    <svg className="w-20 h-20 transform -rotate-90">
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" className="text-zinc-800" fill="transparent" />
                      <circle cx="40" cy="40" r="32" stroke="currentColor" strokeWidth="6" strokeDasharray="18, 100" className="text-emerald-400" fill="transparent" />
                    </svg>
                    <span className="absolute text-sm font-bold font-mono text-white">18%</span>
                  </div>
                  <div className="text-[11px] text-zinc-500 font-mono">180 GB / Unlimited</div>
                </Card>
              </div>

              {/* Quick Utilities Grid */}
              <Card className="p-6 space-y-4">
                <CardTitle className="text-sm font-semibold">Quick Management Utilities</CardTitle>
                <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                  <button onClick={() => setActiveTab("files")} className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 text-left transition-all group space-y-2">
                    <Database className="w-5 h-5 text-indigo-400 group-hover:scale-110 transition-transform" />
                    <div>
                      <div className="text-xs font-semibold text-white">phpMyAdmin</div>
                      <div className="text-[11px] text-zinc-400">Database admin</div>
                    </div>
                  </button>

                  <button onClick={() => setActiveTab("email")} className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 text-left transition-all group space-y-2">
                    <Mail className="w-5 h-5 text-cyan-400 group-hover:scale-110 transition-transform" />
                    <div>
                      <div className="text-xs font-semibold text-white">Mailboxes</div>
                      <div className="text-[11px] text-zinc-400">Webmail & Forwarders</div>
                    </div>
                  </button>

                  <button onClick={() => setActiveTab("security")} className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 text-left transition-all group space-y-2">
                    <ShieldCheck className="w-5 h-5 text-emerald-400 group-hover:scale-110 transition-transform" />
                    <div>
                      <div className="text-xs font-semibold text-white">SSL Status</div>
                      <div className="text-[11px] text-zinc-400">AutoSSL Active</div>
                    </div>
                  </button>

                  <button onClick={() => alert("Backup Wizard launched!")} className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 text-left transition-all group space-y-2">
                    <Folder className="w-5 h-5 text-amber-400 group-hover:scale-110 transition-transform" />
                    <div>
                      <div className="text-xs font-semibold text-white">Backup Wizard</div>
                      <div className="text-[11px] text-zinc-400">Full cPanel Backups</div>
                    </div>
                  </button>

                  <button onClick={() => setActiveTab("dns")} className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 hover:border-zinc-700 text-left transition-all group space-y-2">
                    <Network className="w-5 h-5 text-rose-400 group-hover:scale-110 transition-transform" />
                    <div>
                      <div className="text-xs font-semibold text-white">Subdomains</div>
                      <div className="text-[11px] text-zinc-400">Manage domain aliases</div>
                    </div>
                  </button>
                </div>
              </Card>
            </div>
          )}

          {/* 2. FILES & DATABASES TAB */}
          {activeTab === "files" && (
            <Card className="p-6 space-y-4">
              <div className="flex items-center justify-between">
                <CardTitle className="text-sm font-semibold">MySQL Databases</CardTitle>
                <Button size="sm" onClick={() => setShowDbModal(true)} className="gap-1">+ Create Database</Button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs text-zinc-300">
                  <thead className="bg-zinc-900 text-zinc-400 uppercase font-semibold text-[11px]">
                    <tr>
                      <th className="px-4 py-3 rounded-l-lg">Database Name</th>
                      <th className="px-4 py-3">Size</th>
                      <th className="px-4 py-3">User Count</th>
                      <th className="px-4 py-3 rounded-r-lg text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-800">
                    {databases.map((db, idx) => (
                      <tr key={idx}>
                        <td className="px-4 py-3 font-mono text-white font-medium">{db.name}</td>
                        <td className="px-4 py-3 font-mono">{db.size}</td>
                        <td className="px-4 py-3">{db.user}</td>
                        <td className="px-4 py-3 text-right space-x-2">
                          <Button variant="outline" size="sm" onClick={() => alert("Launching phpMyAdmin...")}>phpMyAdmin</Button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </Card>
          )}

          {/* 3. EMAIL TAB */}
          {activeTab === "email" && (
            <Card className="p-6 space-y-4">
              <div className="flex items-center justify-between">
                <CardTitle className="text-sm font-semibold">Mailbox Accounts</CardTitle>
                <Button size="sm" onClick={() => setShowMailModal(true)}>+ Create Account</Button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs text-zinc-300">
                  <thead className="bg-zinc-900 text-zinc-400 uppercase font-semibold text-[11px]">
                    <tr>
                      <th className="px-4 py-3 rounded-l-lg">Mailbox</th>
                      <th className="px-4 py-3">Quota Used</th>
                      <th className="px-4 py-3">Status</th>
                      <th className="px-4 py-3 rounded-r-lg text-right">Actions</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-800">
                    {mailboxes.map((mail, idx) => (
                      <tr key={idx}>
                        <td className="px-4 py-3 text-white font-medium">{mail.email}</td>
                        <td className="px-4 py-3 font-mono">{mail.quota}</td>
                        <td className="px-4 py-3"><Badge variant="success">{mail.status}</Badge></td>
                        <td className="px-4 py-3 text-right">
                          <Button variant="outline" size="sm" onClick={() => alert("Webmail launched!")}>Launch Webmail</Button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </Card>
          )}

          {/* 4. DNS TAB */}
          {activeTab === "dns" && (
            <Card className="p-6 space-y-4">
              <div className="flex items-center justify-between">
                <CardTitle className="text-sm font-semibold">DNS Zone Editor</CardTitle>
                <Button size="sm" onClick={() => setShowDnsModal(true)}>+ Add Record</Button>
              </div>

              <div className="overflow-x-auto">
                <table className="w-full text-left text-xs text-zinc-300">
                  <thead className="bg-zinc-900 text-zinc-400 uppercase font-semibold text-[11px]">
                    <tr>
                      <th className="px-4 py-3 rounded-l-lg">Type</th>
                      <th className="px-4 py-3">Host / Name</th>
                      <th className="px-4 py-3">Value / Target</th>
                      <th className="px-4 py-3">TTL</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-zinc-800">
                    {dnsRecords.map((rec, idx) => (
                      <tr key={idx}>
                        <td className="px-4 py-3 font-mono font-bold text-cyan-400">{rec.type}</td>
                        <td className="px-4 py-3 font-mono text-white">{rec.host}</td>
                        <td className="px-4 py-3 font-mono text-zinc-300">{rec.value}</td>
                        <td className="px-4 py-3 font-mono text-zinc-400">{rec.ttl}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </Card>
          )}

          {/* 5. SECURITY TAB */}
          {activeTab === "security" && (
            <Card className="p-6 space-y-4">
              <CardTitle className="text-sm font-semibold">Let's Encrypt AutoSSL Protection</CardTitle>
              <p className="text-xs text-zinc-400">Automated 256-bit SSL certificates for all domains and subdomains.</p>
              <div className="p-4 rounded-xl bg-zinc-900/60 border border-zinc-800 flex items-center justify-between">
                <div className="flex items-center gap-3">
                  <ShieldCheck className="w-6 h-6 text-emerald-400" />
                  <div>
                    <div className="text-xs font-bold text-white">Wildcard SSL Active</div>
                    <div className="text-[11px] text-zinc-500 font-mono">Expires in 84 days (Auto-renews)</div>
                  </div>
                </div>
                <Button variant="outline" size="sm" onClick={() => alert("AutoSSL check triggered!")}>Run AutoSSL Check</Button>
              </div>
            </Card>
          )}

          {/* 6. ADVANCED PHP TAB */}
          {activeTab === "advanced" && (
            <Card className="p-6 space-y-4">
              <CardTitle className="text-sm font-semibold">PHP Version Selector & Extensions</CardTitle>
              <div className="space-y-4 text-xs">
                <div>
                  <label className="text-zinc-400 block mb-1">Active PHP Version</label>
                  <select
                    value={phpVersion}
                    onChange={(e) => {
                      setPhpVersion(e.target.value);
                      alert(`PHP Version changed to ${e.target.value}`);
                    }}
                    className="px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs font-mono text-white"
                  >
                    <option value="8.2">PHP 8.2 (Recommended)</option>
                    <option value="8.1">PHP 8.1</option>
                    <option value="8.0">PHP 8.0</option>
                  </select>
                </div>
              </div>
            </Card>
          )}

          {/* Create DB Modal */}
          {showDbModal && (
            <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
              <Card className="max-w-md w-full p-6 space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-base font-bold text-white">Create MySQL Database</h3>
                  <button onClick={() => setShowDbModal(false)} className="text-zinc-400 hover:text-white">&times;</button>
                </div>
                <div className="space-y-1">
                  <label className="text-xs text-zinc-400">Database Name Suffix</label>
                  <input
                    type="text"
                    value={newDbName}
                    onChange={(e) => setNewDbName(e.target.value)}
                    placeholder="my_app"
                    className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white font-mono"
                  />
                </div>
                <div className="flex justify-end gap-2 pt-2">
                  <Button variant="outline" onClick={() => setShowDbModal(false)}>Cancel</Button>
                  <Button onClick={handleCreateDb}>Create Database</Button>
                </div>
              </Card>
            </div>
          )}

          {/* Create Mailbox Modal */}
          {showMailModal && (
            <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
              <Card className="max-w-md w-full p-6 space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-base font-bold text-white">Create Mailbox Account</h3>
                  <button onClick={() => setShowMailModal(false)} className="text-zinc-400 hover:text-white">&times;</button>
                </div>
                <div className="space-y-3 text-xs">
                  <div>
                    <label className="text-zinc-400 block mb-1">Username Prefix</label>
                    <div className="flex items-center gap-2">
                      <input
                        type="text"
                        value={newMailName}
                        onChange={(e) => setNewMailName(e.target.value)}
                        placeholder="support"
                        className="flex-1 px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white"
                      />
                      <span className="font-mono text-zinc-500">@example.com</span>
                    </div>
                  </div>
                  <div>
                    <label className="text-zinc-400 block mb-1">Mailbox Password</label>
                    <input
                      type="text"
                      value={newMailPass}
                      onChange={(e) => setNewMailPass(e.target.value)}
                      className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white font-mono"
                    />
                  </div>
                </div>
                <div className="flex justify-end gap-2 pt-2">
                  <Button variant="outline" onClick={() => setShowMailModal(false)}>Cancel</Button>
                  <Button onClick={handleCreateMail}>Create Mailbox</Button>
                </div>
              </Card>
            </div>
          )}

          {/* Add DNS Record Modal */}
          {showDnsModal && (
            <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
              <Card className="max-w-md w-full p-6 space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-base font-bold text-white">Add DNS Zone Record</h3>
                  <button onClick={() => setShowDnsModal(false)} className="text-zinc-400 hover:text-white">&times;</button>
                </div>
                <div className="space-y-3 text-xs">
                  <div>
                    <label className="text-zinc-400 block mb-1">Record Type</label>
                    <select
                      value={newDnsType}
                      onChange={(e) => setNewDnsType(e.target.value)}
                      className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white font-mono"
                    >
                      <option value="A">A Record</option>
                      <option value="CNAME">CNAME Alias</option>
                      <option value="MX">MX Mail Exchange</option>
                      <option value="TXT">TXT Text Record</option>
                    </select>
                  </div>
                  <div>
                    <label className="text-zinc-400 block mb-1">Host / Name</label>
                    <input
                      type="text"
                      value={newDnsHost}
                      onChange={(e) => setNewDnsHost(e.target.value)}
                      placeholder="subdomain or @"
                      className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white font-mono"
                    />
                  </div>
                  <div>
                    <label className="text-zinc-400 block mb-1">Value / Target IP</label>
                    <input
                      type="text"
                      value={newDnsValue}
                      onChange={(e) => setNewDnsValue(e.target.value)}
                      placeholder="192.168.1.100 or target.com"
                      className="w-full px-3 py-2 bg-zinc-900 border border-zinc-800 rounded-lg text-xs text-white font-mono"
                    />
                  </div>
                </div>
                <div className="flex justify-end gap-2 pt-2">
                  <Button variant="outline" onClick={() => setShowDnsModal(false)}>Cancel</Button>
                  <Button onClick={handleAddDns}>Add Record</Button>
                </div>
              </Card>
            </div>
          )}
        </main>
      </div>
    </div>
  );
}
