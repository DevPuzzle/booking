<template>
  <div id="editorModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog  modal-lg" role="document">
      <div class="modal-content">
        <form @submit.prevent="handleSave">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">{{ event.summary ? event.summary: 'Leave Day Form'
              }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <time-range-picker :from="event.starts_at" :to="event.ends_at" :hide-time="event.is_all_day" @fromUpdated="setStartDate"
                               @toUpdated="setEndDate"></time-range-picker>
            <label class="checkbox"><input type="checkbox" v-model="event.is_all_day"> All day</label>
            <rrule-editor v-model="event.recurrence"></rrule-editor>
            <pre>{{event.recurrence}}</pre>
            <div class="form-group">
              <label for="summary">Summary</label>
              <small class="text-muted">(Optional)</small>
              <input type="text" class="form-control" id="summary" v-model="event.summary">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <small class="text-muted">(Optional)</small>
              <textarea class="form-control" v-model="event.description" id="description"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
  import TimeRangePicker from './TimeRangePicker';
  import RruleEditor from './RRuleEditor';

  export default {
    components: {
      TimeRangePicker,
      RruleEditor,
    },
    data() {
      return {
        event: {
          summary: '',
          description: '',
          starts_at: '',
          ends_at: '',
          is_all_day: false,
          recurrence: '',
        }
      }
    },
    methods: {
      setStartDate(date) {
        this.event.starts_at = date;
      },
      setEndDate(date) {
        this.event.ends_at = date;
      },
      handleSave() {
        this.$emit('save', this.event);
      },
    },
    mounted() {
      console.log('mounted', this.value);
      $('#editorModal').modal('show');
      $('#editorModal').on('hidden.bs.modal', e => {
        this.$emit('closed');
      });
    },
    props: {
      value: {},
      show: { type: Boolean, default: false }
    },
    watch: {
      value: {
        immediate: true,
        handler(val) {
          this.event = val;
        }
      },
      show: {
        immediate: true,
        handler(show) {
          if (show) {
            console.log('show', show);
          } else {
            console.log('hide', show);
          }
        }
      },
    }
  }
</script>
