import re

with open('./scratch/panel3.php', 'r') as f:
    content = f.read()

# 1. Update the grid CSS to be 5-column, and only stack on mobile.
# I'll inject a style block before the grid container and assign a class.

style_block = """<style>
.quick-cards-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 12px;
}
@media (max-width: 768px) {
    .quick-cards-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<div class="quick-cards-grid">"""

# Replace the inline grid style with the class
content = content.replace('<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">', style_block)

# 2. Remove Firewall and IP Addresses cards
# We can just remove the HTML for them.

firewall_str = """        <!-- Card 11 -->
        <a href="/list/firewall/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-shield-halved" style="font-size: 18px; color: #ef4444;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">Firewall</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Iptables rules</span>
            </div>
        </a>"""

ip_str = """        <!-- Card 12 -->
        <a href="/list/ip/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 14px 16px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-map-location-dot" style="font-size: 18px; color: #d946ef;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 13px; letter-spacing: 0.5px;">IP Addresses</span>
                <span style="font-size: 10px; color: #94a3b8; margin-top: 2px;">Network config</span>
            </div>
        </a>"""

content = content.replace(firewall_str, '')
content = content.replace(ip_str, '')

with open('./scratch/panel3.php', 'w') as f:
    f.write(content)
