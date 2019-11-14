import Backbone from 'backbone';
import Router from 'router';
import { extend } from 'lodash';

export default Backbone.Model.extend({
    url() {
        return Router.generate('_xhr_clients_contact', { 'id': this.id })
    },
    destroy(options) {
        const opts = extend({ url: Router.generate('_xhr_clients_delete_contact', { 'id': this.id }) }, options || {});
        return Backbone.Model.prototype.destroy.call(this, opts);
    }
});
