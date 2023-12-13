<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>News List</title>
    <link rel="stylesheet" href="style.css">
    <style>
        div.news-item {
            opacity: 0;
            transition: opacity 1s ease-in-out;
            margin-bottom: 20px;
        }
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>

<?php
function formatDate($dateStr) {
    $date = new DateTime($dateStr);
    return $date->format('l, jS \of F Y g:i a');
}

$jsonUrl = "https://test.osky.dev/101/data.json";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $jsonUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Keep SSL verification for security
$jsonData = curl_exec($ch);

if ($jsonData === false) {
    echo "<p>Error: " . curl_error($ch) . "</p>";
    curl_close($ch);
    exit;
}

curl_close($ch);
$newsItems = json_decode($jsonData, true);

if (!is_array($newsItems)) {
    echo "<p>Error: Decoded JSON is not an array.</p>";
    exit;
}

usort($newsItems, function($a, $b) {
    return strcmp($a['title'], $b['title']);
});

echo '<div>';
foreach ($newsItems as $item) {
    echo '<div class="news-item">';
    echo '<h2>' . htmlspecialchars($item['title']) . '</h2>';
    echo '<p><i>' . formatDate($item['pubDate']) . '</i></p>';
    echo '<p>' . htmlspecialchars(strip_tags($item['description'])) . '</p>';

    if (is_array($item['link'])) {
        $links = array_filter($item['link'], function($url) {
            return filter_var($url, FILTER_VALIDATE_URL);
        });
        if (!empty($links)) {
            echo '<a href="' . htmlspecialchars($links[0]) . '" target="_blank">Read More</a>';
        }
    } else {
        echo '<a href="' . htmlspecialchars($item['link']) . '" target="_blank">Read More</a>';
    }

    echo '</div>';
}
echo '</div>';
?>

<script>
window.onload = function() {
    let newsItems = document.querySelectorAll('.news-item');
    let delay = 0;
    newsItems.forEach(function(item) {
        setTimeout(function() { item.style.opacity = 1; }, delay);
        delay += 500;
    });
};
</script>

</body>
</html>
