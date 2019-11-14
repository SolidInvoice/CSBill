import Backbone from 'backbone';
import { each, isEmpty, isUndefined } from 'lodash';

export default Backbone.Collection.extend({
    footerModel: null,
    discountModel: null,
    initialize (models, options) {
        this.footerModel = options.footerModel;
        this.discountModel = options.discountModel;
        this.listenTo(this, 'change reset add remove', this.updateTotals);
    },
    updateTotals () {
        let total = 0,
            tax = 0,
            subTotal = 0;

        each(this.models, (model) => {
            let rowTotal = model.get('total'),
                rowTax = model.get('tax');

            if (isUndefined(rowTotal)) {
                return;
            }

            total += rowTotal;
            subTotal += rowTotal;

            if (!isEmpty(rowTax) && !isUndefined(rowTax.type)) {
                let taxAmount = 0;

                if ('inclusive' === rowTax.type.toLowerCase()) {
                    taxAmount = ( rowTotal / ( parseFloat(rowTax.rate / 100) + 1 ) - rowTotal ) * -1;
                    subTotal -= taxAmount;
                } else {
                    taxAmount = rowTotal * parseFloat(rowTax.rate / 100);
                    total += taxAmount;
                }

                tax += taxAmount;
            }
        });

        let discount = 0,
            footerModel = this.footerModel;

        if (this.discountModel.get('value') > 0) {
            if ('money' === this.discountModel.get('type')) {
                discount = this.discountModel.get('value');
            } else {
                discount = ( total * this.discountModel.get('value') ) / 100;
            }
        }

        footerModel.set('subTotal', subTotal);
        footerModel.set('total', total - discount);
        footerModel.set('discount', discount);
        footerModel.set('tax', tax);

        this.trigger('update:totals');
    }
});
