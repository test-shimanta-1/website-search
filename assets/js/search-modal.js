jQuery(function ($) {
    const $body  = $('body');
    const $modal = $('.sdw-search-modal');

    function openModal() {
        $modal.addClass('is-open');
        $body.addClass('sdw-modal-open');
        setTimeout(() => {
            $modal.find('.sdw-search-input').focus();
        }, 200);
    }

    function closeModal() {
        $modal.removeClass('is-open');
        $body.removeClass('sdw-modal-open');
    }

    // Open modal
    $(document).on('click', '.sdw-search-open', function (e) {
        e.preventDefault();
        openModal();
    });

    // Close (overlay / button)
    $(document).on('click', '.sdw-search-close, .sdw-search-modal-overlay', function () {
        closeModal();
    });

    // ESC key
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $modal.hasClass('is-open')) {
            closeModal();
        }
    });
});
