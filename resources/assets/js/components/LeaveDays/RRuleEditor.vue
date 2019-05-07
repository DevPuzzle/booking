<style>

</style>
<template>
  <div>
    <div class="form-group">
      <label>Recurs:</label>
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
          <input type="radio" class="form-check-input" v-model="until" id="never" value="never" @change="resetUntil">
          <label class="form-check-label" for="never">Never</label>
        </div>
        <div class="form-check form-check-inline">
          <input type="radio" class="form-check-input" v-model="until" id="date" value="date" @change="resetUntil">
          <label class="form-check-label" for="date">Specific Date</label>
        </div>
        <div class="form-check form-check-inline">
          <input type="radio" class="form-check-input" v-model="until" id="count" value="count" @change="resetUntil">
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
  </div>
</template>
<script>
  import _ from 'lodash';
  import { RRuleSet, RRule, rrulestr } from 'rrule';

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
        ];
      },
      recurrences() {
        return [
          {
            value: '',
            text: 'Does not repeat'
          },
          /*  {
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
            },*/
          {
            value: 'custom',
            text: 'Repeats every'
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
          const rruleset = new RRuleSet();

          this.exdate.forEach(exdate => rruleset.exdate(exdate));

          if (this.recurrence) {
            if (this.recurrence !== 'custom') {
              rruleset.rrule(RRule.fromText(this.recurrence));
            }
            if (this.recurrence === 'custom' && this.rule.freq !== undefined) {
              rruleset.rrule(new RRule(this.rule));
            }
            return rruleset.valueOf();
          }
        }
        return null;
      },

    },
    data() {
      return {
        recurrence: '',
        monthFreq: '',
        until: 'never',
        rule: {
          interval: 1,
          freq: RRule.DAILY,
          byweekday: [],
          bymonthday: [],
          until: null,
          count: null,
        },
        exdate: [],
        exrule: [],
      }
    },
    methods: {
      resetUntil() {
        if (this.until === 'date') {
          this.rule.count = null;
        }
        if (this.until === 'count') {
          this.rule.until = null;
        }
        if (this.until === 'never') {
          this.rule.count = null;
          this.rule.until = null;
        }
        console.log(this.until, this.rule.until, this.rule.count)
      },
      setUntil(date) {
        if (date) {
          this.rule.until = moment(date).toDate()
          console.log(date)
        }
      },
      untilParser() {
        if (this.rule.until) {
          this.until = 'date';
          this.$set(this.rule, 'count', null);
          return;
        }
        if (this.rule.count) {
          this.until = 'count';
          this.$set(this.rule, 'until', null);
          ;
          return;
        }
        this.until = 'never';
        this.$set(this.rule, 'until', null);
        ;
        this.$set(this.rule, 'count', null);
        ;
      },
      weekdayParser() {
        if (this.rule.byweekday && this.rule.byweekday.length) {
          const map = {
            0: RRule.MO,
            1: RRule.TU,
            2: RRule.WE,
            3: RRule.TH,
            4: RRule.FR,
            5: RRule.SA,
            6: RRule.SU,
            7: RRule.SU,
          };
          this.rule.byweekday = this.rule.byweekday.map(day => {
            const parsed = map[day.weekday];
            if (day.n) {
            }
            return parsed;
          })
        }
      },
    },
    mounted() {
      if (this.value) {
        let rrule = _.isArray(this.value) ? this.value.join('\n') : this.value;

        rrule = rrule.replace('TZID=America/New_York:', 'VALUE=DATE:');
        try {
          const ruleset = rrulestr(rrule, { forceset: true });
          if (ruleset._rrule) {
            this.recurrence = 'custom';
            this.rule = ruleset._rrule[0].origOptions;
            this.untilParser();
            this.weekdayParser();
            this.rule.interval = this.rule.interval || 1;
          }
          if (ruleset._exdate.length) {
            this.exdate = ruleset._exdate;
          }
          if (ruleset._exrule.length) {
            this.exrule = ruleset._exrule;
          }
        } catch (e) {
          console.log(rrule);
          console.error(e)
        }
      }
    },
    props: {
      value: {}
    },
    watch: {
      rule: {
        deep: true,
        handler() {
          this.$emit('input', this.rrule)
        }
      },
      recurrence: {
        deep: true,
        handler() {
          this.$emit('input', this.rrule)
        }
      },

    }
  }
</script>