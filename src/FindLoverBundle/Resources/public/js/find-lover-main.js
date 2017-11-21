attachDoemEvents();

function attachDoemEvents() {
    var offset = 0;

    $(document).ready( function () {
        $('#search-lover').autocomplete({
            minLength: 1,
            source: source
        });
        $('li#search-more').on('click', function (e) {
            $.ajax({
                url: $('#data-ajax-url').attr('data-ajax-url'),
                data:{
                    term: $.trim($('#search-lover').val()),
                    offset: offset + 6
                },
                success: function (data) {
                    var template = $($('#result-lovers').find('li#template'));
                    for (var i = 0; i < data.length; i++) {
                        template
                            .clone()
                            .insertBefore('#search-more')
                            .find('img')
                            .attr('src', data[i].profilePicture)
                            .addBack()
                            .find('a')
                            .attr('href', $('#data-ajax-url').attr('data-profile-url').replace(0, data[i].id))
                            .append(data[i].firstName + ' ' + data[i].lastName + '( ' + data[i].nickname + ' )')
                            .addBack()
                            .removeAttr('id');
                    }
                }
            });
        });
    } );
}

function source(request, response) {
    $.ajax( {
        url: $('#data-ajax-url').attr('data-ajax-url'),
        data: {
            term: request.term,
            offset: 0
        },
        success: function( data ) {
            var loversList = $('#result-lovers');
            loversList.children().not('li#search-more, li#template').remove();
            loversList.show();
            var template = $(loversList.find('li#template'));
            for (var i = 0; i < data.length; i++) {
                template
                    .clone()
                    .insertBefore('#search-more')
                    .find('img')
                    .attr('src', data[i].profilePicture)
                    .addBack()
                    .find('a')
                    .attr('href', $('#data-ajax-url').attr('data-profile-url').replace(0, data[i].id))
                    .append(data[i].firstName + ' ' + data[i].lastName + '( ' + data[i].nickname + ' )')
                    .addBack()
                    .removeAttr('id');
            }

            if( loversList.find('li.search-result-item:not(#template)').length < 6) {
                loversList.find('li#search-more').hide();
            } else {
                loversList.find('li#search-more').show();
            }

            if( ! loversList.find('li.search-result-item').length ) {
                loversList.find('li#search-more').hide();
                loversList.append('<li>No results found.</li>')
            }

            $('body').on('click', function ( e ) {
                if( $(e.target).attr('id') !== 'result-lovers' && $(e.target).attr('id') !== 'search-more' ) {
                    $('#result-lovers').hide();
                }
            });
        }
    } );
}