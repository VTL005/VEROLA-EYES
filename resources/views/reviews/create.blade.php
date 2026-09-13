@extends('layouts.app')


@section(
'title',
'Đánh giá sản phẩm - VELORA Eyes'
)


@push('styles')

<link rel="stylesheet" href="{{ asset('css/product-review-form.css') }}">

@endpush


@section('content')

<section class="section review-create-page">

  <div class="velora-container">

    <div class="review-create-shell">

      <a href="{{ route(
                    'orders.index',
                    ['status' => 'completed']
                ) }}" class="review-create-back">
        ← Quay lại đơn hàng
      </a>


      @if(session('success'))

      <div class="alert alert-success">
        {{ session('success') }}
      </div>

      @endif


      <div class="review-form-card">

        <span class="hero-kicker">
          PRODUCT REVIEW
        </span>

        <h1>
          Đánh giá sản phẩm
        </h1>

        <p class="text-muted review-create-intro">
          Chia sẻ trải nghiệm của bạn để giúp
          những khách hàng khác lựa chọn dễ dàng hơn.
        </p>


        <div class="review-create-product">

          <div class="review-create-product-mark">
            V
          </div>

          <div>

            <strong>
              {{ $product->name }}
            </strong>

            <span>
              SKU: {{ $product->sku }}
            </span>

          </div>

        </div>


        <form class="product-review-form js-product-review-form" action="{{ route(
                            'reviews.store',
                            $product
                        ) }}" method="POST">

          @csrf


          <fieldset class="product-review-rating">

            <legend class="form-label" id="review-rating-label">
              Bạn cảm thấy sản phẩm thế nào?
            </legend>

            <div class="product-review-stars" role="radiogroup" aria-labelledby="review-rating-label">

              @for($rating = 5; $rating >= 1; $rating--)

              <input class="product-review-star-input" type="radio" name="rating" id="review-rating-{{ $rating }}"
                value="{{ $rating }}" {{ (int) old('rating') === $rating ? 'checked' : '' }} required>

              <label class="product-review-star" for="review-rating-{{ $rating }}" title="{{ $rating }} sao"
                aria-label="{{ $rating }} sao">
                <span aria-hidden="true">★</span>
              </label>

              @endfor

            </div>

            <p class="product-review-rating-text" id="reviewRatingText" aria-live="polite">
              Chạm vào một ngôi sao để đánh giá
            </p>

            @error('rating')

            <p class="product-review-error">
              {{ $message }}
            </p>

            @enderror

          </fieldset>


          <div class="form-group product-review-comment-group">

            <div class="product-review-comment-heading">

              <label for="reviewComment" class="form-label">
                Nhận xét
              </label>

              <span class="product-review-counter" id="reviewCommentCount">
                0/500
              </span>

            </div>

            <textarea id="reviewComment" name="comment" class="form-control product-review-comment" maxlength="500"
              rows="5"
              placeholder="Chia sẻ trải nghiệm của bạn về chất lượng, kiểu dáng hoặc độ thoải mái...">{{ old('comment') }}</textarea>

            @error('comment')

            <p class="product-review-error">
              {{ $message }}
            </p>

            @enderror

          </div>


          <div class="review-create-actions">

            <button type="submit" class="btn btn-primary product-review-submit">
              Gửi đánh giá
            </button>

            <a href="{{ route(
                            'orders.index',
                            ['status' => 'completed']
                        ) }}" class="btn btn-outline">
              Để sau
            </a>

          </div>

        </form>

      </div>

    </div>

  </div>

</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
  const reviewForm = document.querySelector('.js-product-review-form');

  if (!reviewForm) {
    return;
  }

  const ratingLabels = {
    1: 'Không hài lòng',
    2: 'Chưa tốt',
    3: 'Bình thường',
    4: 'Tốt',
    5: 'Rất tốt'
  };

  const ratingInputs = reviewForm.querySelectorAll('input[name="rating"]');
  const ratingText = reviewForm.querySelector('#reviewRatingText');
  const comment = reviewForm.querySelector('#reviewComment');
  const commentCount = reviewForm.querySelector('#reviewCommentCount');

  function updateRatingText() {
    const selectedRating = reviewForm.querySelector('input[name="rating"]:checked');

    ratingText.textContent = selectedRating ?
      ratingLabels[selectedRating.value] + ' · ' + selectedRating.value + '/5 sao' :
      'Chạm vào một ngôi sao để đánh giá';
  }

  function updateCommentCount() {
    commentCount.textContent = comment.value.length + '/500';
  }

  ratingInputs.forEach(function(input) {
    input.addEventListener('change', updateRatingText);
  });

  comment.addEventListener('input', updateCommentCount);

  updateRatingText();
  updateCommentCount();
});
</script>

@endsection