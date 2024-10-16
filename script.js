jQuery(document).ready(function($) {
    // Toggle the sidebar menu
    $('.tmaudasbourd-toggle-menu').on('click', function() {
        $('.tmaudasbourd-menu').toggleClass('open');
    });

    // Add new menu item
    $('#tmaudasbourd-add-menu-item').on('click', function() {
        var index = $('#tmaudasbourd-menu-items-container .tmaudasbourd-menu-item').length;
        var newItem = `
            <div class="tmaudasbourd-menu-item">
                <input type="text" name="tmaudasbourd_menu_items[` + index + `][title]" value="" placeholder="عنوان" />
                <textarea name="tmaudasbourd_menu_items[` + index + `][content]" placeholder="محتوا"></textarea>
                <input type="text" name="tmaudasbourd_menu_items[` + index + `][icon]" value="" placeholder="کلاس Dashicon" />
                <button type="button" class="button button-secondary tmaudasbourd-remove-menu-item">حذف</button>
            </div>
        `;
        $('#tmaudasbourd-menu-items-container').append(newItem);
    });

    // Remove menu item
    $('#tmaudasbourd-menu-items-container').on('click', '.tmaudasbourd-remove-menu-item', function() {
        $(this).closest('.tmaudasbourd-menu-item').remove();
    });

    // Load content via AJAX
    $(document).on('click', '.tmaudasbourd-menu-link', function(e) {
        e.preventDefault();
        var content = $(this).data('content');
        $.post(tmaudasbourd_ajax.ajax_url, {
            action: 'tmaudasbourd_load_content',
            content: content
        }, function(response) {
            $('.tmaudasbourd-content').html(response);
            $('.tmaudasbourd-menu').removeClass('open'); // Close the menu after selection
        });
    });

    // Load the content of the first menu item by default
    var firstContent = $('.tmaudasbourd-menu-link').first().data('content');
    if (firstContent) {
        $.post(tmaudasbourd_ajax.ajax_url, {
            action: 'tmaudasbourd_load_content',
            content: firstContent
        }, function(response) {
            $('.tmaudasbourd-content').html(response);
        });
    }
});
