attachDomEvents();
setUserOffline();

function attachDomEvents() {
    let offset = 0;

    $(document).ready(function () {
        //Apply jQuery autocomplete to search field
        bindJqueyAutocompleteEvent();

        //Bind search more functionality to search more list item
        bindSearchMoreEvent(offset);

        //Bind click event to "Add lover" button
        bindSendInvitationEvent();

        bindGetNotificationsEvent();

        bindShowNotificationsEvent();

        openTestWebSocket();

        getRecentlyOnlineContacts();

        clearSessionOnLogout();

        getRecentSearches();

    });

    setUserOnline();
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

                addRecentSearch();
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
                    $('#lovers-controls').find('ul').prepend('<li class="lover-control" id="cancel-invitation">Cancel invitation</li>');
                }
            }
        });
    });
}

function bindGetNotificationsEvent() {
    if($('ul#logged-in-menu').length) {
        $.ajax({
            method:"GET",
            url:$('#notifications-bell').attr('data-notifications-url'),
            success: function (data) {
                let invitations = JSON.parse(data);
                let utils = $('#notification-utils');

                if( invitations.length ) {
                    utils.show();
                    utils.find('#count').text(invitations.length);
                    let template = $('#notification-template');

                    for (var i = 0; i < invitations.length; i++) {
                        let date = new Date(invitations[i]['dateSent']);
                        template
                            .clone()
                            .appendTo('#notifications-list')
                            .find('img')
                            .attr('src', invitations[i]['lover'].profile_picture)
                            .addBack()
                            .find('span')
                            .text(invitations[i]['lover'].nickname + ' has sent you an invitation')
                            .addBack()
                            .find('#time-happened')
                            .text(date.getDate() + ' ' + date.toLocaleString(navigator.language, { month: "long" }) + ' at ' + date.getHours() + ':' + date.getMinutes())
                            .addBack();
                        $('#notification-template:last-of-type').attr('id', invitations[i]['lover'].id);
                    }
                    bindInvitationHandlerEvent();
                }
            }
        });
    }
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

function bindInvitationHandlerEvent() {
    $('.notification #accept').on('click',function (e) {
        $.ajax({
            method:"POST",
            url: $('#notifications-list').attr('data-ajax-url'),
            data: {
                senderId: $(e.target).parent().attr('id')
            },
            success: function (data) {
                if(data) {
                    var list = $('#notifications-list');
                    var utils = $('#notification-utils');
                    list.find('li#' + $(e.target).parent().attr('id') ).fadeOut(400, function () {
                        list.css('bottom', -list.height() - 15);
                        utils.find('#count').text(utils.find('#count').text() * 1 - 1);

                        if(utils.find('#count').text() * 1 === 0) {
                            utils.hide();
                        }

                        //Replace "Add lover" button with "Remove lover if the user is currently at the target lover's profile"
                        if($('#accept-invitation').length) {
                            $('#accept-invitation').remove();
                            $('#lovers-controls').find('ul').prepend('<li class="lover-control" id="remove-lover">Remove lover</li>');
                        }
                    });
                }
            }
        })
    })
}

function getRecentlyOnlineContacts() {
    if($('ul#logged-in-menu').length) {

        clearInterval(window.recentlyOnlineContacts);
        window.recentlyOnlineContacts = setInterval(getRecentlyOnlineContacts, 60000);

        $.ajax({
            method: "GET",
            url: $('#lovers-recently-online').attr('data-ajax-url'),
            success: function (data) {
                if(data) {
                    let parsedLovers = JSON.parse(data);
                    $('#lovers-recently-online-list').children(':not(#template-lover)').remove();
                    for (let i = 0; i < parsedLovers.length; i++) {
                        let template = $('#template-lover');
                        template
                            .clone()
                            .appendTo('#lovers-recently-online-list')
                            .find('img.search-profile-pic')
                            .attr('src', parsedLovers[i].profilePicture)
                            .addBack()
                            .find('span#names')
                            .text(parsedLovers[i].firstName + ' ' + parsedLovers[i].lastName + '( ' + parsedLovers[i].nickname + ' )');

                        template = $('#template-lover:last-of-type');

                        if(parsedLovers[i].lastOnline) {
                            let currentDate = new Date();
                            let loverDate = new Date(parsedLovers[i].lastOnline);
                            let diff =  (currentDate.getHours() * 60 + currentDate.getMinutes()) - (loverDate.getHours() * 60 + loverDate.getMinutes());
                            diff === 0 ? diff = 1 : diff;
                            template
                                .children('span#online-or-last')
                                .text(diff + ' mins')
                        } else {
                            template
                                .children('span#online-or-last')
                                .append('<i class="fa fa-circle" aria-hidden="true"></i>')
                        }

                        template.attr('id', parsedLovers[i].id);
                    }
                } else {
                    $('#lovers-recently-online-list').children(':not(#template-lover)').remove();
                }
            }
        });
    }
}

function openTestWebSocket() {
    if($('#logged-in-menu').length) {
        var webSocket = WS.connect(_WS_URI);

        webSocket.on("socket/connect", function(session){

            session.subscribe("lover/channel", function(uri, payload){
                console.log("Received message", payload.msg);
            });

            session.publish("lover/channel", {msg: "This is a message!"});
        });
    }
}

function clearSessionOnLogout() {
    $('a#logout').on('click', function () {
        sessionStorage.removeItem('lovers');
    });
}

function addRecentSearch() {
    $('.search-result-item').on('click', function (e) {
        let id = $(e.target).attr('href').match(/\/([0-9]+)/);
        if(id !== null) {
            $.ajax({
               method:"POST",
               url: $('#lover-search-input-li').attr('data-ajax-url'),
               data: {
                   searchedId: id[1]
               }
            });
        }
    })
}

function getRecentSearches(isCalledForMore, offset = 0) {
    if($('#logged-in-menu').length) {
        if(offset < 36) {
            $.ajax({
                method:"GET",
                url: $('#lovers-recently-viewed').attr('data-ajax-url'),
                data: {
                    offset: offset
                },
                success: function (data) {
                    if(data) {
                        data = JSON.parse(data);
                        let template = $('#template-search-lover');
                        for (let i = 0; i < data.length - 1; i++) {
                            template
                                .clone()
                                .appendTo('#recent-searches')
                                .find('.search-profile-pic')
                                .attr('src', data[i].profilePicture)
                                .addBack()
                                .find('a')
                                .attr('href', template.find('a').attr('href').replace('0', data[i].id))
                                .addBack()
                                .find('#lovers-data')
                                .text(data[i].firstName + ' ' + data[i].lastName + ' (' + data[i].nickname + ' ) ');
                        }
                        $('#template-search-lover:not(:first-of-type)').removeAttr('id');
                        $('#lovers-recently-viewed').show();
                    }
                    if(! isCalledForMore) {
                        getMoreRecentSearches(data[data.length - 1]);
                    }
                }
            });
        }
    }
}

function getMoreRecentSearches(idsLength) {
    if(idsLength > 6) {
        let offset = 0;
        let timesScrolled = 0;
        $('#recent-searches').on('wheel', function (e) {
            timesScrolled++;
            let direction = e.originalEvent.deltaY + '';
            if(parseInt(direction[0]) && 1 === timesScrolled) {
                offset += 6;
                getRecentSearches(true, offset);
                timesScrolled = 0;
            }
        })
    } else {
        $('#recent-searches').off('wheel');
    }
}

function setUserOffline() {
    window.onbeforeunload = function() {
        if($('#logged-in-menu').length) {
            $.ajax({
                method:"POST",
                url: $('body > header nav').attr('data-ajax-logout-url')
            })
        }
    };
}

function setUserOnline() {
    if($('#logged-in-menu').length) {
        $.ajax({
            method:"POST",
            url: $('body > header nav').attr('data-ajax-login-url')
        })
    }
}