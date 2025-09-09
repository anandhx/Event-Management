<?php include 'header.php'; ?>
<?php
$error = '';
$success = '';
if (isset($_SESSION['error_message'])) { $error = $_SESSION['error_message']; unset($_SESSION['error_message']); }
if (isset($_SESSION['success_message'])) { $success = $_SESSION['success_message']; unset($_SESSION['success_message']); }
?>

        <!-- Contact Start -->
        <div class="container-fluid py-5">
            <div class="container py-5">
                <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
                    <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">Get In Touch</h5>
                    <h1 class="display-5 w-50 mx-auto">Contact for any query</h1>
                </div>
                <div class="row g-5 mb-5">
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="h-100">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3001583.639214438!2d-78.4099249913019!3d42.71993723844549!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4ccc4bf0f123a5a9%3A0xddcfc6c1de189567!2sNew%20York%2C%20USA!5e0!3m2!1sen!2sbd!4v1687175686342!5m2!1sen!2sbd" class="border-0 rounded w-100 h-100" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="col-lg-6 wow fadeInUp" data-wow-delay=".5s">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>
                        <div class="rounded contact-form">
                            <form method="post" action="submit_contact.php">
                                <div class="mb-4">
                                    <input type="text" class="form-control p-3" name="name" placeholder="Your Name" value="<?php echo isset($_SESSION['old_contact']['name']) ? htmlspecialchars($_SESSION['old_contact']['name']) : ''; ?>" required>
                                </div>
                                <div class="mb-4">
                                    <input type="email" class="form-control p-3" name="email" placeholder="Your Email" value="<?php echo isset($_SESSION['old_contact']['email']) ? htmlspecialchars($_SESSION['old_contact']['email']) : ''; ?>" required>
                                </div>
                                <div class="mb-4">
                                    <input type="text" class="form-control p-3" name="subject" placeholder="Subject" value="<?php echo isset($_SESSION['old_contact']['subject']) ? htmlspecialchars($_SESSION['old_contact']['subject']) : ''; ?>" required>
                                </div>
                                <div class="mb-4">
                                    <textarea class="w-100 form-control p-3" rows="6" cols="10" name="message" placeholder="Message" required><?php echo isset($_SESSION['old_contact']['message']) ? htmlspecialchars($_SESSION['old_contact']['message']) : ''; ?></textarea>
                                </div>
                                <button class="btn btn-primary border-0 py-3 px-4 rounded-pill" type="submit">Send Message</button>
                            </form>
                        </div>
                        <?php unset($_SESSION['old_contact']); ?>
                    </div>
                </div>
                <div class="row g-4 wow fadeInUp" data-wow-delay=".3s">
                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="d-flex bg-light p-3 rounded contact-btn-link">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                                <i class="fa fa-share text-dark"></i>
                            </div>
                            <div class="ms-3 contact-link">
                                <h4 class="text-dark">Follow Us</h4>
                                <div class="d-flex justify-content-center">
                                    <a class="pe-2" href="#"><i class="fab fa-facebook-f text-dark"></i></a>
                                    <a class="px-2" href="#"><i class="fab fa-twitter text-dark"></i></a>
                                    <a class="px-2" href="#"><i class="fab fa-instagram text-dark"></i></a>
                                    <a class="px-2" href="#"><i class="fab fa-linkedin-in text-dark"></i></a>
                                    <a class="px-2" href="#"><i class="fab fa-youtube text-dark"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="d-flex bg-light p-3 rounded contact-btn-link">
                            <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                                <i class="fas fa-map-marker-alt text-dark"></i>
                            </div>
                            <div class="ms-3 contact-link">
                                <h4 class="text-dark">Address</h4>
                                <a href="#">
                                    <h5 class="text-dark d-inline fs-6">Stored in settings</h5>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="d-flex bg-light p-3 rounded contact-btn-link">
                            <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                                <i class="fa fa-phone text-dark"></i>
                            </div>
                            <div class="ms-3 contact-link">
                                <h4 class="text-dark">Call Us</h4>
                                <a class="h5 text-dark fs-6" href="tel:+0123456789">+012 3456 7890</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12">
                        <div class="d-flex bg-light p-3 rounded contact-btn-link">
                            <div class="d-flex align-items-center justify-content-center bg-primary rounded-circle p-3 ms-3" style="width: 64px; height: 64px;">
                                <i class="fa fa-envelope text-dark"></i>
                            </div>
                            <div class="ms-3 contact-link">
                                <h4 class="text-dark">Email Us</h4>
                                <a class="h5 text-dark fs-6" href="#">support@ems.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Contact End -->

        <?php include 'footer.php'; ?>