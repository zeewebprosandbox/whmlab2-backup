import re

with open('./scratch/panel2.php', 'r') as f:
    content = f.read()

# Make grid responsive and smaller
content = content.replace('grid-template-columns: repeat(4, 1fr); gap: 20px;', 'grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;')
content = content.replace('margin-top: 30px; margin-bottom: 30px;', 'margin-top: 20px; margin-bottom: 20px;')

# Resize card padding and gap
content = content.replace('padding: 20px 24px;', 'padding: 14px 16px;')
content = content.replace('gap: 16px;', 'gap: 12px;')

# Resize icon container
content = content.replace('width: 44px; height: 44px;', 'width: 36px; height: 36px;')
# Resize icon
content = content.replace('font-size: 22px;', 'font-size: 18px;')

# Resize text
content = content.replace('font-size: 15px;', 'font-size: 13px;')
content = content.replace('font-size: 12px;', 'font-size: 10px;')

with open('./scratch/panel2.php', 'w') as f:
    f.write(content)
