export default class LoverModel {
    constructor() {
        this.attachDomEvents();
    }

    attachDomEvents() {
        let self = this;
        $(document).ready(function() {
            self.setAjaxUrls();
        });
    }

    changeUserState(urlType) {
        $.ajax({
            method: 'POST',
            url: urlType === 'online' ? this.onlineUrl : this.offlineUrl,
        });
    }

    setUserOffline() {
        this.changeUserState('offline');
    }

    setUserOnline() {
        this.changeUserState('online');
    }

    setAjaxUrls() {
        let urlsContainer = $('body > header nav');

        this.offlineUrl = urlsContainer.attr('data-ajax-logout-url');
        this.onlineUrl = urlsContainer.attr('data-ajax-login-url');
    }

    set offlineUrl(offlineUrl) {
        this._offlineUrl = offlineUrl;
    }

    get offlineUrl() {
        return this._offlineUrl;
    }

    set onlineUrl(onlineUrl) {
        this._onlineUrl = onlineUrl;
    }

    get onlineUrl() {
        return this._onlineUrl;
    }
}
