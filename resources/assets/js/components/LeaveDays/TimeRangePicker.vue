<style>

</style>
<template>
  <div class="form-row">
    <div class="form-group col-md-6 d-flex align-items-create">
      <label class="mr-4">{{fromLabel}}: </label>
      <date-picker :config="startsAtOptions" id="min_date" :value="min_date" @input="updateFrom" ref="minDate"></date-picker>
    </div>
    <div class="form-group col-md-6 d-flex align-items-center">
      <label class="mr-4">{{toLabel}}: </label>
      <date-picker :config="endsAtOptions" id="max_date" :value="max_date" @input="updateTo" ref="maxDate"></date-picker>
    </div>
  </div>
</template>
<script>
  export default {
    computed: {
      startsAtOptions() {
        return {
          format: this.hideTime ? 'MM/DD/YYYY' : 'MM/DD/YYYY hh:mm a',
          sideBySide: true,
          stepping: 5,
        }
      },
      endsAtOptions() {
        return {
          format: this.hideTime ? 'MM/DD/YYYY' : 'MM/DD/YYYY hh:mm a',
          sideBySide: true,
          stepping: 5,
        }
      },
    },
    created() {
      this.min_date = this.from;
      this.max_date = this.to;
    },
    methods: {
      updateFrom(date) {
        this.min_date = date;
        this.$emit('fromUpdated', date);
        if (date) {
          $('#max_date').data("DateTimePicker").minDate(date);
        }
      },
      updateTo(date) {
        this.max_date = date;
        this.$emit('toUpdated', date);
        if (date) {
          $('#min_date').data("DateTimePicker").maxDate(date);
        }
      }
    },
    data() {
      return {
        min_date: '',
        max_date: '',
      }
    },
    props: {
      hideTime: {
        type: Boolean,
        default: false
      },
      from: {
        default: () => moment().format('MM/DD/YYYY hh:mm a'),
      },
      fromLabel: {
        default: 'From'
      },
      to: {
        default: () => moment().format('MM/DD/YYYY hh:mm a'),
      },
      toLabel: {
        default: 'To'
      },
    },
    watch: {
      from() {
        this.min_date = this.from;
      },
      to() {
        this.max_date = this.to;
      },
      hideTime() {
        this.$emit('fromUpdated', moment(this.min_date).format(this.endsAtOptions.format));
        this.$emit('toUpdated', moment(this.max_date).format(this.endsAtOptions.format));
      }
    },
  }
</script>