<?php
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
?>