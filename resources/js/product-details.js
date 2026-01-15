// Product Details - Review Submission

document.addEventListener('DOMContentLoaded', function () {
    const reviewForm = document.getElementById('add-review-form');

    if (!reviewForm) return;

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
        submitBtn.textContent = 'Submitting...';

        const formData = new FormData(reviewForm);
        const data = {
            product_id: formData.get('product_id'),
            rating: formData.get('rating'),
            review_text: formData.get('review_text'),
        };

        try {
            const response = await fetch('/api/reviews', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                alert('Thank you for your review! Reloading page...');
                // Reload page to show the new review
                location.reload();
            } else {
                // Handle validation errors
                if (result.errors) {
                    const errorMessages = Object.values(result.errors).flat().join('\n');
                    alert('Validation errors:\n' + errorMessages);
                } else if (result.error) {
                    alert('Error: ' + result.error);
                } else {
                    alert('Failed to submit review. Please try again.');
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Network error. Please check your connection and try again.');
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });
});
