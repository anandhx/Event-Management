<?php
// Script to download event-related images from Unsplash
$imageUrls = [
    'hero-bg.jpg' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1920&h=1080&fit=crop&crop=center',
    'event-planning.jpg' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=600&fit=crop&crop=center',
    'wedding-event.jpg' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=600&fit=crop&crop=center',
    'corporate-event.jpg' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=800&h=600&fit=crop&crop=center',
    'photography.jpg' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&h=600&fit=crop&crop=center',
    'entertainment.jpg' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=600&fit=crop&crop=center',
    'catering.jpg' => 'https://images.unsplash.com/photo-1555939594-58d20cb2ad4f?w=800&h=600&fit=crop&crop=center',
    'security.jpg' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop&crop=center',
    'team-1.jpg' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400&fit=crop&crop=face',
    'team-2.jpg' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face',
    'team-3.jpg' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
    'team-4.jpg' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
    'about-img.jpg' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=600&fit=crop&crop=center',
    'testimonial-1.jpg' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face',
    'testimonial-2.jpg' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&h=200&fit=crop&crop=face',
    'testimonial-3.jpg' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face',
    'testimonial-4.jpg' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face',
    'blog-1.jpg' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=400&fit=crop&crop=center',
    'blog-2.jpg' => 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=400&fit=crop&crop=center',
    'blog-3.jpg' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=400&fit=crop&crop=center'
];

$targetDir = 'assets/img/';

// Create directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

echo "Starting image download...\n";

foreach ($imageUrls as $filename => $url) {
    $filepath = $targetDir . $filename;
    
    echo "Downloading $filename...\n";
    
    // Set up cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $imageData !== false) {
        if (file_put_contents($filepath, $imageData)) {
            echo "✓ Successfully downloaded $filename\n";
        } else {
            echo "✗ Failed to save $filename\n";
        }
    } else {
        echo "✗ Failed to download $filename (HTTP Code: $httpCode)\n";
    }
    
    // Small delay to be respectful to the server
    usleep(500000); // 0.5 seconds
}

echo "\nImage download completed!\n";
?> 