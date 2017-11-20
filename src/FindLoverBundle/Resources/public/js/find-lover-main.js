attachDoemEvents();

function attachDoemEvents() {
    $(document).ready( function () {
        $('#search-lover').autocomplete({
            minLength: 1,
            source: source,
            select: selectResult,
            formatResult: function (suggestion, currentValue) {
                console.log(suggestion);
                console.log(currentValue);
            }
        });
    } );
}

function source(request, response) {
    var offset = 0;
    var term = request.term;
    $.ajax( {
        url: $('#data-ajax-url').attr('data-ajax-url'),
        data: {
            term: term,
            offset: offset
        },
        success: function( data ) {
            var loversList = $('#result-lovers');
            loversList.children().not('li#search-more').remove();
            loversList.find('li#search-more').show();
            loversList.show();
            for (var i = 0; i < data.length; i++) {
                if( loversList.attr('data-user-id') != data[i].id ) {
                    $('<li class="search-result-item" id="' + data[i].id + '"><img class="search-profile-pic" src="' + data[i].profilePicture + '" alt="Pic">' + data[i].firstName + ' ' + data[i].lastName + '( ' + data[i].nickname + ' )' + '</li>')
                        .insertBefore('#search-more')
                }
            }

            if( ! loversList.find('li.search-result-item').length ) {
                loversList.find('li#search-more').hide();
                loversList.append('<li>No results found.</li>')
            }

            $('body').on('click', function ( e ) {
                if( $(e.target).attr('id') !== 'result-lovers' && $(e.target).attr('id') !== 'search-more' ) {
                    $('#result-lovers').hide();
                }
            })
        }
    } );
    $('#search-more').on('click', function () {
        $.ajax({
            url: $('#data-ajax-url').attr('data-ajax-url'),
            data:{
                term: term,
                offset: offset + 10
            },
            success: function (data) {
                for (var i = 0; i < data.length; i++) {
                    $('<li class="search-result-item" id="' + data[i].id + '"><img class="search-profile-pic" src="' + data[i].profilePicture + '" alt="Pic">' + data[i].firstName + ' ' + data[i].lastName + '( ' + data[i].nickname + ' )' + '</li>')
                        .insertBefore('#search-more');
                }
            }
        });
    });
}

function selectResult(event, ui) {

}