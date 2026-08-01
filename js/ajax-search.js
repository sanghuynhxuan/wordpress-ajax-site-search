jQuery(document).ready(function($) {
    // Tìm tất cả các form được đánh dấu bằng class của chúng ta
    $('.ajax-search-container').each(function() {
        var container = $(this);
        var searchInput = container.find('.ajax-search-input');
        var resultsContainer = container.find('.ajax-search-results');
        var typingTimer;
        var doneTypingInterval = 500;

        searchInput.on('keyup', function() {
            clearTimeout(typingTimer);
            var query = $(this).val();
            if (query.length >= 2) {
                typingTimer = setTimeout(function() { performAjaxSearch(query); }, doneTypingInterval);
            } else {
                resultsContainer.html('').removeClass('active');
            }
        });

        function performAjaxSearch(query) {
            resultsContainer.html('<div class="loading-spinner"></div>').addClass('active');
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: { action: 'my_ajax_search', query: query, nonce: ajax_object.nonce },
                success: function(response) { resultsContainer.html(response); },
            });
        }
    });

    // Click ra ngoài để ẩn kết quả
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.ajax-search-container').length) {
            $('.ajax-search-results').html('').removeClass('active');
        }
    });
});