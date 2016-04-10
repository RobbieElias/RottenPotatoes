$(document).ready(function() {

    var title = document.title.split('-')[0].trim();
    $('.nav a:contains("' + title + '")').addClass('active');

    $('#searchForm').on('submit', function(e) {
        if ($('#search-term').val() === '') {
            return false;
        }
    })

});