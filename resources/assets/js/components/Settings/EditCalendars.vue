<template>
    <div>
        <h3>
            Update Calendar Settings
        </h3>
        <small>Current google account: {{this.gcalEmail}}.</small>
        <form @submit.prevent="saveData()">
            <div class="form-group">
                <label for="read_calendar">Read Calendar</label>
                <select class="form-control" id="read_calendar" v-model="calendarData.read_calendar" required>
                    <option selected disabled hidden value="">Choose a calendar to read from.</option>
                    <option v-for="calendar in calendars" :value="{id: calendar.id, summary: calendar.summary}">{{ calendar.summary }}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="write_calendar">Write Calendar</label>
                <select class="form-control" id="write_calendar" v-model="calendarData.write_calendar" required>
                    <option selected disabled hidden value="">Choose a calendar to write to.</option>
                    <option v-for="calendar in calendars" :value="{id: calendar.id, summary: calendar.summary}">{{ calendar.summary }}</option>
                </select>
            </div>
            <button class="btn btn-block btn-secondary" type="submit" :class="{ 'disabled': saving }">
                {{ saving ? 'Saving' : 'Save' }}
            </button>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['calendars', 'updateUrl', 'settingsPagePath', 'gcalEmail'],
        data() {
            return {
                saving: false
            }
        },
        computed: {
            calendarData() {
                return {
                    write_calendar: '',
                    read_calendar: '',
                }
            },
        },
        methods: {
            saveData() {
                this.saving = true;
                let data = this.calendarData;
                axios.put(this.updateUrl, data)
                    .then((response) => {
                        if (response.status) {
                            this.saving = false;
                            this.$notify({
                                group: 'notifications',
                                type: 'success',
                                text: 'Calendar Settings Successfully Updated!'
                            });
                        }
                        window.location.href = this.settingsPagePath;
                    })
                    .catch(() => {
                        this.$notify({
                            group: 'notifications',
                            type: 'error',
                            title: 'Error',
                            text: 'Calendar Settings Not Updated. Please Refresh & Try again!'
                        });
                    });
            }
        },
    }
</script>
