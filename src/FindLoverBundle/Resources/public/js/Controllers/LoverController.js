import LoverModel from '../Models/LoverModel';

export default class LoverController {
    constructor() {
        this.LoverModel = new LoverModel();
        this.attachDomEvents();
    }

    attachDomEvents() {
        let self = this;
        $(document).ready(function() {
            self.setUserOnline();
        });
    }

    set LoverModel(LoverModel) {
        this._LoverModel = LoverModel;
    }

    get LoverModel() {
        return this._LoverModel;
    }
}
