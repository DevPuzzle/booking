<template>
    <div style="display:inline">
        <a href="#edit" data-toggle="collapse">edit</a>
        <div id="edit" class="collapse">
            <date-picker v-model="date" id="startDate"></date-picker>
            <button class="btn btn-sm btn-primary" @click="schedule()">Shedule</button>
        </div>
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['page_id'],
        data() {
            return {
                date: '',
                published_at: ''
            }
        },
        watch: {
            published_at() {
                this.$emit('publishedAt', this.published_at);
            }
        },
        methods: {
            schedule() {
                axios.post('/admin/pages/'+this.page_id+'/schedule', {date:this.date}).then(response =>  {
                    this.published_at = response.data.published_at;
                });
            }
        }
    }
</script>
