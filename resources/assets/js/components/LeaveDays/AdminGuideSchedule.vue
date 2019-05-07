<template>
    <div>
        <div class="d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <p class="lead my-0 mr-1 ">Dates</p>
                <div class="col-md-3">
                    <date-picker :config="startTimeOptions" v-model="startDate"></date-picker>
                </div>
                <span class="mx-2"> - </span>
                <div class="col-md-3">
                    <date-picker class="datetime" :config="endTimeOptions" v-model="endDate"></date-picker>
                </div>
                <span class="mx-2">&nbsp;</span>
                <button class="btn btn-default" @click="getLeaveDays()">Get</button>
            </div>
        </div>
        <div class="card">
            <div class="list-group list-group-flush">
                <div class="list-group-item " v-for="(leaveDay, date) in leaveDays">
                    <div class="row d-flex align-items-stretch">
                        <div class="col-lg-1 col-md-2 col-3">{{ date | dateFormat('DD-MM-YYYY','ddd, Do MMM')}}</div>
                        <div class="col-lg-11 col-md-10 col-9">
                            <div v-for="event in leaveDay" class="row mb-2">
                                <div class="col-3">
                                <span class="mr-1 badge badge-success rounded-circle"
                                      style="height: 14px; width: 14px; display: inline-block;">&nbsp;</span>
                                    <span><a target="_blank" :href="`leave-days/user/${event.user.username}`"> {{event.user.name}}:&nbsp;</a></span>
                                    <span v-if="event.is_all_day">All Day</span>
                                    <span v-if="!event.is_all_day">{{event.starts_at | dateFormat('DD-MM-YYYY HH:mm:ss','hh:mm a')}} - {{event.ends_at | dateFormat('DD-MM-YYYY HH:mm:ss','hh:mm a')}}</span>
                                </div>
                                <div class="col-auto">{{event.summary}}</div>
                                <small v-if="event.recurrence" class="col-12"><span class="icon-refresh"></span>
                                    {{event.human_readable_recurrence }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <button @click="loadMore()" class="btn btn-link" v-if="hasMore">Load More</button>
            <span v-if="!hasMore">No More Leave Days In Range</span>
        </div>
    </div>
</template>

<script>
    export default {
        computed: {
            getBaseUrl() {
                return `${window.location.origin}/api/leave-days/`;
            },
            startTimeOptions() {
                return {
                    format: 'MM/DD/YYYY'
                }
            },
            endTimeOptions() {
                return {
                    format: 'MM/DD/YYYY',
                    minDate: moment(this.startDate, 'MM/DD/YYYY'),
                }
            }
        },
        created() {
            this.getLeaveDays();
        },
        data() {
            return {
                startDate: moment().format('MM/DD/YYYY'),
                endDate: moment().add(1, 'w').format('MM/DD/YYYY'),
                currentPage: 1,
                totalPages: 1,
                leaveDays: [],
                perPage: 10,
                hasMore: false,
            }
        },
        methods: {
            getLeaveDays() {
                this.currentPage = 1;
                axios.get(`${this.getBaseUrl}?page=${this.currentPage}&perPage=10` +
                    `&startDate=${this.startDate}&endDate=${this.endDate}`)
                    .then((resp) => {
                        this.leaveDays = resp.data.data;
                        this.currentPage = resp.data.current_page;
                        this.totalPages = resp.data.last_page;
                        this.hasMore = this.currentPage < this.totalPages
                    });
            },
            loadMore() {
                this.currentPage += 1;
                axios.get(`${this.getBaseUrl}?page=${this.currentPage}&perPage=10` +
                    `&startDate=${this.startDate}&endDate=${this.endDate}`)
                    .then((resp) => {
                        this.leaveDays = _.mergeWith(this.leaveDays, resp.data.data, (objValue, srcValue) => {
                            if (_.isArray(objValue)) {
                                return objValue.concat(srcValue);
                            }
                        });
                        this.currentPage = resp.data.current_page;
                        this.totalPages = resp.data.last_page;
                        this.hasMore = this.currentPage < this.totalPages;
                        this.$forceUpdate();
                    })
            }
        }
    }
</script>