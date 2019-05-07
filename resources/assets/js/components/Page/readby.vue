<template>
    <button class="btn btn-outline-info" v-if="!this.read" @click="markasread()">Mark as Read</button>
    <p v-else style="color:#00c3cc;text-transform:uppercase">Marked as Read</p>
</template>

<script>
    import  axios from 'axios';

    export default {
        props: ['page_id'],
        data(){
            return  {
                read: ''
            }
        },
        mounted() {
            axios.get('/user/page/'+this.page_id).then(response => {
                this.read = response.data;
            }).catch(err=>console.log(err));
        },
        methods: {
            markasread() {
                axios.get('/user/page/'+this.page_id+'/markasread').then(response=>{
                    this.read = response.data;
                }).catch(err =>console.log(err));
            }
        }
    }
</script>
