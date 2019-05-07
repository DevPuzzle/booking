<style>

</style>
<template>
    <b-form @submit.prevent="save">
        <!--<div class="mb-3">
            <button class="btn btn-block text-truncate btn-outline-secondary" @click="event.is_all_day = !event.is_all_day" type="button">
                <span v-if="!event.is_all_day">A FEW HOURS OFF (CLICK TO CHANGE TO ALL DAY OFF)</span>
                <span v-if="event.is_all_day">ALL DAY OFF (CLICK TO CHANGE TO A FEW HOURS OFF)</span>
            </button>
        </div>-->
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>From:</label>
                <date-picker class="btn badge-secondary-bordered-prl" :config="startsAtOptions" id="from_date" v-model="event.starts_at"></date-picker>
            </div>
            <div class="form-group col-md-6">
                <label>To:</label>
                <date-picker class="btn badge-secondary-bordered-prl" :config="endsAtOptions" id="to_date" v-model="event.ends_at"></date-picker>
            </div>
        </div>
        <div class="form-group" style="display: none;">
            <label for="summary">Summary</label>
            <small class="text-muted">(Optional)</small>
            <input type="text" class="form-control" id="description" v-model="event.description">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <small class="text-muted">(Optional)</small>
            <textarea class="form-control" v-model="event.summary" id="summary"></textarea>
        </div>
        <rrule-editor v-model="event.recurrence"></rrule-editor>
        <div class="text-left">
            <button type="submit" class="btn badge-primary-prl">Save</button>
            <button type="button" class="btn badge-secondary-prl" @click="close">Cancel</button>
        </div>
    </b-form>
</template>
<script>
    import TimeRangePicker from '../../components/LeaveDays/TimeRangePicker';
    import RruleEditor from '../../components/LeaveDays/RRuleEditor';

    export default {
        computed: {
            startsAtOptions() {
                return {
                    format: this.event.is_all_day ? 'MM/DD/YYYY' : 'MM/DD/YYYY hh:mm a',
                    stepping: 5,
                    sideBySide: true,
                }
            },
            endsAtOptions() {
                const config = {
                    format: this.event.is_all_day ? 'MM/DD/YYYY' : 'MM/DD/YYYY hh:mm a',
                    stepping: 5,
                    sideBySide: true,
                };
                // if (!this.event.recurrence) {
                //   config.format = this.event.is_all_day ? 'MM/DD/YYYY' : 'MM/DD/YYYY hh:mm a'
                // } else {
                //   config.format = 'hh:mm a'
                // }

                return config
            },
        },
        components: {
            TimeRangePicker,
            RruleEditor,
        },
        data() {
            return {
                event: {
                    event_id: '',
                    read_id: '',
                    write_id: '',
                    html_link: '',
                    summary: '',
                    description: '',
                    starts_at: '',
                    ends_at: '',
                    recurrence: '',
                    recurring_last_instance_date: '',
                    user_id: '',
                    added_by: '',
                    created_at: '',
                    updated_at: '',
                    write_at: '',
                    read_at: '',
                    deleted_at: '',
                    is_all_day: true,
                    user: {
                        id: '',
                        name: '',
                        username: '',
                        role: '',
                        phone_no: '',
                        email: '',
                        created_at: '',
                        updated_at: '',
                    }
                }
            }
        },
        created() {
            this.event = _.cloneDeep(this.value);
            this.event.starts_at = moment(this.event.starts_at).format('MM/DD/YYYY hh:mm a')
            this.event.ends_at = moment(this.event.ends_at).format('MM/DD/YYYY hh:mm a')
        },
        methods: {
            save() {
                const data = _.cloneDeep(this.event);
                data.starts_at = moment(data.starts_at).format('YYYY-MM-DD HH:mm:ss');
                data.ends_at = moment(data.ends_at).format('YYYY-MM-DD HH:mm:ss');
                axios({
                    method: this.event.id ? 'PUT' : 'POST',
                    data,
                    url: this.event.id ? `/leave-days/${ this.event.id }` : '/leave-days',
                })
                    .then(() => {
                        this.$emit('updated');
                    }).catch(() => {
                    this.$notify({
                        group: 'notifications',
                        type: 'error',
                        title: 'Error',
                        text: 'An Error Occurred. Please try again.'
                    });
                });
            },
            close() {
                this.$emit('cancel')
            }
        },
        props: {
            value: {}
        },
        watch: {
            'event.is_all_day': {
                immediate: true,
                handler(allDay) {
                    this.event.starts_at = moment(this.event.starts_at).format(this.startsAtOptions.format);
                    this.event.ends_at = moment(this.event.ends_at).format(this.endsAtOptions.format);
                },
            },
            'event.starts_at'(date) {
                if (date && date !== 'Invalid date') {
                    $('#max_date').data("DateTimePicker").minDate(date);
                }
            },
            'event.ends_at'(date) {
                if (date && date !== 'Invalid date') {
                    $('#min_date').data("DateTimePicker").maxDate(date);
                }
            },
        }
    }
</script>