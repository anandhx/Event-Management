<?php
// Create placeholder images for EventPro
$images = [
    'hero-bg.jpg' => [1920, 1080, 'Event Planning Background'],
    'event-planning.jpg' => [800, 600, 'Event Planning Service'],
    'wedding-event.jpg' => [800, 600, 'Wedding Events'],
    'corporate-event.jpg' => [800, 600, 'Corporate Events'],
    'photography.jpg' => [800, 600, 'Photography Services'],
    'entertainment.jpg' => [800, 600, 'Entertainment Services'],
    'catering.jpg' => [800, 600, 'Catering Services'],
    'security.jpg' => [800, 600, 'Security Services'],
    'team-1.jpg' => [400, 400, 'Team Member 1'],
    'team-2.jpg' => [400, 400, 'Team Member 2'],
    'team-3.jpg' => [400, 400, 'Team Member 3'],
    'team-4.jpg' => [400, 400, 'Team Member 4'],
    'about-img.jpg' => [800, 600, 'About EventPro'],
    'testimonial-1.jpg' => [200, 200, 'Testimonial 1'],
    'testimonial-2.jpg' => [200, 200, 'Testimonial 2'],
    'testimonial-3.jpg' => [200, 200, 'Testimonial 3'],
    'testimonial-4.jpg' => [200, 200, 'Testimonial 4'],
    'blog-1.jpg' => [800, 400, 'Blog Post 1'],
    'blog-2.jpg' => [800, 400, 'Blog Post 2'],
    'blog-3.jpg' => [800, 400, 'Blog Post 3']
];

$targetDir = 'assets/img/';

// Create directory if it doesn't exist
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

foreach ($images as $filename => $details) {
    list($width, $height, $text) = $details;
    
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Define colors
    $primary = imagecolorallocate($image, 99, 102, 241); // Indigo
    $secondary = imagecolorallocate($image, 139, 92, 246); // Purple
    $accent = imagecolorallocate($image, 236, 72, 153); // Pink
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $gray = imagecolorallocate($image, 107, 114, 128);
    
    // Create gradient background
    for ($y = 0; $y < $height; $y++) {
        $ratio = $y / $height;
        $r = (int)(99 + (139 - 99) * $ratio);
        $g = (int)(102 + (92 - 102) * $ratio);
        $b = (int)(241 + (246 - 241) * $ratio);
        $color = imagecolorallocate($image, $r, $g, $b);
        
        for ($x = 0; $x < $width; $x++) {
            imagesetpixel($image, $x, $y, $color);
        }
    }
    
    // Add some decorative elements
    for ($i = 0; $i < 5; $i++) {
        $x = rand(0, $width);
        $y = rand(0, $height);
        $size = rand(20, 100);
        imagefilledellipse($image, $x, $y, $size, $size, $white);
    }
    
    // Add text
    $fontSize = min($width, $height) / 15;
    $fontFile = 'arial.ttf'; // Use default font
    
    // Try to use a system font
    $systemFonts = [
        'C:/Windows/Fonts/arial.ttf',
        'C:/Windows/Fonts/calibri.ttf',
        '/System/Library/Fonts/Arial.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf'
    ];
    
    $fontFile = null;
    foreach ($systemFonts as $font) {
        if (file_exists($font)) {
            $fontFile = $font;
            break;
        }
    }
    
    if ($fontFile) {
        // Calculate text position
        $bbox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth = $bbox[4] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[5];
        $x = ($width - $textWidth) / 2;
        $y = ($height + $textHeight) / 2;
        
        // Add text shadow
        imagettftext($image, $fontSize, 0, $x + 2, $y + 2, $black, $fontFile, $text);
        // Add main text
        imagettftext($image, $fontSize, 0, $x, $y, $white, $fontFile, $text);
    } else {
        // Fallback to basic text
        $x = ($width - strlen($text) * 10) / 2;
        $y = $height / 2;
        imagestring($image, 5, $x, $y, $text, $white);
    }
    
    // Save image
    $filepath = $targetDir . $filename;
    if (imagejpeg($image, $filepath, 90)) {
        echo "✓ Created $filename\n";
    } else {
        echo "✗ Failed to create $filename\n";
    }
    
    imagedestroy($image);
}

echo "\nAll placeholder images created successfully!\n";
?> 