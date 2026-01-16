// Product Details - Review Submission

document.addEventListener('DOMContentLoaded', function () {
    const reviewForm = document.getElementById('add-review-form');

    if (!reviewForm) return;

    let editingReviewId = null; // Track if we're editing a review

    // Star rating functionality - VANILLA JS, NO VUE!
    const starInputs = document.querySelectorAll('.star-rating-input input[type="radio"]');
    const starLabels = document.querySelectorAll('.star-rating-input label');

    // Update star display based on selection
    function updateStars(rating) {
        starLabels.forEach((label, index) => {
            // Stars are now in order 1,2,3,4,5 (index 0,1,2,3,4)
            const starValue = index + 1;
            if (starValue <= rating) {
                label.classList.add('filled');
            } else {
                label.classList.remove('filled');
            }
        });
    }

    // Handle star clicks
    starLabels.forEach((label) => {
        label.addEventListener('click', function() {
            const input = document.getElementById(label.getAttribute('for'));
            if (input) {
                input.checked = true;
                updateStars(parseInt(input.value));
            }
        });

        // Hover preview
        label.addEventListener('mouseenter', function() {
            const input = document.getElementById(label.getAttribute('for'));
            if (input) {
                updateStars(parseInt(input.value));
            }
        });
    });

    // Restore selected rating on mouse leave
    document.querySelector('.star-rating-input').addEventListener('mouseleave', function() {
        const checkedInput = document.querySelector('.star-rating-input input[type="radio"]:checked');
        if (checkedInput) {
            updateStars(parseInt(checkedInput.value));
        } else {
            updateStars(0);
        }
    });

    reviewForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = reviewForm.querySelector('.submit-review-btn');
        const originalBtnText = submitBtn.textContent;

        // Disable button during submission
        submitBtn.disabled = true;
        submitBtn.textContent = editingReviewId ? 'Updating...' : 'Submitting...';

        const formData = new FormData(reviewForm);
        const data = {
            product_id: formData.get('product_id'),
            rating: formData.get('rating'),
            review_text: formData.get('review_text'),
        };

        try {
            let response;

            if (editingReviewId) {
                // Update existing review
                response = await fetch(`/api/reviews/${editingReviewId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        rating: data.rating,
                        review_text: data.review_text
                    })
                });
            } else {
                // Create new review
                response = await fetch('/api/reviews', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
            }

            const result = await response.json();

            if (response.ok) {
                location.reload();
            } else {
                // Handle validation errors
                if (result.errors) {
                    console.error('Validation errors:', result.errors);
                } else if (result.error) {
                    console.error('Error:', result.error);
                } else {
                    console.error('Failed to submit review');
                }
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });

    // Handle delete review buttons
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.delete-review-btn')) {
            const btn = e.target.closest('.delete-review-btn');
            const reviewId = btn.dataset.reviewId;

            if (!confirm('Are you sure you want to delete this review?')) {
                return;
            }

            try {
                const response = await fetch(`/api/reviews/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    location.reload();
                } else {
                    console.error('Error:', result.error || 'Failed to delete review');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Handle edit review buttons
        if (e.target.closest('.edit-review-btn')) {
            const btn = e.target.closest('.edit-review-btn');
            const reviewId = btn.dataset.reviewId;
            const reviewCard = document.querySelector(`[data-review-id="${reviewId}"]`);
            const reviewTextDiv = reviewCard.querySelector('.review-text');
            const currentText = reviewTextDiv.dataset.reviewText;
            const currentRating = parseInt(reviewTextDiv.dataset.reviewRating);

            // Set editing mode
            editingReviewId = reviewId;

            // Populate the form with current review data
            reviewForm.querySelector('#review_text').value = currentText;

            // Set the rating
            const ratingInput = reviewForm.querySelector(`input[name="rating"][value="${currentRating}"]`);
            if (ratingInput) {
                ratingInput.checked = true;
                updateStars(currentRating);
            }

            // Update form heading and button text
            const formHeading = document.querySelector('.add-review-section h2');
            const submitBtn = reviewForm.querySelector('.submit-review-btn');
            formHeading.textContent = 'Edit Your Review';
            submitBtn.textContent = 'Update Review';

            // Add cancel button if it doesn't exist
            let cancelBtn = reviewForm.querySelector('.cancel-edit-review-btn');
            if (!cancelBtn) {
                cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.className = 'cancel-edit-review-btn';
                cancelBtn.textContent = 'Cancel';
                submitBtn.parentNode.insertBefore(cancelBtn, submitBtn.nextSibling);
            }

            // Scroll to form
            document.querySelector('.add-review-section').scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Cancel edit handler
            cancelBtn.addEventListener('click', function() {
                // Reset form
                editingReviewId = null;
                reviewForm.reset();
                updateStars(0);
                formHeading.textContent = 'Write a Review';
                submitBtn.textContent = 'Submit Review';
                cancelBtn.remove();
            });
        }
    });
});
