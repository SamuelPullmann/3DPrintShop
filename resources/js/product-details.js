// Product Details - Review Submission

document.addEventListener('DOMContentLoaded', function () {
    const reviewForm = document.getElementById('add-review-form');

    if (!reviewForm) return;

    let editingReviewId = null;

    // Star rating functionality
    const starLabels = document.querySelectorAll('.star-rating-input label');

    function updateStars(rating) {
        starLabels.forEach((label, index) => {
            const starValue = index + 1;
            if (starValue <= rating) {
                label.classList.add('filled');
            } else {
                label.classList.remove('filled');
            }
        });
    }

    starLabels.forEach((label) => {
        label.addEventListener('click', function() {
            const input = document.getElementById(label.getAttribute('for'));
            if (input) {
                input.checked = true;
                updateStars(parseInt(input.value));
            }
        });

        label.addEventListener('mouseenter', function() {
            const input = document.getElementById(label.getAttribute('for'));
            if (input) {
                updateStars(parseInt(input.value));
            }
        });
    });

    document.querySelector('.star-rating-input').addEventListener('mouseleave', function() {
        const checkedInput = document.querySelector('.star-rating-input input[type="radio"]:checked');
        if (checkedInput) {
            updateStars(parseInt(checkedInput.value));
        } else {
            updateStars(0);
        }
    });

    // Function to recalculate and update reviews summary
    function updateReviewsSummary() {
        const reviewCards = document.querySelectorAll('.review-card');
        const reviewsCount = reviewCards.length;

        if (reviewsCount === 0) {
            const reviewsSummary = document.querySelector('.reviews-summary');
            const productRating = document.querySelector('.product-rating');
            if (reviewsSummary) reviewsSummary.remove();
            if (productRating) productRating.remove();

            const customerReviewsSection = document.querySelector('.customer-reviews-section');
            const h2 = customerReviewsSection.querySelector('h2');
            const noReviews = document.querySelector('.no-reviews');
            if (!noReviews) {
                h2.insertAdjacentHTML('afterend', '<p class="no-reviews">No reviews yet. Be the first to review this product!</p>');
            }
            return;
        }

        let totalRating = 0;
        reviewCards.forEach(card => {
            const rating = parseInt(card.querySelector('.review-text').dataset.reviewRating);
            totalRating += rating;
        });
        const averageRating = totalRating / reviewsCount;

        let starsHTML = '';
        for (let i = 1; i <= 5; i++) {
            starsHTML += i <= Math.round(averageRating) ? '⭐' : '☆';
        }

        const reviewsText = reviewsCount === 1 ? 'review' : 'reviews';

        const productRating = document.querySelector('.product-rating');
        if (productRating) {
            productRating.innerHTML = `
                <div class="stars">${starsHTML}</div>
                <span class="reviews-count">${reviewsCount} ${reviewsText}</span>
            `;
        } else {
            const productTitle = document.querySelector('.product-details-title');
            if (productTitle) {
                const newRating = document.createElement('div');
                newRating.className = 'product-rating';
                newRating.innerHTML = `
                    <div class="stars">${starsHTML}</div>
                    <span class="reviews-count">${reviewsCount} ${reviewsText}</span>
                `;
                productTitle.after(newRating);
            }
        }

        const reviewsSummary = document.querySelector('.reviews-summary');
        const summaryHTML = `
            <div class="reviews-score">
                <div class="score-number">${averageRating.toFixed(1)}</div>
                <div class="score-stars">${starsHTML}</div>
                <div class="score-count">${reviewsCount} ${reviewsText}</div>
            </div>
        `;

        if (reviewsSummary) {
            reviewsSummary.innerHTML = summaryHTML;
        } else {
            const customerReviewsSection = document.querySelector('.customer-reviews-section');
            const h2 = customerReviewsSection.querySelector('h2');
            const div = document.createElement('div');
            div.className = 'reviews-summary';
            div.innerHTML = summaryHTML;
            h2.after(div);
        }
    }

    reviewForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = reviewForm.querySelector('.submit-review-btn');
        const originalBtnText = submitBtn.textContent;

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
                response = await fetch(`/reviews/${editingReviewId}`, {
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
                response = await fetch('/reviews', {
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
            console.log('Response:', result);

            if (response.ok) {
                if (editingReviewId) {
                    const reviewCard = document.querySelector(`[data-review-id="${editingReviewId}"]`);
                    if (reviewCard) {
                        const reviewStars = reviewCard.querySelector('.review-stars');
                        const reviewText = reviewCard.querySelector('.review-text');

                        let starsHTML = '';
                        for (let i = 1; i <= 5; i++) {
                            starsHTML += i <= data.rating ? '★' : '☆';
                        }
                        reviewStars.innerHTML = starsHTML;

                        reviewText.textContent = data.review_text;
                        reviewText.dataset.reviewText = data.review_text;
                        reviewText.dataset.reviewRating = data.rating;
                    }

                    updateReviewsSummary();

                    const cancelBtn = reviewForm.querySelector('.cancel-edit-review-btn');
                    if (cancelBtn) cancelBtn.click();
                } else {
                    const review = result.review;

                    const noReviews = document.querySelector('.no-reviews');
                    if (noReviews) noReviews.remove();

                    let reviewsList = document.querySelector('.reviews-list');
                    if (!reviewsList) {
                        const customerReviewsSection = document.querySelector('.customer-reviews-section');
                        reviewsList = document.createElement('div');
                        reviewsList.className = 'reviews-list';
                        customerReviewsSection.appendChild(reviewsList);
                    }

                    const userInitial = review.user.name.charAt(0).toUpperCase();
                    const reviewDate = new Date(review.created_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });

                    let starsHTML = '';
                    for (let i = 1; i <= 5; i++) {
                        starsHTML += i <= review.rating ? '★' : '☆';
                    }

                    const reviewCard = document.createElement('div');
                    reviewCard.className = 'review-card';
                    reviewCard.dataset.reviewId = review.review_id;
                    reviewCard.innerHTML = `
                        <div class="review-header">
                            <div class="reviewer-avatar">${userInitial}</div>
                            <div class="reviewer-info">
                                <div class="reviewer-name">${review.user.name}</div>
                                <div class="review-date">${reviewDate}</div>
                            </div>
                            <div class="review-actions">
                                <button class="edit-review-btn" data-review-id="${review.review_id}" title="Edit review">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M11.333 2a1.886 1.886 0 1 1 2.667 2.667L4.667 14H2v-2.667L11.333 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                <button class="delete-review-btn" data-review-id="${review.review_id}" title="Delete review">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M2 4h12M5.333 4V2.667a1.333 1.333 0 0 1 1.334-1.334h2.666a1.333 1.333 0 0 1 1.334 1.334V4m2 0v9.333a1.333 1.333 0 0 1-1.334 1.334H4.667a1.333 1.333 0 0 1-1.334-1.334V4h9.334Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="review-stars">
                            ${starsHTML}
                        </div>
                        <div class="review-text" data-review-text="${review.review_text}" data-review-rating="${review.rating}">
                            ${review.review_text}
                        </div>
                    `;

                    reviewsList.insertBefore(reviewCard, reviewsList.firstChild);
                    updateReviewsSummary();
                }

                reviewForm.reset();
                updateStars(0);
            } else {
                console.error('Error:', result);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;
        }
    });

    document.addEventListener('click', async function(e) {
        if (e.target.closest('.delete-review-btn')) {
            const btn = e.target.closest('.delete-review-btn');
            const reviewId = btn.dataset.reviewId;

            if (!confirm('Are you sure you want to delete this review?')) {
                return;
            }

            try {
                const response = await fetch(`/reviews/${reviewId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (response.ok) {
                    const reviewCard = document.querySelector(`[data-review-id="${reviewId}"]`);
                    if (reviewCard) reviewCard.remove();

                    const reviewsList = document.querySelector('.reviews-list');
                    if (reviewsList && reviewsList.querySelectorAll('.review-card').length === 0) {
                        reviewsList.remove();
                    }

                    updateReviewsSummary();
                } else {
                    const result = await response.json();
                    console.error('Error:', result);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        if (e.target.closest('.edit-review-btn')) {
            const btn = e.target.closest('.edit-review-btn');
            const reviewId = btn.dataset.reviewId;
            const reviewCard = document.querySelector(`[data-review-id="${reviewId}"]`);
            const reviewTextDiv = reviewCard.querySelector('.review-text');
            const currentText = reviewTextDiv.dataset.reviewText;
            const currentRating = parseInt(reviewTextDiv.dataset.reviewRating);

            editingReviewId = reviewId;

            reviewForm.querySelector('#review_text').value = currentText;

            const ratingInput = reviewForm.querySelector(`input[name="rating"][value="${currentRating}"]`);
            if (ratingInput) {
                ratingInput.checked = true;
                updateStars(currentRating);
            }

            const formHeading = document.querySelector('.add-review-section h2');
            const submitBtn = reviewForm.querySelector('.submit-review-btn');
            formHeading.textContent = 'Edit Your Review';
            submitBtn.textContent = 'Update Review';

            let cancelBtn = reviewForm.querySelector('.cancel-edit-review-btn');
            if (!cancelBtn) {
                cancelBtn = document.createElement('button');
                cancelBtn.type = 'button';
                cancelBtn.className = 'cancel-edit-review-btn';
                cancelBtn.textContent = 'Cancel';
                submitBtn.parentNode.insertBefore(cancelBtn, submitBtn.nextSibling);
            }

            document.querySelector('.add-review-section').scrollIntoView({ behavior: 'smooth', block: 'center' });

            cancelBtn.addEventListener('click', function() {
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

