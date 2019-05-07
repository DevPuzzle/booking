<template>
    <div class="container">
        <b-table responsive :items="users" :fields="fields" :fixed="fixed" :small="small" :hover="hover"></b-table>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        data () {
            return {
                fields: [
                    {
                        key: 'name',
                        sortable: true,
                    },
                    {
                        key:'username', 
                        sortable: true,
                    }, 
                    {
                        Key: 'email', 
                        sortable: true, 
                    },
                    { 
                        key: 'role.name',
                        label: 'Role',
                        sortable: false
                    },
                    {
                        key: 'region.name',
                        label: 'Region',
                        sortable: false
                    }
                ], 
                small: 'small',
                hover: 'hover',
                fixed: 'fixed',
                users: []
            }
        }, 
        mounted() {
            axios.get('http://torontour.test/admin/users/all').then(response => {
                console.log(response.data);
                this.users = response.data;
            });
        }
    }
</script>
