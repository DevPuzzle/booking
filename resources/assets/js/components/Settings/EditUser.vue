<template>
    <div>
        <h3>
            Profile Settings
        </h3>
        <form>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" v-model="userData.name"/>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" v-model="userData.username"/>
            </div>
            <div class="form-group">
                <label for="phone_no">Phone No</label>
                <input type="number" class="form-control" id="phone_no" v-model="userData.phone_no"/>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" disabled="disabled" class="form-control" id="email" aria-describedby="emailHelp"
                       :value="user.email">
                <small id="emailHelp" class="form-text text-muted">You cannot change your email.</small>
            </div>
            <button class="btn btn-block btn-secondary" :class="{ 'disabled': saving }" @click.prevent="saveData()">
                {{ saving ? 'Saving' : 'Save' }}
            </button>
        </form>
    </div>
</template>

<script>
    export default {
        props: ['user', 'updateUrl'],
        data() {
            return {
                saving: false
            }
        },
        computed: {
            userData() {
                return {
                    name: this.user.name,
                    username: this.user.username,
                    phone_no: this.user.phone_no,
                }
            },
        },
        methods: {
            saveData() {
                this.saving = true;
                let data = this.userData;
                axios.put(this.updateUrl, data)
                    .then((response) => {
                        if (response.status) {
                            this.saving = false;
                            this.$notify({
                                group: 'notifications',
                                type: 'success',
                                text: 'Profile Successfully Updated!'
                            });
                        }
                    })
                    .catch(() => {
                        this.$notify({
                            group: 'notifications',
                            type: 'error',
                            title: 'Error',
                            text: 'Profile Not Updated. Please Try Again!'
                        });
                    });
            }
        },
        created() {
            console.log(this.user)
        }

    }
</script>
