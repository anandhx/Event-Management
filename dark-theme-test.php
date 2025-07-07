<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dark Theme Test - EventPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="assets/css/custom-style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h1 class="section-title">Dark Theme Test</h1>
                <p class="section-subtitle">Testing all components with the new dark theme</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="custom-card">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h4 class="text-primary mb-3">Event Planning</h4>
                    <p class="text-secondary">Professional event planning services with attention to detail.</p>
                    <a href="#" class="btn-custom btn-primary-custom">Learn More</a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="custom-card">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="text-primary mb-3">Team Coordination</h4>
                    <p class="text-secondary">Expert coordination of all event team members and vendors.</p>
                    <a href="#" class="btn-custom btn-secondary-custom">Learn More</a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="custom-card">
                    <div class="card-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4 class="text-primary mb-3">Premium Services</h4>
                    <p class="text-secondary">High-quality services that exceed expectations.</p>
                    <a href="#" class="btn-custom btn-accent-custom">Learn More</a>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <div class="custom-form">
                    <h3 class="text-primary mb-4">Test Form</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control-custom w-100" placeholder="Your Name">
                        </div>
                        <div class="col-md-6">
                            <input type="email" class="form-control-custom w-100" placeholder="Your Email">
                        </div>
                        <div class="col-12">
                            <textarea class="form-control-custom w-100" rows="4" placeholder="Your Message"></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn-custom btn-primary-custom">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5">
            <div class="col-12">
                <div class="dashboard-card">
                    <h3 class="text-primary mb-4">Dashboard Card Test</h3>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number">150</div>
                                <div class="stat-label">Events Planned</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number">98%</div>
                                <div class="stat-label">Success Rate</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number">500+</div>
                                <div class="stat-label">Happy Clients</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to cards
            const cards = document.querySelectorAll('.custom-card, .stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html> 