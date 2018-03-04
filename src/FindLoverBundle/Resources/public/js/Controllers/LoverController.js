import LoverModel from '../Models/LoverModel';

export default class LoverController {
    constructor() {
        this.LoverModel = new LoverModel();
        this.attachDomEvents();
        this.setUserOffline();
    }

    attachDomEvents() {
        let self = this;
        $(document).ready(function() {
            self.setUserOnline();
        });
    }

    setUserOffline() {
        window.onbeforeunload = function() {
            if ($('#logged-in-menu').length) {
                this.LoverModel.setUserOffline();
            }
        };
    }

    setUserOnline() {
        if ($('#logged-in-menu').length) {
            this.LoverModel.setUserOnline();
        }
    }

    set LoverModel(LoverModel) {
        this._LoverModel = LoverModel;
    }

    get LoverModel() {
        return this._LoverModel;
    }
}
