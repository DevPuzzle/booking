<style>

</style>
<template>
    <div @click="edit">
        <div v-if="!editing" @click="edit"> {{val}}</div>
        <input v-if="editing" :type="type" class="form-control" v-model="val" @keyup.enter="save" @keyup.esc="cancel" @blur="cancel" autofocus>
        <small  v-if="editing" class="text-muted"> Press enter to save, esc to cancel</small>
        <small  v-if="!editing" class="text-muted"> Click here to edit</small>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                editing: false,
                val: undefined
            }
        },
        methods: {
            cancel() {
                this.val = this.value;
                this.editing = false;
            },
            edit() {
                this.editing = true;
            },
            save() {
                this.editing = false;
                this.$emit('input', this.val, this.date);
            }
        },
        watch: {
            value: {
                immediate: true,
                handler() {
                    this.val = this.value;
                }
            }
        },
        props: {
            value: { },
            type: {
                default: 'text',
            },
            date: { },
        }
    }
</script>
