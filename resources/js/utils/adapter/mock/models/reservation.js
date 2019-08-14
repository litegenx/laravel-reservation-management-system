const moment = require('moment');
import store from '../../../../store';
import guest from './guest';
import room from './room';

export default item => {
    const endDate = moment(item.end_date).add(1, 'days').format('YYYY-MM-DD');
    const checkInTime = store.getters[ 'adapter/getSetting' ]('check_in');
    const checkOutTime = store.getters[ 'adapter/getSetting' ]('check_out');
    return Object.assign({}, item, {
        guest: guest(store.getters[ 'adapter/search' ]('guests', guest => guest.id === item.guest_id)),
        room: room(store.getters[ 'adapter/search' ]('rooms', room => room.id === item.room_id)),
        start_date_str: item.start_date,
        start_datetime: item.start_date + `T${ checkInTime }:00.000+0900`,
        end_date_str: item.end_date,
        end_datetime: endDate + `T${ checkOutTime }:00.000+0900`,
        is_past: moment(endDate + `T${ checkOutTime }:00.000+0900`).isBefore(),
        is_present: !moment(item.start_date + `T${ checkInTime }:00.000+0900`).isAfter() && !moment(endDate + `T${ checkOutTime }:00.000+0900`).isBefore(),
        is_future: moment(item.start_date + `T${ checkInTime }:00.000+0900`).isAfter(),
    });
}