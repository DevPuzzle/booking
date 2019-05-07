<template>
    <nav v-if="data.current_page">
        <ul class="pagination justify-content-center">
            <li class="page-item" :class="{ 'disabled' : !hasPrevious() }">
                <a class="page-link" tabindex="-1" @click="$emit('previous')">&laquo;</a>
            </li>
            <li v-if="left[0] !== 1" :class="{ 'disabled' : data.current_page === 1 }" class="page-item">
                <a @click="$emit('page', 1)" class="page-link">First</a>
            </li>
            <li v-for="no in left" class="page-item">
                <a @click="$emit('page', no)" class="page-link">{{no}}</a>
            </li>
            <li class="page-item active" v-if="data.current_page">
                <a class="page-link">{{data.current_page}}</a>
            </li>
            <li v-for="no in right" class="page-item">
                <a @click="$emit('page', no)" class="page-link">{{no}}</a>
            </li>
            <li v-if="right[-1] !== data.last_page" :class="{ 'disabled' : data.current_page === data.last_page }" class="page-item">
                <a @click="$emit('page', data.last_page)" class="page-link">Last</a>
            </li>
            <li class="page-item" :class="{ 'disabled' : !hasNext() }">
                <a class="page-link" @click="$emit('next')">&raquo;</a>
            </li>
        </ul>
    </nav>
</template>

<script>
    export default {
        props: ['data'],
        computed: {
            left(){
                let end = this.data.current_page-2;
                end = end < 1 ? 1 : end;
                return _.range(this.data.current_page-1, end-1, -1).reverse()
            },
            right(){
                let end = this.data.current_page+2;
                end = end > this.data.last_page ? this.data.last_page : end;
                return _.range(this.data.current_page+1, end+1, 1)
            }
        },
        methods: {
            hasNext(){
                return this.data.current_page < this.data.last_page
            },
            hasPrevious(){
                return this.data.current_page > 1
            }
        }
    }
</script>