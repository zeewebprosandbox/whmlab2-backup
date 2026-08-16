const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8002/whmpanel/api/v1";

export interface ServerStats {
  nodeName: string;
  status: string;
  cpuPercent: number;
  memoryPercent: number;
  diskPercent: number;
  bandwidthPercent: number;
  activeAccounts: number;
}

export interface DnsRecord {
  type: string;
  host: string;
  value: string;
  ttl: number;
}

export const apiClient = {
  async fetchWithAuth(endpoint: string, options: RequestInit = {}) {
    const token = typeof window !== "undefined" ? localStorage.getItem("whm_auth_token") : null;
    const headers = {
      "Content-Type": "application/json",
      Accept: "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options.headers,
    };

    try {
      const res = await fetch(`${API_BASE}${endpoint}`, { ...options, headers });
      if (!res.ok) {
        throw new Error(`API Error ${res.status}: ${res.statusText}`);
      }
      return await res.json();
    } catch (err) {
      console.warn(`[API Fallback] ${endpoint}:`, err);
      return null;
    }
  },

  async getServerStats(): Promise<ServerStats> {
    const data = await this.fetchWithAuth("/server/stats");
    return (
      data || {
        nodeName: "US-East-Node-01",
        status: "online",
        cpuPercent: 32,
        memoryPercent: 68,
        diskPercent: 41,
        bandwidthPercent: 18,
        activeAccounts: 3,
      }
    );
  },

  async getDnsRecords(domain: string): Promise<DnsRecord[]> {
    const data = await this.fetchWithAuth(`/dns/zones/${domain}/records`);
    return (
      data?.records || [
        { type: "A", host: "@", value: "192.168.1.100", ttl: 14400 },
        { type: "CNAME", host: "www", value: domain, ttl: 14400 },
        { type: "MX", host: "@", value: `mail.${domain}`, ttl: 14400 },
        { type: "TXT", host: "@", value: "v=spf1 a mx ~all", shadow: true, ttl: 14400 },
      ]
    );
  },

  async searchDomainAvailability(domain: string) {
    // Domain search simulation
    const tld = domain.split(".").pop() || "com";
    const available = !domain.includes("taken");
    const prices: Record<string, string> = {
      com: "$9.99/yr",
      io: "$29.99/yr",
      net: "$12.99/yr",
      org: "$14.99/yr",
      dev: "$19.99/yr",
    };

    return {
      domain,
      available,
      price: prices[tld] || "$14.99/yr",
      tld: `.${tld}`,
    };
  },
};
