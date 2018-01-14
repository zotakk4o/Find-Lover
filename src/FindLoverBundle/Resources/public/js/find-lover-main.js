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

        openWebSocket();

        getRecentlyOnlineContacts();

        getRecentSearches();

        bindRemoveLoverFriend();

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
            url: $(e.target).parent().attr('data-add-lover'),
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
            list.css('bottom', -list.height() - 30);
            list.show();
        } else if (! $(e.target).parents('#notifications-list').length) {
            list.hide();
        }
    });
}

function bindInvitationHandlerEvent() {
    $('.notification #accept, #accept-invitation').on('click',function (e) {
        let senderId;
        $('section#lovers-data').length > 0 ? senderId = $('#picture').find('.name').attr('id') : senderId = $(e.target).parent().attr('id');

        $.ajax({
            method:"POST",
            url: $('#notifications-list').attr('data-ajax-url'),
            data: {
                senderId: senderId
            },
            success: function (data) {
                if(data) {
                    let list = $('#notifications-list');
                    let utils = $('#notification-utils');
                    list.find('li#' + senderId).fadeOut(400, function () {
                        list.css('bottom', -list.height() - 15);
                        utils.find('#count').text(utils.find('#count').text() * 1 - 1);

                        if(utils.find('#count').text() * 1 === 0) {
                            utils.hide();
                        }

                        //Replace "Add lover" button with "Remove lover if the user is currently at the target lover's profile"
                        if($('#accept-invitation').length) {
                            $('#accept-invitation').remove();
                            $('#lovers-controls').find('ul').prepend('<li class="lover-control" id="remove-lover">Remove lover</li>');
                            bindRemoveLoverFriend();
                            getRecentlyOnlineContacts();
                        }
                    });
                }
            }
        })
    })
}

function bindRemoveLoverFriend() {
    $('#remove-lover').on('click', function () {
        $.ajax({
            method:"POST",
            url: $('#remove-lover').parent().attr('data-remove-lover'),
            data: {
                targetId: $('div#picture').find('div.name').attr('id')
            },
            success: function (data) {
                if(data) {
                    $('#remove-lover').remove();
                    $('#lovers-controls').find('ul').prepend('<li class="lover-control" id="add-lover">Add lover</li>');
                    bindSendInvitationEvent();
                    getRecentlyOnlineContacts();
                }
            }
        });
    });
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
                            let diff = Math.round(((currentDate.getTime() / 1000) - (loverDate.getTime() / 1000)) / 60);
                            diff === 0 ? diff = 1 : diff;

                            if(diff < 60 * 24) {
                                if(diff > 60) {
                                    template
                                        .children('span.online-or-last')
                                        .text(Math.round(diff / 60) + ' hrs');
                                } else {
                                    template
                                        .children('span.online-or-last')
                                        .text(diff + ' mins');
                                }
                            }
                        } else {
                            template
                                .children('span.online-or-last')
                                .append('<i class="fa fa-circle" aria-hidden="true"></i>')
                        }

                        template.attr('id', parsedLovers[i].id);
                        openWebSocket();
                    }
                } else {
                    $('#lovers-recently-online-list').children(':not(#template-lover)').remove();
                }
            }
        });
    }
}

function openWebSocket() {
    let clicked = false;
    let timesOpened = 0;
    $('.lover-in-online-section, li#message.lover-control').on('click', function (e) {
        let chatId;
        if($(e.currentTarget).attr('id') === 'message') {
            chatId = $('div#picture div.name').attr('id') + '-' + $('li#profile-picture a').attr('id');
        } else {
            chatId = $(e.currentTarget).attr('id') + '-' + $('li#profile-picture a').attr('id');
        }

        if(! clicked) {
            clicked = ! clicked;
            timesOpened++;

            if(timesOpened !== 1) {
                openChat(chatId);
            }

            if(1 === timesOpened) {
                let webSocket = WS.connect(_WS_URI);
                createChat(chatId);

                webSocket.on('socket/connect', function(session){
                    session.subscribe('lover/channel/' + chatId, function(uri, message){
                        if(message.message) {
                            console.log(message);
                        }

                        $('section#chats').on('chatCreated', function () {
                            publishMessageHandler(session, 'lover/channel/' + chatId);
                        })
                    });
                });
            }
        } else {
            closeChat(chatId);
            clicked = ! clicked;
        }
    });
}

function openChat(id) {
    $('li#' + id + '.chat').css('display', 'inline-block');
}

function createChat(participants) {
    $.ajax({
        method: "GET",
        url: $('section#chats').attr('data-ajax-url'),
        data: {
            offset: 0,
            participants: participants
        },
        success: function (data) {
            if(data) {
                data = JSON.parse(data);
                populateChat(data, participants);
            }
        }
    });
}

function populateChat(data, participants) {
    let template = $('li#template.chat');
    if(! $('li#' + participants).length) {
        template
            .clone()
            .appendTo('section#chats ul');

        template = $('li#template.chat:last-of-type');
        let name = data.guestLover.first_name + " " + data.guestLover.last_name;
        name.length > 30 ? name = name.substr(0, 30) : name;
        template.find('div.header')
            .prepend(name);

        let otherMsgTemplate = template.find('div.message-content-other');
        let myMsgTemplate = template.find('div.message-content-mine');
        let previousId;

        if(data.chatMessages) {
            for (let i = data.chatMessages.length - 1; i >= 0 ; i--) {
                let msg = data.chatMessages[i];
                if(parseInt(msg.sender_id) !== data.currentLover.id) {
                    otherMsgTemplate
                        .clone()
                        .appendTo(template.find('div.content'));
                    otherMsgTemplate = template.find('div.message-content-other:last-of-type');
                    otherMsgTemplate
                        .find('span')
                        .text(msg.message);

                    if(previousId !== data.guestLover.id) {
                        otherMsgTemplate.find('img').attr('src', data.guestLover.profile_picture)
                    } else {
                        otherMsgTemplate.find('img').remove();
                    }

                } else {
                    myMsgTemplate
                        .clone()
                        .appendTo(template.find('div.content'));
                    myMsgTemplate = template.find('div.message-content-mine:last-of-type');
                    myMsgTemplate
                        .find('span')
                        .text(msg.message);

                }

                previousId = parseInt(msg.sender_id);
            }

            template.find('#template-other:not(:first-of-type)').removeAttr('id');
            template.find('#template-mine:not(:first)').removeAttr('id');
        }

        if(data.guestLover.last_online) {
            let currentDate = new Date();
            let loverDate = new Date(data.guestLover.last_online);
            let diff = Math.round(((currentDate.getTime() / 1000) - (loverDate.getTime() / 1000)) / 60);
            diff === 0 ? diff = 1 : diff;
            if(diff < 60 * 24) {
                if(diff > 60) {
                    template
                        .find('span.online-or-last')
                        .text(Math.round(diff / 60) + ' hrs')
                } else {
                    template
                        .find('span.online-or-last')
                        .text(diff + ' mins')
                }
            }
        } else {
            template
                .find('span.online-or-last')
                .append('<i class="fa fa-circle" aria-hidden="true"></i>')
        }

        $('li#template.chat:not(:first-of-type)').attr('id', participants).css('display', 'inline-block');
        $('section#chats').trigger('chatCreated', template);
    }
}

function publishMessageHandler(session, channel) {
    let textArea = $('textarea.message-input');
    textArea.on('keyup', function (e) {
        if(e.keyCode === 13 && $(e.target).val() !== '') {
            session.publish(channel, $.trim($(e.target).val()));
            $(e.target).val('');
        }
    });
}

function closeChat(id) {
    $('li#' + id + '.chat').hide();
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

                        if(data.length) {
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
                                    .text(data[i].firstName + ' ' + data[i].lastName + ' (' + data[i].nickname + ') ');
                            }
                            $('#template-search-lover:not(:first-of-type)').removeAttr('id');
                            $('#lovers-recently-viewed').show();
                        }

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