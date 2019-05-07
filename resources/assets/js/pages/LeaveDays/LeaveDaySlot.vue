<style>

</style>
<template>
    
  <div class="row mb-3 leave-day-slot">
    <div class="col-3 p-1">
      <span v-if="isAdmin" class="d-inline-flex"><b>{{event.user.username}}</b> : </span>
      <span v-if="event.is_all_day"  class="d-inline-flex">All Day</span>
      <span v-if="!event.is_all_day" class="d-inline-flex text-center">
        {{event.starts_at | dateFormat('DD-MM-YYYY HH:mm:ss','hh:mm a')}}-{{event.ends_at | dateFormat('DD-MM-YYYY HH:mm:ss','hh:mm a')}}
      </span>
    </div>
       
    <div v-if="!isAdmin" class="col-3 text-truncate p-1">{{event.summary}}</div>
    <div v-if="isAdmin" class="col-6 text-truncate p-1">{{event.summary}}</div>
    <div class="col-3 p-1"> 
        <span v-if="event.recurrence" class="d-inline-flex text-center">
           {{ recurrence }}
        </span>
     </div> 
    <div v-if="!isAdmin" class="col-3 text-right">
      <div v-if="moment(event.ends_at).isBefore()" role="group">
        <button type="button" class="btn btn-sm badge-primary-prl smallFont " disabled>Edit</button>
        <button class="btn badge-danger-prl btn-sm smallFont " @click="showDelete(event)" >Delete</button>
      </div>
      <div v-else>
        <button type="button" class="btn btn-sm badge-primary-prl smallFont " @click="showUpdate">Edit</button>
        <button class="btn badge-danger-prl btn-sm smallFont " @click="showDelete(event)">Delete</button>
      </div>
    </div>
    <b-modal v-model="showEditor" :hide-footer="true" :hide-header="true">
      <leave-day-editor v-if="showEditor" v-model="parent ? parent : event" @cancel="showEditor =false;" @updated="updateSuccess"></leave-day-editor>
    </b-modal>
    <b-modal v-model="confirmDelete" @ok="destroy" ok-title="Delete" ok-variant="danger" title="Warning! Deleting Day Out"
             header-bg-variant="danger">
      <p class="lead">Are you sure you want to delete this day out <strong>{{event.summary}}</strong> ?</p>
      <div v-if="event.recurrence">
        <div class="form-check">
          <input type="radio" class="form-check-input" v-model="deleting" value="single" id="single" name="single">
          <label for="single" class="form-check-label">This event only</label></div>
        <div class="form-check">
          <input type="radio" class="form-check-input" v-model="deleting" value="future" id="future" name="future">
          <label for="future" class="form-check-label">This and following events</label></div>
        <div class="form-check">
          <input type="radio" class="form-check-input" v-model="deleting" value="all" id="all" name="all">
          <label for="all" class="form-check-label">All events</label></div>
      </div>
      <pre>{{deleting}}</pre>
    </b-modal>
  </div>
    
</template>
<script>
  import RRule from '../../libs/rrule';
  import LeaveDayEditor from './LeaveDayEditor';

  export default {
    components: {
      LeaveDayEditor,
    },
    computed: {
      recurrence() {
        const ruleset = RRule.strToRuleset(this.event.recurrence)
        if (ruleset._rrule.length) {
          return ruleset._rrule[0].toText();
        }
        return 'single';
      }
    },
    data() {
      return {
        deleting: undefined,
        showEditor: false,
        confirmDelete: false,
      }
    },
    methods: {
      showUpdate() {
        this.showEditor = true;
      },
      updateSuccess() {
        this.showEditor = false;
        this.$notify({
          group: 'notifications',
          type: 'success',
          text: 'Leave Day Successfully Updated!'
        });
        this.$emit('refresh')
      },
      showDelete() {
        this.confirmDelete = true;
      },
      destroy() {
        this.confirmDelete = false;
        let method = 'DELETE';
        const data = {};

        if (this.event.recurrence) {
          if (this.deleting !== 'all') {
            method = 'PUT';
            const ruleset = RRule.strToRuleset(this.event.recurrence);
            if (this.deleting === 'single') {
              ruleset.exdate(moment(this.event.starts_at).toDate());
            } else {
              ruleset._rrule[0].origOptions.until = moment(this.event.starts_at).subtract(1, 'day').toDate();
            }
            data.recurrence = ruleset.valueOf();
          }
        }
        axios({
          url: '/leave-days/' + this.event.parent_id || this.event.id,
          data,
          method,
        }).then(() => {
          this.$notify({
            group: 'notifications',
            type: 'success',
            text: 'Leave Day Successfully Deleted!'
          });
          this.$emit('refresh')
        }).catch(() => {
          this.$notify({
            group: 'notifications',
            type: 'error',
            title: 'Error',
            text: 'An Error Occurred. Please try again.'
          });
          });
      },
    },
    props: {
      event: {},
      parent: {},
      isAdmin: {
        type: Boolean,
        default: false
      }
    }
  }
</script>