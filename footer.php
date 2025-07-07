    <!-- Footer Start -->
    <footer class="custom-footer">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4 col-md-6">
                    <div class="mb-4">
                        <h4 class="footer-title">EventPro</h4>
                        <p class="text-secondary">Creating unforgettable events with professional planning and execution. Your vision, our expertise.</p>
                    </div>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn-custom btn-primary-custom rounded-circle p-2">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="btn-custom btn-secondary-custom rounded-circle p-2">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="btn-custom btn-accent-custom rounded-circle p-2">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="btn-custom btn-primary-custom rounded-circle p-2">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="text-primary mb-4">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">About Us</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Services</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Projects</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Contact</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Blog</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-primary mb-4">Our Services</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="footer-link">Event Planning</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Wedding Events</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Corporate Events</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Photography</a></li>
                        <li class="mb-2"><a href="#" class="footer-link">Catering</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-primary mb-4">Newsletter</h5>
                    <p class="text-secondary mb-3">Subscribe for updates and exclusive offers</p>
                    <div class="position-relative">
                        <input type="email" class="form-control-custom" placeholder="Your email address">
                        <button class="btn-custom btn-primary-custom position-absolute top-0 end-0 h-100">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <hr class="my-5" style="border-color: rgba(255,255,255,0.1);">
            
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="text-secondary mb-0">&copy; 2024 EventPro. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3">
                        <a href="#" class="footer-link">Privacy Policy</a>
                        <a href="#" class="footer-link">Terms of Service</a>
                        <a href="#" class="footer-link">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn-custom btn-primary-custom rounded-circle position-fixed bottom-0 end-0 m-4" style="width: 50px; height: 50px; z-index: 1000;">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/lib/wow/wow.min.js"></script>
    <script src="assets/lib/easing/easing.min.js"></script>
    <script src="assets/lib/waypoints/waypoints.min.js"></script>
    <script src="assets/lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/custom.js"></script>

    

<script>



function toggleTab(tab) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    if (tab === 'login') {
        document.getElementById('loginTab').classList.add('active');
        document.getElementById('signupTab').classList.remove('active');

        // Add fade-out effect to the signup form
        signupForm.classList.remove('show');
        signupForm.classList.add('fade');
        
        // Delay the display of the login form to allow the fade-out to complete
        setTimeout(() => {
            loginForm.style.display = 'block';
            signupForm.style.display = 'none';
            loginForm.classList.add('show');
            loginForm.classList.remove('fade');
        }, 500); // 500 ms to match the CSS transition duration
    } else {
        document.getElementById('loginTab').classList.remove('active');
        document.getElementById('signupTab').classList.add('active');

        // Add fade-out effect to the login form
        loginForm.classList.remove('show');
        loginForm.classList.add('fade');
        
        // Delay the display of the signup form to allow the fade-out to complete
        setTimeout(() => {
            signupForm.style.display = 'block';
            loginForm.style.display = 'none';
            signupForm.classList.add('show');
            signupForm.classList.remove('fade');
        }, 500); // 500 ms to match the CSS transition duration
    }
}


</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        // Show the modal if there's a message
        if ($("#modalMessage").text().trim() !== "") {
            $("#alertModal").modal('show');

            // Automatically fade out after 5 seconds
            setTimeout(function() {
                $("#alertModal").modal('hide');
            }, 2000);
        }
    });
</script>

<script>
    function toggleTab(tab) {
        if (tab === 'login') {
            document.getElementById('loginTab').classList.add('active');
            document.getElementById('signupTab').classList.remove('active');
            document.getElementById('loginForm').style.display = 'block';
            document.getElementById('signupForm').style.display = 'none';
        } else {
            document.getElementById('loginTab').classList.remove('active');
            document.getElementById('signupTab').classList.add('active');
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('signupForm').style.display = 'block';
        }
    }

    // Apply blur effect to background when modal is shown
    var modal = document.getElementById('authModal');
    modal.addEventListener('show.bs.modal', function () {
        document.querySelector('.custom-modal-bg').classList.add('blurred-bg');
    });

    modal.addEventListener('hide.bs.modal', function () {
        document.querySelector('.custom-modal-bg').classList.remove('blurred-bg');
    });

    function validateLoginForm() {
        var username = document.getElementById('loginUsername').value.trim();
        var password = document.getElementById('loginPassword').value.trim();
        var usernamePattern = /^[a-zA-Z0-9_]{3,20}$/;
        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,20}$/;

        if (!usernamePattern.test(username)) {
            alert("Username must be 3-20 characters long and can only contain letters, numbers, and underscores.");
            return false;
        }

        if (!passwordPattern.test(password)) {
            alert("Password must be 8-20 characters long, include at least one uppercase letter, one lowercase letter, and one number.");
            return false;
        }

        return true;
    }

    function validateSignupForm() {
        var username = document.getElementById('signupUsername').value.trim();
        var password = document.getElementById('signupPassword').value.trim();
        var confirmPassword = document.getElementById('signupConfirmPassword').value.trim();
        var email = document.getElementById('signupEmail').value.trim();
        
        var usernamePattern = /^[a-zA-Z]{3,20}$/; // Only alphabets allowed
        
        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,20}$/;
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!usernamePattern.test(username)) {
            alert("Username must be 3-20 characters long and can only contain letters");
            return false;
        }

        if (!passwordPattern.test(password)) {
            alert("Password must be 8-20 characters long, include at least one uppercase letter, one lowercase letter, and one number.");
            return false;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }

        if (!emailPattern.test(email)) {
            alert("Please enter a valid email address.");
            return false;
        }

        return true;
    }
</script>

<script>
    window.onload = function() {
        var messageBox = document.getElementById('message-box');
        if (messageBox) {
            setTimeout(function() {
                messageBox.classList.add('fade-out');
            }, 3000); // Adjust the time as needed
        }
    };
</script>

<!-- Include Bootstrap JS (before the closing </body> tag) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- JavaScript for fading out the message -->
<script>
    window.onload = function() {
        var messageBox = document.getElementById('message-box');
        if (messageBox) {
            setTimeout(function() {
                messageBox.querySelectorAll('.alert').forEach(function(alert) {
                    alert.classList.add('fade-out');
                });
            }, 5000); // 5 seconds delay
        }
    };
</script>



</body>
</html>
