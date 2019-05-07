<template>
    <ckeditor :config="config" :name="name" v-model="contentText"></ckeditor>
</template>

<script>
import Ckeditor from 'vue-ckeditor2';
import axios from 'axios';
var toolbar = require('../config/toolbar.json');

export default {
    props: {
        name,
        content:{
            required: false,
            default: '',
        }
    },
    components: { Ckeditor },
    data () {
        return {
            config: {
                toolbar : [ toolbar.document, toolbar.clipboard, toolbar.editing, toolbar.basicstyles, toolbar.paragraph, toolbar.links, toolbar.insert ],
                height: 350,
                filebrowserBrowseUrl:'/admin/pages/image/',
                filebrowserUploadUrl:'/admin/pages/image/url',
            },
            contentText: ''
        }
    },
    watch:{
        contentText() {
            this.$emit('contentUpdate', this.contentText);
        },
        content() {
            this.contentText = this.content;
        }
    }
}
</script>
