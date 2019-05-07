<template>
    <div class="container">
        <h1>Upload  image For Editor</h1>
        <input type="file" @change="onFileChanged">
        <button @click="onUpload">Upload</button>
    </div>
</template>

<script>
import axios from 'axios';

export default{
    data() {
        return {
            selectedFile: null
        }
    },
    methods:{
        onFileChanged (event) {
            this.selectedFile = event.target.files[0];
            console.log(this.selectedFile);
        },
        onUpload(){
            const formData = new FormData();
            formData.append('photo', this.selectedFile);
            console.log(formData.get('photo'));
            axios.post('/admin/pages/upload', formData).then(response=>{
                console.log(response.data);
                var funcNum = getUrlParam( 'CKEditorFuncNum' );
                var fileUrl = response.data;
                window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );
                window.close();
            }).catch(err=>console.log(err));
        },
    }
}
function getUrlParam( paramName ) {
    var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' );
    var match = window.location.search.match( reParam );

    return ( match && match.length > 1 ) ? match[1] : null;
}
</script>
