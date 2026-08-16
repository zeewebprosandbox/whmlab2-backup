import re
import sys

with open('./scratch/panel.php', 'r') as f:
    content = f.read()

# 1. Insert Theme Toggle
theme_toggle_html = """
					<button id="theme-toggle" style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; width: 50px; height: 26px; position: relative; cursor: pointer; display: inline-flex; align-items: center; padding: 2px; margin-right: 15px; transition: 0.3s; vertical-align: middle;">
						<div id="theme-toggle-knob" style="background: #fff; border-radius: 50%; width: 20px; height: 20px; transition: 0.3s; display: flex; justify-content: center; align-items: center;">
							<i id="theme-icon" class="fas fa-sun" style="color: #f59e0b; font-size: 12px;"></i>
						</div>
					</button>
					<script>
						document.addEventListener("DOMContentLoaded", () => {
							const btn = document.getElementById('theme-toggle');
							const knob = document.getElementById('theme-toggle-knob');
							const icon = document.getElementById('theme-icon');
							let isDark = document.body.classList.contains('theme-dark') || localStorage.getItem('theme') === 'dark';
							
							function updateToggleUI() {
								if(isDark) {
									btn.style.background = '#3b82f6';
									btn.style.borderColor = '#3b82f6';
									knob.style.transform = 'translateX(24px)';
									icon.className = 'fas fa-moon';
									icon.style.color = '#1e293b';
									document.body.classList.add('theme-dark');
									document.body.classList.remove('theme-light');
								} else {
									btn.style.background = 'rgba(0,0,0,0.2)';
									btn.style.borderColor = 'rgba(255,255,255,0.1)';
									knob.style.transform = 'translateX(0px)';
									icon.className = 'fas fa-sun';
									icon.style.color = '#f59e0b';
									document.body.classList.add('theme-light');
									document.body.classList.remove('theme-dark');
								}
							}
							
							updateToggleUI();
							
							btn.addEventListener('click', () => {
								isDark = !isDark;
								localStorage.setItem('theme', isDark ? 'dark' : 'light');
								updateToggleUI();
							});
						});
					</script>
"""

content = content.replace('<div class="top-bar-usage">', '<div class="top-bar-usage">\n' + theme_toggle_html)

# 2. Insert the Grid
grid_html = """
<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
        <!-- Card 1 -->
        <a href="/list/web/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-globe" style="font-size: 22px; color: #3b82f6;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Web Domains</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Manage websites</span>
            </div>
        </a>
        <!-- Card 2 -->
        <a href="/list/dns/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-book-atlas" style="font-size: 22px; color: #10b981;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">DNS Records</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Zone management</span>
            </div>
        </a>
        <!-- Card 3 -->
        <a href="/list/mail/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-envelope" style="font-size: 22px; color: #f59e0b;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Mail Accounts</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Email & Webmail</span>
            </div>
        </a>
        <!-- Card 4 -->
        <a href="/list/db/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-database" style="font-size: 22px; color: #8b5cf6;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Databases</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">MySQL / PgSQL</span>
            </div>
        </a>
        
        <!-- Card 5 -->
        <a href="/list/cron/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-clock" style="font-size: 22px; color: #64748b;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Cron Jobs</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Scheduled tasks</span>
            </div>
        </a>
        <!-- Card 6 -->
        <a href="/list/backup/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-file-zipper" style="font-size: 22px; color: #0ea5e9;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Backups</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Restore & Archive</span>
            </div>
        </a>
        <!-- Card 7 -->
        <a href="/list/ssl/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-lock" style="font-size: 22px; color: #ef4444;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">SSL Certificates</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Let's Encrypt</span>
            </div>
        </a>
        <!-- Card 8 -->
        <a href="/list/apps/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-bolt" style="font-size: 22px; color: #eab308;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Quick Apps</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">WP, Laravel, etc</span>
            </div>
        </a>
        
        <!-- Card 9 -->
        <a href="/list/terminal/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-terminal" style="font-size: 22px; color: #a3e635;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Terminal</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">SSH Access</span>
            </div>
        </a>
        <!-- Card 10 -->
        <a href="/list/nodejs/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fab fa-node-js" style="font-size: 22px; color: #16a34a;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Node.js</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Manage Node apps</span>
            </div>
        </a>
        <!-- Card 11 -->
        <a href="/list/firewall/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-shield-halved" style="font-size: 22px; color: #ef4444;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">Firewall</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Iptables rules</span>
            </div>
        </a>
        <!-- Card 12 -->
        <a href="/list/ip/" style="background: linear-gradient(145deg, #1f242d, #272c36); border: 1px solid rgba(255,255,255,0.05); color: #fff; padding: 20px 24px; border-radius: 12px; text-decoration: none; display: flex; flex-direction: row; align-items: center; gap: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='rgba(255,255,255,0.2)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.05)';">
            <div style="background: rgba(255,255,255,0.05); border-radius: 10px; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fas fa-map-location-dot" style="font-size: 22px; color: #d946ef;"></i>
            </div>
            <div style="display: flex; flex-direction: column;">
                <span style="font-weight: 600; font-size: 15px; letter-spacing: 0.5px;">IP Addresses</span>
                <span style="font-size: 12px; color: #94a3b8; margin-top: 2px;">Network config</span>
            </div>
        </a>
    </div>
</div>
"""

# Only inject if not already present
if 'grid-template-columns: repeat(4, 1fr)' not in content:
    content = content.replace('</header>', '</header>\n' + grid_html)

with open('./scratch/panel.php', 'w') as f:
    f.write(content)
