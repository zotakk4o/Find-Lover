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

    setAjaxUrls() {

    }
}
