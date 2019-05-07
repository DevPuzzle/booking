<style>

</style>
<template>
    <div>
        <!--<div class="d-flex justify-content-between m-3">
            <h3 v-if="!isAdmin">My Unavailable Days: {{eventsCount}}</h3>
            <h3 v-else>Unavailable Days: {{eventsCount}}</h3>
        </div>-->
        <!--<div class="text-center mb-4">
            <b-btn class="p-3 " v-if="!isAdmin" @click="showCreate" variant="success" block>
                <span class="icon-plus"></span> ADD DAY
            </b-btn>
        </div>-->
        
        
        <div class="row mt-3">
                <div  class="col-sm-3 ">
                    <div v-if="!isAdmin">
                        <h2>Add new</h2>
                        <hr>
                        <leave-day-editor v-model="event" @cancel="showEditor =false;"
                                  @updated="createSuccess"></leave-day-editor>
                    </div>
                    <div v-if="isAdmin">
                        <h2>Filter</h2>
                        <hr>
                        <div class="form-group">
                            <label  class="form-label">Users :</label>
                            <select class="form-control" v-model="sectedUserFilter">
                            <option value="all">All</option>
                            <option v-for="user in usersList" v-bind:value="user.id">
                                {{ user.username }}
                            </option>
                            </select>
                        </div>
                        
                        
                    </div>
                </div>
                <div class="col-sm-9 ">
                    <h2>Select time</h2>
                    <hr>
                    <time-range-picker :from="filters.from.format('MM/DD/YYYY hh:mm a')"
                           :to="filters.to.format('MM/DD/YYYY hh:mm a')"
                           :hide-time="true"
                           @fromUpdated="setFilterStart"
                           @toUpdated="setFilterEnd"></time-range-picker>
                    
                    <h2 v-if="!isAdmin">My Unavailable Days: {{eventsCount}}</h2>
                    <h2 v-else>Unavailable Days: {{eventsCount}}</h2>
                    <hr>
                    <div class=" mb-4 smallFont">
                        <div class="list-group list-group-flush">
                            <div class="row promlead-th-bg p-3 m-0">
                                <div class="col-2 font-weight-bold">Day</div>
                                <div class="col-10">
                                    <div class="row">
                                        <div class="col-3 font-weight-bold"> Time </div>
                                        <div v-if="!isAdmin" class="col-3 font-weight-bold"> Notes </div>
                                        <div v-if="isAdmin" class="col-6 font-weight-bold"> Notes </div>
                                        <div class="col-3 font-weight-bold"> Repeat</div>
                                        <div v-if="!isAdmin" class="col-3 font-weight-bold"> Action</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item promlead-bg m-1" v-for="(day, date) in days" :key="date" v-if="day.length">
                                <div class="row d-flex align-items-stretch m-0 pt-1">
                                    <div class="col-2 col-md-2 pl-0 pt-1">{{ date | dateFormat('DD-MM-YYYY','ddd, Do MMM')}}</div>
                                    <div class="col-10 col-md-10">
                                        <leave-day-slot v-for="event in day" 
                                                        :key="event.id"
                                                        :event="event"
                                                        :parent="events[event.parent_id]"
                                                        :isAdmin="isAdmin"
                                                        @refresh="refresh"></leave-day-slot>
                                    </div>
                                </div>
                            </div>
                        </div>
        </div>  
                </div>
            </div>
        
        
        <!--<b-modal v-model="showEditor" :hide-footer="true" :hide-header="true" size="lg">
            <leave-day-editor v-if="showEditor" v-model="event" @cancel="showEditor =false;"
                              @updated="createSuccess"></leave-day-editor>
        </b-modal>-->

    </div>
</template>
<script>
    import RRule from '../../libs/rrule';
    import TimeRangePicker from '../../components/LeaveDays/TimeRangePicker';
    import RruleEditor from '../../components/LeaveDays/RRuleEditor';
    import LeaveDaySlot from './LeaveDaySlot';
    import LeaveDayEditor from './LeaveDayEditor';

    export default {
        components: {
            LeaveDayEditor,
            LeaveDaySlot,
            TimeRangePicker,
            RruleEditor,
        },
        computed: {
            
            days() {
                const filteredEvents = _.filter(this.events,
                        event => {
                            if( this.sectedUserFilter == 'all' ){
                                return true;
                            }
                            return event.user.id == this.sectedUserFilter;
                        });
                        
                const instances = _.reduce(/*this.events*/ filteredEvents, (events, event) => {
                    const within = this.filters.from.isSameOrBefore(event.starts_at) && this.filters.to.isSameOrAfter(event.ends_at);
                    const endsAfterFrom = this.filters.from.isSameOrBefore(event.starts_at) && this.filters.to.isSameOrAfter(event.starts_at);
                    const startsBeforeTo = this.filters.from.isSameOrBefore(event.ends_at) && this.filters.to.isSameOrAfter(event.starts_at);

                    if (!event.recurrence && (within || endsAfterFrom || startsBeforeTo)) {
                        events.push(event);
                    }

                    if (event.recurrence) {
                        const ruleset = RRule.strToRuleset(event.recurrence, event.starts_at);
                        const duration = moment(event.ends_at).diff(moment(event.starts_at));
                        const exceptions = ruleset._exdate.map(date => moment(date).format('DDMMYYYY'));
                        _(ruleset.between(this.filters.from.toDate(), this.filters.to.toDate()))
                            .filter(date => exceptions.indexOf(moment(date).format('DDMMYYYY')) === -1)
                            .each((date, i) => {
                                events.push(_.defaultsDeep({
                                    id: event.id + '-' + i,
                                    parent_id: event.id,
                                    starts_at: date,
                                    ends_at: moment(date).add(duration)
                                }, _.defaultsDeep(_.cloneDeep(event))));
                            });
                    }
                    return events;
                }, []);
                return _(instances)
                    .sortBy(event => {
                        return moment(event.starts_at).format('YYYYMMDDHHmmss')
                    })
                    .groupBy(event => {
                        return moment(event.starts_at).format('DD-MM-YYYY')
                    })
                    .value();
            },
            endTimeOptions() {
                return {
                    format: 'MM/DD/YYYY',
                    // minDate: moment(this.startDate, 'MM/DD/YYYY')
                }
            },
            startTimeOptions() {
                return {
                    format: 'MM/DD/YYYY'
                }
            },
            eventsCount() {
                return _.values(this.days).length;
            }
        },
        created() {
            // const from = localStorage.getItem('filters_from');
            // if (from && from !== 'Invalid date') {
            //   this.filters.from = moment(from, 'MM/DD/YYYY');
            // }
            // const to = localStorage.getItem('filters_to');
            // if (from && from !== 'Invalid date') {
            //   this.filters.to = moment(to, 'MM/DD/YYYY');
            // }

            this.refresh();

        },
        data() {
            return {
                sectedUserFilter: 'all',
                events: [],
                usersList:[],
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
                },
                filters: {
                    from: moment().subtract(1, 'day'),
                    to: moment().add(1, 'week'),
                },
                showEditor: false,
            }
        },
        methods: {
            createSuccess() {
                this.showEditor = false;
                this.$notify({
                    group: 'notifications',
                    type: 'success',
                    text: 'Leave Day Successfully created!'
                });
                this.event = {}
                this.refresh();
            },
            showCreate() {
                this.showEditor = true;
                this.event = {
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
            },
            refresh() {
                axios.get('/leavedays/' + this.username + '?from=' + this.filters.from.format('YYYY-MM-DD') + '&to=' + this.filters.to.format('YYYY-MM-DD')).then(response => {
                    this.events = _.keyBy(response.data, 'id');
                    this.usersList=_.unionBy(_.map(this.events,function(event){ return event.user; }),
                        function(user){ return user.id;});
                    });
            },
            setFilterStart(date) {
                this.filters.from = moment(date, 'MM/DD/YYYY');
            },
            setFilterEnd(date) {
                this.filters.to = moment(date, 'MM/DD/YYYY');
            },
        },
        props: {
            isAdmin: {},
            username: {
                default: ''
            },
        },
        watch: {
            'filters.from': {
                immediate: true,
                handler() {
                    if (this.filters.from.toString() !== localStorage.getItem('filters_from')) {
                        this.refresh();
                    }
                    if (this.filters.from) {
                        localStorage.setItem('filters_from', this.filters.from.toString())
                    }
                },
            },
            'filters.to': {
                immediate: false,
                handler() {
                    if (this.filters.to.toString() !== localStorage.getItem('filters_to')) {
                        this.refresh();
                    }
                    if (this.filters.to) {
                        localStorage.setItem('filters_to', this.filters.to.toString())
                    }
                },
            },
        }
    }
</script>