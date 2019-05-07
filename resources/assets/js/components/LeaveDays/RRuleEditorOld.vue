<style>

</style>
<template>
  <div>
    <div class="form-group">
      <select class="form-control" v-model="recurrence">
        <option :value="recur.value" v-for="recur in recurrences">{{recur.text}}</option>
      </select>
    </div>
    <div v-if="recurrence">
      <div v-if="recurrence === 'custom'">
        <div class="form-group">
          <label style="display: inline;">Repeat Every: </label>
          <input type="number" class="form-control" v-model="rule.interval" style="display: inline; width: auto" min="1">
          <select class="form-control" v-model="rule.freq" style="display: inline; width: auto">
            <option :value="freq.value" v-for="freq in frequencies">{{freq.text}}</option>
          </select>
          <div v-if="rule.freq === RRule.MONTHLY" style="display: inline;">
            <label>on the</label>
            <select class="form-control" style="display: inline; width: auto" v-model="monthFreq" @change="setMonthFreq()">
              <option value="date">{{ today.format('Do') }}</option>
              <!--<option value="week">{{ weekOfTheMonth(true) }} {{ today.format('dddd') }}</option>-->
            </select>
          </div>
        </div>
        <div v-if="rule.freq === RRule.WEEKLY">
          <label class="form-label">Repeat On: </label>
          <label class="checkbox-inline"> <input type="checkbox" v-model="rule.byweekday" :value="RRule.MO"> Mon</label>
          <label class="checkbox-inline"> <input type="checkbox" v-model="rule.byweekday" :value="RRule.TU"> Tue</label>
          <label class="checkbox-inline"> <input type="checkbox" v-model="rule.byweekday" :value="RRule.WE"> Wed</label>
          <label class="checkbox-inline"> <input type="checkbox" v-model="rule.byweekday" :value="RRule.TH"> Thur</label>
          <label class="checkbox-inline"> <input type="checkbox" v-model="rule.byweekday" :value="RRule.FR"> Fri</label>
          <label class="checkbox-inline"> <input type="checkbox" v-model="rule.byweekday" :value="RRule.SA"> Sat</label>
          <label class="checkbox-inline"> <input type="checkbox" v-model="rule.byweekday" :value="RRule.SU"> Sun</label>
        </div>
      </div>
      <div class="form-group">
        <label>Ends: </label>
        <div class="form-check form-check-inline">
          <input type="radio" class="form-check-input" v-model="until" name="until" id="never" value="" @change="resetUntil">
          <label class="form-check-label" for="never">Never</label>
        </div>
        <div class="form-check form-check-inline">
          <input type="radio" class="form-check-input" v-model="until" name="until" id="date" value="date" @change="resetUntil">
          <label class="form-check-label" for="date">Specific Date</label>
        </div>
        <div class="form-check form-check-inline">
          <input type="radio" class="form-check-input" v-model="until" name="until" id="count" value="count" @change="resetUntil">
          <label class="form-check-label" for="count">After Several Runs</label>
        </div>
        <div class="input-group" v-if="until === 'date'">
          <div class="input-group-prepend">
            <div class="input-group-text">
              Ends On
            </div>
          </div>
          <date-picker class="col-sm-10" :value="rule.until" @input="setUntil" :config="{format: 'MM/DD/YYYY'}"></date-picker>
        </div>
        <div class="input-group" v-if="until === 'count'">
          <div class="input-group-prepend">
            <div class="input-group-text">
              Ends After
            </div>
          </div>
          <input type="number" v-model="rule.count" class="form-control">
          <div class="input-group-append">
            <span class="input-group-text">Occurrences</span>
          </div>
        </div>
      </div>
      <p class="alert alert-info">On leave {{ruleText}}</p>
    </div>
    <pre>{{rrule}}</pre>
    <pre>{{rule}}</pre>
  </div>
</template>
<script>
  import _ from 'lodash';
  import { RRule, rrulestr } from 'rrule';

  export default {
    computed: {
      today() {
        return moment();
      },
      RRule() {
        return RRule;
      },
      frequencies() {
        return [
          {
            value: RRule.HOURLY,
            text: this.rule.interval > 1 ? 'Hours' : 'Hour',
          },
          {
            value: RRule.DAILY,
            text: this.rule.interval > 1 ? 'Days' : 'Day',
          },
          {
            value: RRule.WEEKLY,
            text: this.rule.interval > 1 ? 'Weeks' : 'Week',
          },
          {
            value: RRule.MONTHLY,
            text: this.rule.interval > 1 ? 'Months' : 'Month',
          },
          {
            value: RRule.YEARLY,
            text: this.rule.interval > 1 ? 'Years' : 'Year',
          },
        ];
      },
      recurrences() {
        return [
          {
            value: '',
            text: 'Does not repeat'
          },
          {
            value: 'Every day',
            text: 'Daily'
          },
          {
            value: 'Every weekday',
            text: 'Every weekday(Mon-Fri)'
          },
          {
            value: 'every week on Saturday, Sunday',
            text: 'Every weekend(Sat-Sun)'
          },
          {
            value: moment().format('[Every week on] dddd'),
            text: moment().format('[Weekly on] dddd')
          },
          {
            value: moment().format('[every month on] [the] Do'),
            text: moment().format('[Monthly on the] Do')
          },
          {
            value: moment().format('[every] MMMM [on] [the] Do'),
            text: moment().format('[Yearly on] MMMM D')
          },
          {
            value: 'custom',
            text: 'Custom'
          },
        ];
      },
      ruleText() {
        if (this.recurrence) {
          if (this.recurrence !== 'custom') {
            return RRule.fromText(this.recurrence).toText();
          }
          if (this.recurrence === 'custom' && this.rule.freq !== undefined) {
            return new RRule(this.rule).toText();
          }
        }
        if (this.value) {
          if (this.value.join) {
            return rrulestr(this.value.join('\n')).toText();
          }
          return rrulestr(this.value).toText();
        }
      },
      rrule() {
        if (this.recurrence) {
          if (this.recurrence !== 'custom') {
            return 'RRULE:' + RRule.fromText(this.recurrence).toString().split('\n');
          }
          if (this.rule.freq !== undefined) {
            return 'RRULE:' + new RRule(this.rule).toString().split('\n');
          }
        }
      },

    },
    data() {
      return {
        recurrence: '',
        monthFreq: '',
        until: 'date',
        rule: {
          interval: 1,
          freq: RRule.DAILY,
          byweekday: [],
          bymonthday: []
        },
        exdate: [],
        exrule: [],
      }
    },
    mounted() {
      if (this.value) {
        const rrule = _.isArray(this.value) ? this.value.join('\n') : this.value;
        try {
          const ruleset = rrulestr(rrule, { forceset: true });
          console.log(ruleset._rrule[0].origOptions, rrule);
          this.rule = ruleset._rrule[0].origOptions;
          // if (ruleset._rrule) {
          //   this.recurrence = 'custom';
          //   this.rule = RRule.fromText(ruleset._rrule[0].toText()).origOptions
          //   this.rule = ruleset._rrule[0].origOptions
          //   return;
          //   this.until = '';
          //   if (this.rule.until) {
          //     this.until = 'date';
          //     this.setUntil(this.rule.until);
          //   }
          //   if (this.rule.count) {
          //     this.until = 'count';
          //   }
          // }
          // if (ruleset._exdate.length) {
          //   this.exdate = ruleset._exdate;
          // }
          // if (ruleset._exrule.length) {
          //   this.exrule = ruleset._exrule;
          // }

        } catch (e) {
          console.log(rrule);
          return;
        }
      }
    },
    methods: {
      setUntil(date) {
        this.rule.until = moment(date).toDate();
      },
      resetUntil() {
        this.rule.count = undefined;
        this.rule.until = undefined;
      },
      weekOfTheMonth(ordinal) {
        const week = Math.ceil(this.today.day() / 7);
        if (!ordinal) return week;
        if (week === 1) return 'first';
        if (week === 2) return 'second';
        if (week === 3) return 'third';
        if (week === 4) return 'fourth';
        if (week === 5) return 'fifth';
      },
      setMonthFreq() {
        if (this.monthFreq === 'date') {
          this.rule.byweekday = [];
          this.rule.bymonthday = [this.today.format('D')];
        }
        if (this.monthFreq === 'week') {
          // this.rule.bymonthday = [];
          // this.rule.byweekday = [{
          //     "weekday": parseInt(this.today.format('e')),
          //     "n": this.weekOfTheMonth()
          //   }];
        }
      },
    },
    watch: {
      'rule.freq': {
        handler(current, prev) {
          if (prev !== current) {
            if (current === RRule.MONTHLY) {
              this.monthFreq = 'date';
              this.setMonthFreq();
              return;
            }
            this.rule.byweekday = [];
            this.rule.bymonthday = [];
          }
        },
        immediate: true,
      },
      rule: {
        handler() {
         /* if (this.rrule[0] !== this.value[0]) {
            // this.$emit('input', this.rrule.split('\n'));
          }*/
        },
        deep: true,
      },
      recurrence: {
        handler(val) {
          if (val && val !== ' custom') {
            // this.$emit('input', this.rrule.split('\n'));
          }
        },
      }
    },
    props: {
      value: {}
    }
  }
</script>