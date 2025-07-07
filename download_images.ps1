# PowerShell script to download event images for EventPro

Write-Host "Downloading Event Images for EventPro..." -ForegroundColor Green

# Create assets/img directory if it doesn't exist
if (!(Test-Path "assets\img")) {
    New-Item -ItemType Directory -Path "assets\img" -Force
    Write-Host "Created assets\img directory" -ForegroundColor Yellow
}

# Define image URLs
$images = @{
    "hero-bg.jpg" = "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1920&h=1080&fit=crop&crop=center"
    "event-planning.jpg" = "https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=600&fit=crop&crop=center"
    "wedding-event.jpg" = "https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=600&fit=crop&crop=center"
    "corporate-event.jpg" = "https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=800&h=600&fit=crop&crop=center"
    "photography.jpg" = "https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&h=600&fit=crop&crop=center"
    "entertainment.jpg" = "https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=600&fit=crop&crop=center"
    "catering.jpg" = "https://images.unsplash.com/photo-1555939594-58d20cb2ad4f?w=800&h=600&fit=crop&crop=center"
    "security.jpg" = "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop&crop=center"
    "team-1.jpg" = "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400&fit=crop&crop=face"
    "team-2.jpg" = "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face"
    "team-3.jpg" = "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face"
    "team-4.jpg" = "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face"
    "about-img.jpg" = "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=600&fit=crop&crop=center"
    "testimonial-1.jpg" = "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face"
    "testimonial-2.jpg" = "https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&h=200&fit=crop&crop=face"
    "testimonial-3.jpg" = "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face"
    "testimonial-4.jpg" = "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face"
    "blog-1.jpg" = "https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=400&fit=crop&crop=center"
    "blog-2.jpg" = "https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=400&fit=crop&crop=center"
    "blog-3.jpg" = "https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=400&fit=crop&crop=center"
}

# Download each image
foreach ($image in $images.GetEnumerator()) {
    $filename = $image.Key
    $url = $image.Value
    $filepath = "assets\img\$filename"
    
    Write-Host "Downloading $filename..." -ForegroundColor Cyan
    
    try {
        Invoke-WebRequest -Uri $url -OutFile $filepath -UserAgent "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"
        Write-Host "✓ Successfully downloaded $filename" -ForegroundColor Green
    }
    catch {
        Write-Host "✗ Failed to download $filename : $($_.Exception.Message)" -ForegroundColor Red
    }
    
    # Small delay between downloads
    Start-Sleep -Milliseconds 500
}

Write-Host "`nAll images downloaded successfully!" -ForegroundColor Green
Write-Host "Images are now available in the assets\img folder." -ForegroundColor Yellow 