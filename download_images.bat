@echo off
echo Downloading Event Images for EventPro...
echo.

REM Create assets/img directory if it doesn't exist
if not exist "assets\img" mkdir "assets\img"

echo Downloading images...

REM Download hero background
powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=1920&h=1080&fit=crop&crop=center' -OutFile 'assets\img\hero-bg.jpg'"
echo Downloaded hero-bg.jpg

REM Download service images
powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\event-planning.jpg'"
echo Downloaded event-planning.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\wedding-event.jpg'"
echo Downloaded wedding-event.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\corporate-event.jpg'"
echo Downloaded corporate-event.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\photography.jpg'"
echo Downloaded photography.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\entertainment.jpg'"
echo Downloaded entertainment.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1555939594-58d20cb2ad4f?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\catering.jpg'"
echo Downloaded catering.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\security.jpg'"
echo Downloaded security.jpg

REM Download team images
powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400&fit=crop&crop=face' -OutFile 'assets\img\team-1.jpg'"
echo Downloaded team-1.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face' -OutFile 'assets\img\team-2.jpg'"
echo Downloaded team-2.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face' -OutFile 'assets\img\team-3.jpg'"
echo Downloaded team-3.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face' -OutFile 'assets\img\team-4.jpg'"
echo Downloaded team-4.jpg

REM Download about image
powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=600&fit=crop&crop=center' -OutFile 'assets\img\about-img.jpg'"
echo Downloaded about-img.jpg

REM Download testimonial images
powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&h=200&fit=crop&crop=face' -OutFile 'assets\img\testimonial-1.jpg'"
echo Downloaded testimonial-1.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=200&h=200&fit=crop&crop=face' -OutFile 'assets\img\testimonial-2.jpg'"
echo Downloaded testimonial-2.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&h=200&fit=crop&crop=face' -OutFile 'assets\img\testimonial-3.jpg'"
echo Downloaded testimonial-3.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&h=200&fit=crop&crop=face' -OutFile 'assets\img\testimonial-4.jpg'"
echo Downloaded testimonial-4.jpg

REM Download blog images
powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=800&h=400&fit=crop&crop=center' -OutFile 'assets\img\blog-1.jpg'"
echo Downloaded blog-1.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1511578314322-379afb476865?w=800&h=400&fit=crop&crop=center' -OutFile 'assets\img\blog-2.jpg'"
echo Downloaded blog-2.jpg

powershell -Command "Invoke-WebRequest -Uri 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=800&h=400&fit=crop&crop=center' -OutFile 'assets\img\blog-3.jpg'"
echo Downloaded blog-3.jpg

echo.
echo All images downloaded successfully!
echo Images are now available in the assets\img folder.
pause 