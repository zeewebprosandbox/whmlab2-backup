import os

# 1. Create the new theme.php endpoint locally
api_endpoint_content = """<?php
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_theme = $_POST['theme'] === 'dark' ? 'dark' : 'default';
    
    // Validate token if needed (optional for pure aesthetic toggle, but good practice)
    if (isset($_POST["token"]) && $_SESSION["token"] == $_POST["token"]) {
        // Run v-change-user-config-value
        exec(HESTIA_CMD . "v-change-user-config-value " . escapeshellarg($user) . " THEME " . escapeshellarg($new_theme));
        $_SESSION["userTheme"] = $new_theme;
        echo json_encode(["status" => "success", "theme" => $new_theme]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid token"]);
    }
}
?>"""

with open('./scratch/theme.php', 'w') as f:
    f.write(api_endpoint_content)

# 2. Update panel3.php JavaScript
with open('./scratch/panel3.php', 'r') as f:
    panel_content = f.read()

old_js = """    function toggleTheme() {
        const isDark = document.body.classList.toggle('theme-dark');
        document.body.classList.toggle('theme-light', !isDark);
        localStorage.setItem('zodTheme', isDark ? 'dark' : 'light');
        const icon = document.getElementById('theme-icon');
        icon.className = isDark ? 'fas fa-sun text-yellow-400' : 'fas fa-moon text-slate-400';
    }"""

new_js = """    function toggleTheme() {
        const isDark = document.body.classList.contains('theme-dark');
        const newTheme = isDark ? 'default' : 'dark';
        
        const icon = document.getElementById('theme-icon');
        icon.className = 'fas fa-spinner fa-spin text-slate-400';
        
        fetch('/api/theme.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'theme=' + newTheme + '&token=<?= $_SESSION["token"] ?>'
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  window.location.reload();
              } else {
                  alert('Theme change failed');
              }
          });
    }"""

# Replace the JS
panel_content = panel_content.replace(old_js, new_js)

# I should also fix the initial load state so the icon reflects the native theme, not localStorage.
old_init_js = """    // Initialize theme
    const savedTheme = localStorage.getItem('zodTheme') || 'dark';
    if (savedTheme === 'light') {
        document.body.classList.remove('theme-dark');
        document.body.classList.add('theme-light');
        document.getElementById('theme-icon').className = 'fas fa-moon text-slate-400';
    }"""

new_init_js = """    // Initialize theme from native session
    const currentTheme = '<?= !empty($_SESSION["userTheme"]) ? $_SESSION["userTheme"] : $_SESSION["THEME"] ?>';
    if (currentTheme !== 'dark') {
        document.body.classList.remove('theme-dark');
        document.body.classList.add('theme-light');
        document.getElementById('theme-icon').className = 'fas fa-moon text-slate-400';
    }"""

panel_content = panel_content.replace(old_init_js, new_init_js)

with open('./scratch/panel3.php', 'w') as f:
    f.write(panel_content)
