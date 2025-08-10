<?php
session_start();
include('../includes/db.php');
 // Include your database connection file

$user_id = $_SESSION['user_id'];

// Dummy services data for showcase
// In real implementation, this would fetch from database
$services = [1, 2, 3, 4, 5]; // Dummy service IDs
?>

<?php include 'header.php'; ?>  

<div class="container my-5">
    <h1 class="text-center mb-4">Service Feedback</h1>

    <!-- Feedback Form -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0">We Value Your Feedback</h4>
        </div>
        <div class="card-body">
            <form action="submit_feedback.php" method="post">
                <div class="mb-3">
                    <label for="serviceID" class="form-label">Service ID</label>
                    <select class="form-select" id="serviceID" name="serviceID" required>
                        <option value="">Select Service ID</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo htmlspecialchars($service); ?>"><?php echo htmlspecialchars($service); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="rating" class="form-label">Rating</label>
                    <select class="form-select" id="rating" name="rating" required>
                        <option value="">Select Rating</option>
                        <option value="1">1 - Very Poor</option>
                        <option value="2">2 - Poor</option>
                        <option value="3">3 - Average</option>
                        <option value="4">4 - Good</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="comments" class="form-label">Comments</label>
                    <textarea class="form-control" id="comments" name="comments" rows="4" placeholder="Provide additional comments here..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        var rating = document.getElementById('rating').value;
        if (rating === "") {
            alert("Please select a rating.");
            event.preventDefault();
        }
        alert("Thank you for your feedback!");
    });
});
</script>
