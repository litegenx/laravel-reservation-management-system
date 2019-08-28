import moment from 'moment';
import store from '../../../store';
import models from './models';
import { arrayToObject } from '../../misc';

const getPrice = item => item.detail.payment - 0;
const getBy = type => {
    if ('daily' === type) {
        return 'days';
    }
    return 'months';
};
const getFormat = type => {
    if ('daily' === type) {
        return 'YYYY-MM-DD';
    }
    return 'YYYY-MM';
};

export default (data) => {
    const start = moment(data[ 'start_date' ]);
    const end = moment(data[ 'end_date' ]);
    const type = data[ 'type' ];
    const model = 'reservations';

    const range = Array.from(moment.range(start, end.subtract(1, 'days')).by(getBy(type)));
    const summary = !range.length ? {} : arrayToObject(range, {
        getKey: ({ item }) => item.format(getFormat(type)),
        getItem: () => 0,
    });
    store.getters[ 'adapter/getAllArray' ](model).filter(item => moment(item[ 'start_date' ]).isBetween(start, end, null, '[)')).map(item => models(model, item)).forEach(item => {
        summary[ moment(item[ 'start_date' ]).format(getFormat(type)) ] += getPrice(item);
    });

    return summary;
}