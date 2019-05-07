<template>
  <div>
    <div class="d-flex justify-content-between">
      <div class="d-flex align-items-center">
        <p class="lead my-0 mr-1">Dates</p>
        <div class="col-md-3">
          <date-picker :config="startTimeOptions" v-model="startDate" id="startDate"></date-picker>
        </div>
        <span class="mx-2"> - </span>
        <div class="col-md-3">
          <date-picker class="datetime" :config="endTimeOptions" v-model="endDate" id="endDate"></date-picker>
        </div>
        <span class="mx-2">&nbsp;</span>
        <button class="btn btn-default" @click="getLeaveDays()">Get</button>
      </div>
      <div class=" text-right" v-if="!admin">
        <button type="button" @click="showCreate()" class="btn btn-success">New Leave Day {{showEditor}}</button>
      </div>
    </div>
    <p v-if="admin" class="h3">
      {{userName}}
    </p>
    <div class="card">
      <div class="list-group list-group-flush">
        <div class="list-group-item " v-for="(leaveDay, date) in leaveDays" v-if="leaveDay.length">
          <div class="row d-flex align-items-stretch">
            <div class="col-lg-1 col-md-2 col-3">{{ date | dateFormat('DD-MM-YYYY','ddd, Do MMM')}}</div>
            <div class="col-lg-11 col-md-10 col-9">
              <div v-for="event in leaveDay" class="row mb-3 leave-day-slot">
                <div class="col-12">
                  <span class="mr-1 badge badge-success rounded-circle" style="height: 14px; width: 14px; display: inline-block;">&nbsp;</span>
                  <span v-if="event.is_all_day">All Day</span>
                  <span v-if="!event.is_all_day">{{event.starts_at | dateFormat('DD-MM-YYYY HH:mm:ss','hh:mm a')}} - {{event.ends_at | dateFormat('DD-MM-YYYY HH:mm:ss','hh:mm a')}}</span>
                  <div class="btn-group btn-group-sm" role="group" style="float: right;">
                    <button class="btn btn-secondary" @click="showUpdate(event)">Edit</button>
                    <button class="btn btn-danger" @click="showDelete(event)">Delete</button>
                  </div>
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

    <leave-day-form v-if="showEditor" :show="showEditor" :value="leaveDay" @save="save" @closed="showEditor = false"></leave-day-form>
    <delete-leave-day-modal :leave-day="leaveDay" @delete="handleDelete"></delete-leave-day-modal>

  </div>
</template>

<script>
  import LeaveDayForm from './LeaveDayForm';
  import DeleteLeaveDayModal from './DeleteLeaveDayModal';


  export default {
    components: {
      LeaveDayForm,
      DeleteLeaveDayModal,
    },
    computed: {
      getBaseUrl() {
        if (this.admin) {
          let username = window.location.href.split('/').slice(-1);
          return `${window.location.origin}/api/leave-days/user/${username}`;
        }
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
          // minDate: moment(this.startDate, 'MM/DD/YYYY')
        }
      }
    },
    created() {
      this.admin = this.user.role !== 'guide';
      this.startDate = moment(localStorage.getItem('startDate') || undefined).format('MM/DD/YYYY');
      this.endDate = moment(localStorage.getItem('endDate') || moment().add(1, 'w')).format('MM/DD/YYYY');
      this.getLeaveDays();
    },
    data() {
      return {
        startDate: moment().format('MM/DD/YYYY'),
        endDate: moment().add(1, 'w').format('MM/DD/YYYY'),
        currentPage: 1,
        totalPages: 1,
        leaveDays: [],
        leaveDay: {},
        perPage: 10,
        userName: {},
        admin: false,
        showEditor: false,
        hasMore: false,
      }
    },
    methods: {
      showCreate() {
        this.leaveDay = {
          summary: '',
          description: '',
          starts_at: '',
          ends_at: '',
          is_all_day: false,
          recurrence: '',
        };
        this.showEditor = true;
        console.log(this.showEditor);
      },
      showUpdate(event) {
        this.leaveDay = _.cloneDeep(event);
        this.leaveDay.starts_at = moment(this.leaveDay.starts_at).format('MM/DD/YYYY hh:mm a');
        this.leaveDay.ends_at = moment(this.leaveDay.ends_at).format('MM/DD/YYYY hh:mm a');
        this.showEditor = true;
      },
      save(event) {
        event.starts_at = moment(event.starts_at).format('YYYY-MM-DD HH:mm:ss');
        event.ends_at = moment(event.ends_at).format('YYYY-MM-DD HH:mm:ss');
        if (event.id) {
          this.createEvent(event)
        }
        else {
          this.updateEvent(event)
        }
      },
      createEvent(event) {

        axios.post(window.location.origin + '/leave-days', event).then(() => {
          this.$notify({
            group: 'notifications',
            type: 'success',
            text: 'New Leave Day Created!'
          });
          window.location.reload();
        }).catch(() => {
          this.$notify({
            group: 'notifications',
            type: 'error',
            title: 'Error',
            text: 'An Error Occurred. Please try again.'
          });
        })
      },
      updateEvent(event) {
        // let prevInstance = _.find(leaveDays, this.event);
        // TODO: Find a way to update specific instance of leave day only.
        // This will remove need of a refresh.
        axios.put(`${window.location.origin}/leave-days/${event.id}`, event).then(() => {
          this.$notify({
            group: 'notifications',
            type: 'success',
            text: 'Leave Day Successfully Updated!'
          });
          window.location.reload();
        }).catch(() => {
          this.$notify({
            group: 'notifications',
            type: 'error',
            title: 'Error',
            text: 'An Error Occurred. Please try again.'
          });
        })
      },
      getLeaveDays() {
        this.currentPage = 1;
        axios.get(`${this.getBaseUrl}?page=${this.currentPage}&perPage=10` +
          `&startDate=${this.startDate}&endDate=${this.endDate}`)
          .then((resp) => {
            this.userName = resp.data.user_name;
            this.leaveDays = resp.data.leave_days.data;
            this.currentPage = resp.data.leave_days.current_page;
            this.totalPages = resp.data.leave_days.last_page;
            this.hasMore = this.currentPage < this.totalPages
          });
      },
      loadMore() {
        this.currentPage += 1;
        axios.get(`${this.getBaseUrl}?page=${this.currentPage}&perPage=10` +
          `&startDate=${this.startDate}&endDate=${this.endDate}`)
          .then((resp) => {
            this.leaveDays = _.mergeWith(this.leaveDays, resp.data.leave_days.data, (objValue, srcValue) => {
              if (_.isArray(objValue)) {
                return objValue.concat(srcValue);
              }
            });
            this.currentPage = resp.data.leave_days.current_page;
            this.totalPages = resp.data.leave_days.last_page;
            this.hasMore = this.currentPage < this.totalPages;
            this.$forceUpdate();
          })
      },
      showDelete(leaveDay) {
        this.leaveDay = leaveDay;
        $('#deleteModal').modal('show');
      },
      handleDelete() {
        axios.delete(`leave-days/${this.leaveDay.id}`).then(() => {
          this.$notify({
            group: 'notifications',
            type: 'success',
            text: 'Leave Day Successfully Deleted!'
          });
          this.leaveDays[moment(this.leaveDay.starts_at).format('DD-MM-YYYY')].splice(_.findIndex(this.leaveDays, { id: this.leaveDay.id }), 1);
        }).catch(error => {
          this.$notify({
            group: 'notifications',
            type: 'error',
            text: 'Unable to Delete Leave Day! Please Try Again.'
          });
        })
      }
    },
    watch: {
      startDate() {
        localStorage.setItem('startDate', this.startDate);
      },
      endDate() {
        localStorage.setItem('endDate', this.endDate);
      },

    },
    props: ['user'],

  }
</script>

<style scoped>
  .card {
    margin-top: 20px;
  }
</style>
