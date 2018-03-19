// import ChatModel from '../Models/ChatModel';

export default class ChatController {
    constructor() {
        this.attachDomEvents();
        this.initializeChatsData();
    }

    attachDomEvents() {
        let self = this;
        $(document).ready(function () {
            self.openWebSocket();
        });
    }

    populateChat(data, participants) {
        let template = $('li#template.chat');
        if (!$('li#' + participants).length) {
            template
                .clone()
                .appendTo('section#chats ul');

            template = $('li#template.chat:last-of-type');
            let name = data.guestLover.first_name + ' ' + data.guestLover.last_name;
            name.length > 30 ? name = name.substr(0, 30) : name;
            template.find('div.header')
                .prepend(name);

            let otherMsgTemplate = template.find('div.message-content-other');
            let myMsgTemplate = template.find('div.message-content-mine');
            let previousId;

            if (data.chatMessages) {
                for (let i = data.chatMessages.length - 1; i >= 0; i--) {
                    let msg = data.chatMessages[i];
                    if (parseInt(msg.sender_id) !== data.currentLover.id) {
                        otherMsgTemplate
                            .clone()
                            .appendTo(template.find('div.content'));
                        otherMsgTemplate = template.find('div.message-content-other:last-of-type');
                        otherMsgTemplate
                            .find('span')
                            .text(msg.message);

                        if (previousId !== data.guestLover.id) {
                            otherMsgTemplate.find('img').attr('src', data.guestLover.profile_picture);
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

            if (data.guestLover.last_online) {
                let currentDate = new Date();
                let loverDate = new Date(data.guestLover.last_online);
                let diff = Math.round(((currentDate.getTime() / 1000) - (loverDate.getTime() / 1000)) / 60);
                diff === 0 ? diff = 1 : diff;
                if (diff < 60 * 24) {
                    if (diff > 60) {
                        template
                            .find('span.online-or-last')
                            .text(Math.round(diff / 60) + ' hrs');
                    } else {
                        template
                            .find('span.online-or-last')
                            .text(diff + ' mins');
                    }
                }
            } else {
                template
                    .find('span.online-or-last')
                    .append('<i class="fa fa-circle" aria-hidden="true"></i>');
            }

            $('li#template.chat:not(:first-of-type)').attr('id', participants).css('display', 'inline-block');
            $('section#chats').trigger('chatCreated', template);
        }
    }

    createChat(participants) {
        let self = this;
        $.ajax({
            method: 'GET',
            url: $('section#chats').attr('data-ajax-url'),
            data: {
                offset: 0,
                participants: participants,
            },
            success: function(data) {
                if (data) {
                    data = JSON.parse(data);
                    self.populateChat(data, participants);
                }
            },
        });
    }

    publishMessageHandler(session) {
        let self = this;
        let textArea = $('textarea.message-input');
        textArea.on('keyup', function(e) {
            if (e.keyCode === 13 && $.trim($(e.currentTarget).val()) !== '') {
                session.publish(self.getChatChannel() + $(e.currentTarget).closest('li.chat:not("#template")').attr('id'), $.trim($(e.currentTarget).val()));
                $(e.currentTarget).val('');
            }
        });
    }

    openWebSocket() {
        let self = this;
        $('.lover-in-online-section, li#message.lover-control').on('click', function(e) {
            let chatId;

            if ($(e.currentTarget).attr('id') === 'message') {
                chatId = $('div#picture div.name').attr('id') + '-' + $('li#profile-picture a').attr('id');
            } else {
                chatId = $(e.currentTarget).attr('id') + '-' + $('li#profile-picture a').attr('id');
            }

            if (!self.chatIsClicked(chatId)) {
                self.addChatOpening(chatId);

                if (self.getChatOpenings(chatId) !== 1) {
                    $('li#' + chatId + '.chat').css('display', 'inline-block');
                }
                if (1 === self.getChatOpenings(chatId) && webSocket) {
                    self.createChat(chatId);

                    if(webSocketSession){
                        webSocketSession.subscribe('lover/channel/' + chatId, function(uri, message) {
                            if (Object.is(message)) {
                                message = JSON.parse(message);

                                if (message.message) {
                                    populateChat();
                                }
                            }

                            $('section#chats').on('chatCreated', function() {
                                self.publishMessageHandler(webSocketSession);
                            });
                        });
                    }
                }
            } else {
                $('li#' + chatId + '.chat').hide();
            }

            self.chatChangeClickedState(chatId);
        });
    }

    get chatsData() {
        return this._chatsData;
    }

    getChatChannel() {
        return 'lover/channel/';
    }

    getChatOpeningsPrefix() {
        return '-openings';
    }

    getChatClickedPrefix() {
        return '-clicked';
    }

    chatIsClicked(key) {
        let newKey = key.trim() + this.getChatClickedPrefix();
        if(this.chatsData[newKey] === undefined) {
            this.chatsData[newKey] = false;
        }
        return this.chatsData[newKey];
    }

    chatChangeClickedState(key) {
        let newKey = key.trim() + this.getChatClickedPrefix();
        this.chatsData[newKey] = !this.chatsData[newKey];
    }

    getChatOpenings(key) {
        let newKey = key.trim() + this.getChatOpeningsPrefix();
        return parseInt(this.chatsData[newKey]);
    }

    addChatOpening(key) {
        let newKey = key.trim() + this.getChatOpeningsPrefix();
        if(isNaN(parseInt(this.chatsData[newKey]))) {
            this.chatsData[newKey] = 1;
            return;
        }
        this.chatsData[newKey] = 1 + this.chatsData[newKey];
    }

    initializeChatsData() {
        this._chatsData = {};
    }
}
