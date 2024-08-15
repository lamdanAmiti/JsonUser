<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

$jsonFile = 'apps_a.json';

function loadJsonData($file) {
    if (file_exists($file)) {
        $json = file_get_contents($file);
        return json_decode($json, true);
    } else {
        return ["apps" => []];
    }
}

function saveJsonData($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($file, $json);
}

$jsonData = loadJsonData($jsonFile);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["addApp"])) {
        // Add a new app
        $newApp = [
            "app_name" => $_POST["newAppName"],
            "app_icon_link" => $_POST["newAppIconLink"],
            "apk_link" => $_POST["newApkLink"],
            "app_version" => $_POST["newAppVersion"],
            "package_name" => $_POST["newPackageName"]
        ];

        $jsonData["apps"][] = $newApp;
        saveJsonData($jsonFile, $jsonData);
    } elseif (isset($_POST["removeApp"])) {
        // Remove an existing app
        $selectedIndex = $_POST["appSelector"];
        if (isset($jsonData["apps"][$selectedIndex])) {
            array_splice($jsonData["apps"], $selectedIndex, 1);
            saveJsonData($jsonFile, $jsonData);
        }
    } elseif (isset($_POST["updateApp"])) {
        // Update an existing app
        $selectedIndex = $_POST["appSelector"];
        if (isset($jsonData["apps"][$selectedIndex])) {
            $selectedApp = &$jsonData["apps"][$selectedIndex];
            $selectedApp["app_name"] = $_POST["appName"];
            $selectedApp["app_icon_link"] = $_POST["appIconLink"];
            $selectedApp["apk_link"] = $_POST["apkLink"];
            $selectedApp["app_version"] = $_POST["appVersion"];
            $selectedApp["package_name"] = $_POST["packageName"];
            saveJsonData($jsonFile, $jsonData);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="https://fiverr-res.cloudinary.com/image/upload/t_collaboration_hd,q_auto,f_auto/v1/secured-attachments/message/order_attachments/2187a4c5c387f8618249b9c6767fafe2-1696904539339/Exclusively%20for%20%283%29.png?__cld_token__=exp=1696928623~hmac=d6ff065f14ae00fb9b31982411bc0d0d7b35cc1bf79c37302ac406a9169a4d50" type="image/png">
  
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .header {
            background-image: url('https://fiverr-res.cloudinary.com/image/upload/t_collaboration_hd,q_auto,f_auto/v1/secured-attachments/message/order_attachments/2187a4c5c387f8618249b9c6767fafe2-1696904539339/Exclusively%20for%20%283%29.png?__cld_token__=exp=1696928623~hmac=d6ff065f14ae00fb9b31982411bc0d0d7b35cc1bf79c37302ac406a9169a4d50');
            background-size: 150px 150px; /* Set the desired dimensions */
            background-repeat: no-repeat;
            background-position: center;
            height: 170px; /* Adjust the height as needed */
            padding: 5px;
        }

        .dashboard-container {
            text-align: center;
            padding: 10px;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        select, input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        hr {
            border: 1px solid #ccc;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        pre {
            white-space: pre-wrap;
        }

        .app-icon-container {
            text-align: center;
        }

        .app-icon {
            max-width: 100px;
            max-height: 100px;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Empty container for the header image -->
    </div>
    <div class="dashboard-container">
        <h1>Kosher Pilot Admin</h1>
        <a href="logout.php">Logout</a>
    </div>

    <h1>App Editor</h1>
    <div class="container">
        <form method="post">
            <div class="app-icon-container">
                <img src="" alt="App Icon" class="app-icon" id="appIcon">
            </div>

            <label for="appSelector">Select an App:</label>
            <select name="appSelector" id="appSelector">
                <?php
                foreach ($jsonData["apps"] as $index => $app) {
                    echo "<option value=\"$index\">{$app["app_name"]}</option>";
                }
                ?>
            </select>

            <h2>Edit App Information</h2>
            <label for="appName">App Name:</label>
            <input type="text" name="appName" id="appName">
            <label for="appIconLink">App Icon Image Link:</label>
            <input type="text" name="appIconLink" id="appIconLink">
            <label for="apkLink">APK Link Source:</label>
            <input type="text" name="apkLink" id="apkLink">
            <label for="appVersion">App Version:</label>
            <input type="text" name="appVersion" id="appVersion">
            <label for="packageName">Package Name:</label>
            <input type="text" name="packageName" id="packageName">

            <br>
            <button type="submit" name="addApp">Add New App</button>
            <button type="submit" name="removeApp">Remove Selected App</button>
            <button type="submit" name="updateApp">Update App Information</button>
        </form>

        <hr>

        <h2>Current JSON Data:</h2>
        <pre><?php echo json_encode($jsonData, JSON_PRETTY_PRINT); ?></pre>
    </div>

    <script>
        var appSelector = document.getElementById("appSelector");
        var appName = document.getElementById("appName");
        var appIconLink = document.getElementById("appIconLink");
        var appIcon = document.getElementById("appIcon");
        var apkLink = document.getElementById("apkLink");
        var appVersion = document.getElementById("appVersion");
        var packageName = document.getElementById("packageName");

        appSelector.addEventListener("change", function() {
            var selectedIndex = appSelector.value;
            var selectedApp = <?php echo json_encode($jsonData["apps"]); ?>[selectedIndex];

            appName.value = selectedApp.app_name;
            appIconLink.value = selectedApp.app_icon_link;
            appIcon.src = selectedApp.app_icon_link;
            apkLink.value = selectedApp.apk_link;
            appVersion.value = selectedApp.app_version;
            packageName.value = selectedApp.package_name;
        });
    </script>
</body>
</html>
