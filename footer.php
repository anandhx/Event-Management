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






</script>



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
