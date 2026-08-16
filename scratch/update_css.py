import re

with open('./scratch/css.php', 'r') as f:
    content = f.read()

switch_css = """
/* Global Toggle Switches */
input[type="checkbox"] {
    appearance: none;
    -webkit-appearance: none;
    width: 36px;
    height: 20px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    position: relative;
    cursor: pointer;
    outline: none;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    vertical-align: middle;
    margin: 0;
}
.theme-light input[type="checkbox"] {
    background: rgba(0, 0, 0, 0.2);
    border: 1px solid rgba(0, 0, 0, 0.1);
}
input[type="checkbox"]::after {
    content: '';
    position: absolute;
    top: 1px;
    left: 2px;
    width: 16px;
    height: 16px;
    background: #fff;
    border-radius: 50%;
    transition: transform 0.3s ease;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}
input[type="checkbox"]:checked {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
}
input[type="checkbox"]:checked::after {
    transform: translateX(14px);
}
input[type="checkbox"]:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
"""

content = content.replace("</style>", switch_css + "</style>")

with open('./scratch/css.php', 'w') as f:
    f.write(content)
