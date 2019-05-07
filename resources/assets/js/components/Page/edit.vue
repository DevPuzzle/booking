<template>
    <div class="row">
         <div class="col-md-3">
              <h3>Page options</h3> 
               <hr> 
            <div>
                                
                <div class="card">
                    <div class="card-body">
                        <p>
                            <strong>Status: </strong> {{formdata.status}}
                            <button href="#" class="btn btn-outline-primary btn-sm" @click="publish()"
                                    v-if="formdata.status && formdata.status !=='published' ">Publish
                            </button>
                            <button href="#" class="btn btn-outline-primary btn-sm" @click="unpublish()"
                                    v-if="formdata.status === 'published' ">Unpublish
                            </button>
                        </p>
                        <p>
                            <strong>Last Updated:</strong> {{formdata.last_updated}}
                        </p>
                        
                    </div>
                </div>
                <div class="card mt-2" v-if="roles.length">
                    <div class="card-body">
                        <h4 class="card-title">Which roles can access:</h4>
                        <div class="form-check" v-for="r in roles">
                            <input type="checkbox" class="form-check-input" :id="r.slug" v-model="role" :value="r.id">
                            <label class="form-check-label" :for=r.slug>{{ r.name }}</label><br>
                        </div>
                        <br>
                        <button @click="updateViewBy('role', role)" class="btn btn-outline-success btn-sm">Save</button>
                    </div>
                </div>

                <div class="card mt-2" v-if="regions.length">
                    <div class="card-body">
                        <h4 class="card-title">Which regions can access:</h4>
                        <div class="form-check" v-for="r in regions">
                            <input type="checkbox" class="form-check-input" :id="r.slug" v-model="region" :value="r.id">
                            <label class="form-check-label" :for=r.slug>{{ r.name }}</label><br>
                        </div>
                        <br>
                        <button @click="updateViewBy('region', region)" class="btn btn-outline-success btn-sm">Save
                        </button>
                    </div>
                </div>

                <div class="card mt-2" v-if="categories.length">
                    <div class="card-body">
                        <h4 class="card-title">Page Categories</h4>
                        <div style="max-height: 100px; overflow: auto">
                            <div class="form-check" v-for="category in categories">
                                <input type="checkbox" class="form-check-input" :id=category.name
                                       v-model="categoryfield" :value=category.id>
                                <label class="form-check-label" :for=category.name>{{ category.name }}</label><br>
                            </div>
                        </div>
                        <br>
                        <button @click="addPageCategory('role', role)" class="btn btn-primary">
                            Save
                        </button>
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
        </div>
        <div class="col-md-9">
             <h3>Edit page : <b>{{ formdata.title }}</b> </h3> 
               <hr> 
            <div class="form-group">
                <label>Title</label>
                <input type="text" v-model="formdata.title" class="form-control"/>
            </div>

            <div class="form-group">
                <label>Summary</label>
                <input type="text" v-model="formdata.summary" class="form-control"/>
            </div>

            <div class="form-group">
                <label>Content</label>
                <ck-editor :content="formdata.content" name="content" @contentUpdate="contentUpdate"></ck-editor>
            </div>

            <div class="form-group">
                <button class="btn btn-primary btn-block" @click="update">Update page</button>
            </div>
        </div>
       
    </div>
</template>

<script>
    import axios from 'axios';

    export default {
        props: ['page_id'],
        data() {
            return {
                formdata: {},
                publish_date: '',
                published: '',
                roles: {},
                regions: {},
                role: [],
                region: [],
                categories: [],
                category: '',
                categoryfield: []
            }
        },
        mounted() {
            axios.get('/admin/pages/ajax/' + this.page_id).then(response => {
                this.formdata = response.data.page;
                this.published = response.data.page.published_at;
            });

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
            axios.get('/admin/pages/' + this.page_id + '/categories').then(response => {
                for (var i = 0; i < response.data.length; i++) {
                    this.categoryfield.push(response.data[i].id);
                }
            });

            axios.get('/admin/pages/' + this.page_id + '/roles/checked').then(response => {
                console.log(response.data);
                for (var i = 0; i < response.data.length; i++) {
                    this.role.push(response.data[i].pivot.viewable_id);
                }
            });
            axios.get('/admin/pages/' + this.page_id + '/regions/checked').then(response => {
                for (var i = 0; i < response.data.length; i++) {
                    this.region.push(response.data[i].pivot.viewable_id);
                }
            });
        },
        methods: {
            update() {
                const uri = '/admin/pages/' + this.page_id;
                axios.put(uri, this.formdata).then(response => {
                    this.formdata = response.data;
                    this.$notify({
                        group: 'notifications',
                        type: 'success',
                        text: 'Page Content Updated Successfully.'
                    });
                }).catch(err => console.log(err));
            },
            contentUpdate(content) {
                this.formdata.content = content;
            },
            publishedAt(published) {
                console.log(published);
                this.published = published;
            },
            publish() {
                this.update();
                window.location.href = '/admin/pages/' + this.page_id + '/publish';
            },
            unpublish() {
                this.update();
                window.location.href = '/admin/pages/' + this.page_id + '/unpublish';
            },
            updateViewBy(type, rr) {
                axios.post('/admin/pages/' + this.page_id + '/viewby/' + type, rr).then(response => {
                    this.$notify({
                        group: 'notifications',
                        type: 'success',
                        text: 'Updated successfully'
                    });

                });
            },
            addCategory() {
                axios.post('/admin/categories', {name: this.category}).then(response => {
                    this.categories.push({'name': response.data.name, 'id': response.data.id});
                    this.categoryfield.push(response.data.id);
                });
                this.category = '';
            },
            addPageCategory() {
                axios.post('/admin/pages/' + this.page_id + '/category', this.categoryfield).then(response => {
                    this.$notify({
                        group: 'notifications',
                        type: 'success',
                        text: 'Updated successfully'
                    });
                });
            }
        }
    }
</script>
