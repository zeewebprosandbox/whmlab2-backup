<link rel="alternate icon" href="/images/favicon.png" type="image/png">
<link rel="icon" href="/images/logo.svg" type="image/svg+xml">
<link rel="stylesheet" href="/css/themes/default.min.css?<?= JS_LATEST_UPDATE ?>">

<?php
$selected_theme = !empty($_SESSION["userTheme"]) ? $_SESSION["userTheme"] : $_SESSION["THEME"];
// Load non-default theme
if ($selected_theme !== "default") {
	// Load HestiaCP-shipped themes (minified, updated/overwritten with updates) - ($HESTIA/web/css/themes/*.min.css)
	$non_default_theme_path = $_SERVER["HESTIA"] . "/web/css/themes/" . $selected_theme . ".min.css";
	if (file_exists($non_default_theme_path)) {
		echo '<link rel="stylesheet" href="/css/themes/' . $selected_theme . ".min.css?" . JS_LATEST_UPDATE . '">';
	}
	// Load custom theme files ($HESTIA/web/css/themes/custom/*.css)
	else {
		$custom_theme_path = $_SERVER["HESTIA"] . "/web/css/themes/custom/" . $selected_theme . ".min.css";
		if (file_exists($custom_theme_path)) {
			echo '<link rel="stylesheet" href="/css/themes/custom/' . $selected_theme . ".min.css?" . JS_LATEST_UPDATE . '">';
		} else {
			echo '<link rel="stylesheet" href="/css/themes/custom/' . $selected_theme . ".css?" . JS_LATEST_UPDATE . '">';
		}
	}
}

?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700&display=swap');
body, *:not(i):not([class*='fa']) { font-family: 'Barlow Condensed', sans-serif !important; }

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
</style>
