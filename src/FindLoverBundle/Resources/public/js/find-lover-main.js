attachDoemEvents();

function attachDoemEvents() {
    var offset = 0;

    $(document).ready(function () {
        //Apply jQuery autocomplete to search field
        bindJqueyAutocompleteEvent();

        //Bind search more functionality to search more list item
        bindSearchMoreEvent(offset);

        //Bind click event to "Add lover" button
        bindSendInvitationEvent();

        bindGetNotificationsEvent();

        bindShowNotificationsEvent();
    });
}

function bindJqueyAutocompleteEvent() {
    $('#search-lover').autocomplete({
        minLength: 1,
        source: source
    });

    function source(request, response) {
        $.ajax( {
            method: "POST",
            url: $('#data-ajax-url').attr('data-ajax-url'),
            data: {
                term: request.term,
                offset: 0
            },
            success: function( data ) {
                data = JSON.parse(data);
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

                if( data.length < 6) {
                    loversList.find('li#search-more').hide();
                } else {
                    loversList.find('li#search-more').show();
                }

                if( ! loversList.find('li.search-result-item:not(#template)').length ) {
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
}

function bindSearchMoreEvent(offset) {
    $('li#search-more').on('click', function (e) {
        offset += 6;
        $.ajax({
            method: "POST",
            url: $('#data-ajax-url').attr('data-ajax-url'),
            data:{
                term: $.trim($('#search-lover').val()),
                offset: offset
            },
            success: function (data) {
                data = JSON.parse(data);
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

                if( data.length < 6) {
                    $('#result-lovers').find('li#search-more').hide();
                } else {
                    $('#result-lovers').find('li#search-more').show();
                }
            }
        });
    });
}

function bindSendInvitationEvent() {
    $('li#add-lover').on('click', function (e) {
        $.ajax({
            method:"POST",
            url: $(e.target).attr('data-ajax-url'),
            data: {
                receiverId: $('#picture').find('.name').attr('id')
            },
            success: function (data) {
                if(data) {
                    $('li#add-lover').remove();
                    $('#lovers-controls').find('ul').prepend('<li class="lover-control" id="invitation-sent">Invitation sent</li>');
                }
            }
        });
    });
}

function bindGetNotificationsEvent() {
    $.ajax({
        method:"GET",
        url:$('#notifications-bell').attr('data-notifications-url'),
        success: function (data) {
            var lovers = JSON.parse(data);
            var utils = $('#notification-utils');

            if( lovers.length ) {
                utils.show();
                utils.find('#count').text(lovers.length);
                var template = $('#notification-template');

                for (var i = 0; i < lovers.length; i++) {
                    template
                        .clone()
                        .appendTo('#notifications-list')
                        .find('img')
                        .attr('src', lovers[i].profile_picture)
                        .addBack()
                        .find('span')
                        .text(lovers[i].nickname + ' has sent you an invitation')
                        .addBack()
                        .attr('id', lovers[i].id)
                }
            }
        }
    });
}

function bindShowNotificationsEvent() {
    var list = $('#notifications-list');
    $('body').on('click', function (e) {
        var targetId = $(e.target).attr('id');
        if(targetId === 'notifications-bell' && list.css('display') === 'none') {
            list.css('bottom', -list.height() - 15);
            list.show();
        } else if (! $(e.target).parents('#notifications-list').length) {
            list.hide();
        }
    });
}