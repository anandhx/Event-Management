    <!-- Footer Start -->
    <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay=".3s">
        <div class="container py-5">
            <div class="row g-4 footer-inner">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-white fw-bold mb-4">About Us</h4>
                        <p>At EventPro, we provide comprehensive event management solutions to ensure your events are always memorable and successful. Our expert team delivers unparalleled support and planning for a seamless experience.</p>
                        <p class="mb-0"><a class="" href="#">EventPro</a> &copy; 2024 All Rights Reserved.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-white fw-bold mb-4">Useful Links</h4>
                        <div class="d-flex flex-column align-items-start">
                            <a class="btn btn-link ps-0" href="about.php"><i class="fa fa-check me-2"></i>About Us</a>
                            <a class="btn btn-link ps-0" href="contact.php"><i class="fa fa-check me-2"></i>Contact Us</a>
                            <a class="btn btn-link ps-0" href="service.php"><i class="fa fa-check me-2"></i>Our Services</a>
                            <a class="btn btn-link ps-0" href="faq.php"><i class="fa fa-check me-2"></i>FAQs</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-white fw-bold mb-4">Our Services</h4>
                        <div class="d-flex flex-column align-items-start">
                            <a class="btn btn-link ps-0" href=""><i class="fa fa-check me-2"></i>Event Planning</a>
                            <a class="btn btn-link ps-0" href=""><i class="fa fa-check me-2"></i>Event Coordination</a>
                            <a class="btn btn-link ps-0" href=""><i class="fa fa-check me-2"></i>24/7 Support</a>
                            <a class="btn btn-link ps-0" href=""><i class="fa fa-check me-2"></i>Custom Solutions</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-item">
                        <h4 class="text-white fw-bold mb-4">Contact Us</h4>
                        <a href="mailto:info@example.com" class="btn btn-link w-100 text-start ps-0 pb-3 border-bottom rounded-0"><i class="fa fa-map-marker-alt me-3"></i>123 Event Street, CA, USA</a>
                        <a href="tel:+01234567890" class="btn btn-link w-100 text-start ps-0 py-3 border-bottom rounded-0"><i class="fa fa-phone-alt me-3"></i>+012 345 67890</a>
                        <a href="mailto:info@example.com" class="btn btn-link w-100 text-start ps-0 py-3 border-bottom rounded-0"><i class="fa fa-envelope me-3"></i>info@eventpro.com</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Copyright Start -->
    <div class="container-fluid copyright bg-dark py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                    <a href="#" class="text-primary mb-0 display-6">EventPro<span class="text-white"></span></a>
                </div>
                <div class="col-md-4 copyright-btn text-center text-md-start mb-3 mb-md-0 flex-shrink-0">
                    <a class="btn btn-primary rounded-circle me-3 copyright-icon" href=""><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-primary rounded-circle me-3 copyright-icon" href=""><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-primary rounded-circle me-3 copyright-icon" href=""><i class="fab fa-youtube"></i></a>
                    <a class="btn btn-primary rounded-circle me-3 copyright-icon" href=""><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Copyright End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary rounded-circle border-3 back-to-top"><i class="fa fa-arrow-up"></i></a>


    <!-- Template Javascript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/wow/wow.min.js"></script>
    <script src="../assets/lib/easing/easing.min.js"></script>
    <script src="../assets/lib/waypoints/waypoints.min.js"></script>
    <script src="../assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
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
<!-- issue in communition when , uncomment this line   -->
<!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> -->

<!-- Include Bootstrap JS (before the closing </body> tag) -->


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

<!-- CSS for fade-out effect -->
<style>
    .fade-out {
        opacity: 0;
        transition: opacity 1s ease-out; /* 1 second for fade-out */
    }
</style>


</body>
</html>