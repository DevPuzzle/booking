import { rrulestr } from 'rrule';

export default {
  strToRuleset(rrule, dtstart) {
    rrule = _.isArray(rrule) ? rrule.join('\n') : rrule;
    rrule = rrule.replace('TZID=America/New_York:', 'VALUE=DATE:');
    return rrulestr(rrule, { forceset: true, dtstart: moment(dtstart).toDate() });
  }
}