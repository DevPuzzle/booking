<template>
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-3">
               <h3>Page options</h3> 
               <hr> 
               <div class="card">
                    <div class="card-body">
                        <button class="btn btn-primary btn-block" @click="create">Create page</button>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <h4 class="card-title">Which roles can access:</h4>
                        <div class="form-check" v-for="r in roles">
                            <input type="checkbox" class="form-check-input" :id=r.slug v-model="role" :value=r.id>
                            <label class="form-check-label" :for=r.slug>{{ r.name }}</label><br>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <h4 class="card-title">Which regions can access:</h4>
                        <div class="form-check" v-for="r in regions">
                            <input type="checkbox" class="form-check-input" :id=r.slug v-model="region" :value=r.id>
                            <label class="form-check-label" :for=r.slug>{{ r.name }}</label><br>
                        </div>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <h4 class="card-title">Page Categories</h4>
                        <div style="max-height: 100px; overflow: auto">
                            <div class="form-check" v-for="category in categories">
                                <input type="checkbox" class="form-check-input" :id=category.name
                                       v-model="categoryfield"
                                       :value=category.id>
                                <label class="form-check-label" :for=category.name>{{ category.name }}</label><br>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <label class="form-control-label">Add New Category</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" v-model="category"
                                       placeholder="Enter category name"
                                       @keys.enter="addCategory">
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-sm" @click="addCategory">Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <h3>New page</h3> 
               <hr> 
               <form action="/admin/pages" method="post">
                    <div class="form-group">
                        <label>Page Title :</label>
                        <input type="text" class="form-control" v-model="formdata.title"/><br>
                    </div>
                    
                    <div class="form-group">
                        <label>Page Summary :</label>
                        <textarea v-model="formdata.summary" class="form-control"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label >Page Content :</label>
                        <ck-editor v-model="formdata.content" name="content" @contentUpdate="contentUpdate"></ck-editor>
                    </div>
                </form>
            </div>
            
        </div>
                

    </div>
</template>

<script>
    export default {
        data() {
            return {
                formdata: {},
                role: [],
                region: [],
                roles: [],
                regions: [],
                categories: [],
                category: '',
                categoryfield: [],
                date: ''
            }
        },
        mounted() {
            axios.get('/admin/roles').then(response => {
                this.roles = response.data;
            });
            axios.get('/admin/regions').then(response => {
                this.regions = response.data;
            });
            axios.get('/admin/categories').then(response => {
                for (var i = 0; i < response.data.categories.length; i++) {
                    this.categories.push({
                        'name': response.data.categories[i].name,
                        'id': response.data.categories[i].id
                    });
                }
            });
        },
        methods: {
            create() {
                var data = Object.assign(this.formdata, {role: this.role}, {region: this.region}, {category: this.categoryfield}, {date: this.date});
                axios.post('/admin/pages', data).then(response => {
                    console.log(response.data);
                    var url = '/admin/pages/' + response.data.id + '/edit';
                    window.location.href = url;
                }).catch(err => {
                    this.$notify({
                        group: 'notifications',
                        type: 'error',
                        text: 'Please Fill in title fields'
                    });
                    console.log(err);
                });
            },
            addCategory() {
                axios.post('/admin/categories', {name: this.category}).then(response => {
                    this.categories.push({'name': response.data.name, 'id': response.data.id});
                    this.categoryfield.push(response.data.id);
                });
                this.category = '';
            },
            contentUpdate(content) {

                this.formdata.content = content;
            },
        }
    }
</script>
