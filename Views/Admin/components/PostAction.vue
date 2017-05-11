<style>
    .post-action .title-input {
        color: #20252b;
        font-size: 1em;
        width: 100%;
        padding: 5px;
    }
    .post-action .right-bloc {
        font-size: 1.3em;
        margin-bottom: 10px;
    }
    .post-action i {
        margin-right: 5px;
    }
    .post-action article {
        padding: 10px;
    }
    .post-action .right-bottom-bloc h3 {
        overflow: auto;
        padding: 15px 10px;
    }
    .post-action .right-bottom-bloc button {
        margin-left: 5px;
    }
    .post-action .img-body {
        padding: 0;
        height: 200px !important;
        width: 100% !important;
        overflow: hidden;
        position: relative;
        background: #c2bfbf;
    }
    .post-action .img-body img {
        max-height: 100%;
        max-width: 100%;
        width: auto;
        height: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        margin: auto;
    }
    .post-action .mar-top-10 {
        margin-top: 10px;
    }
    .post-action .media-action-btn{
        float: right;
        padding: 5px 0;
    }
    .post-action .right-bottom-bloc .media-title{
        float: left;
        margin-top: 0;
    }



</style>

<template>
    <div>
        <section class="post-action">
            <div class="section-header">
                <ol class="breadcrumb">
                    <li>
                        <router-link :to="{name: 'module:post', params: {website_id: $route.params.website_id}}">
                            Articles
                        </router-link>
                    </li>
                    <li class="active">{{ post.title }}</li>
                </ol>

            </div>
            <div class="section-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-tiles style-default-light">

                            <!-- BEGIN BLOG POST HEADER -->
                            <div class="row style-default-dark">
                                <div class="col-sm-9">
                                    <div class="card-body style-default-dark">
                                        <h2><input class="title-input" v-model="post.title" type="text"></h2>
                                        <div v-if="auth.status.leve < 4" class="form-group">
                                            <label class="post-slug-label" for="post-slug">Slug</label>
                                            <input id="post-slug" class="title-input" v-model="post.slug" type="text">
                                        </div>
                                        <div class="text-default-light"><strong class="text-primary">Lien : </strong><a
                                                :href="website.url + post_url" target="_blank">{{website.url}}{{post_url}}</a>
                                        </div>
                                    </div>
                                </div><!--end .col -->
                                <div class="col-sm-3 style-primary">
                                    <div class="card-body">
                                        <div class="row right-bloc">
                                            <strong><i class="fa fa-check-circle-o"></i> État : </strong><span
                                                v-if="post.published">Publié</span><span v-else>Brouillon</span><br/>
                                            <strong><i class="fa fa-calendar"></i> Publié le : </strong>{{post.updated_at.date
                                            | moment('DD/MM/YYYY')}}
                                            <div class="switch">
                                                <label>
                                                    Brouillon
                                                    <input v-model="post.published" type="checkbox">
                                                    <span class="lever"></span>
                                                    Publié
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <button data-toggle="modal" data-target="#deletePostModal" type="button"
                                                    class="col-md-12 btn ink-reaction btn-raised btn-default">
                                                <i class="fa fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                        <div class="row mar-top-10">
                                            <button @click="updateOrCreatePost" type="button"
                                                    class="col-md-12 btn ink-reaction btn-raised btn-default">
                                                <i class="fa fa-save"></i> Enregistrer
                                            </button>
                                        </div>
                                    </div>
                                </div><!--end .col -->
                            </div><!--end .row -->
                            <!-- END BLOG POST HEADER -->

                            <div class="row">

                                <!-- BEGIN BLOG POST TEXT -->
                                <div class="col-md-9">
                                    <article class="style-default-bright">
                                        <h4>Description</h4>
                                        <div class="form-group">
                                            <textarea class="form-control" id="post-description"
                                                      v-model="post.description"></textarea>
                                        </div>
                                    </article>
                                    <article class="style-default-bright">
                                        <h4>Contenu</h4>
                                        <div>
                                            <tinymce-editor v-if="launch_tinymce" @updateContent="updateContent"
                                                            :height="300"
                                                            :id="'post-' + post_id"
                                                            :value="post.content"></tinymce-editor>
                                        </div>
                                    </article>
                                    <div class="style-default-bright col-md-12">
                                        <form class="form row">
                                            <div class="col-md-12 custom-field-render">
                                                <div class="card-body no-padding">
                                                    <div v-if="custom_fields.length > 0"
                                                         v-for="custom_field in custom_fields">
                                                        <h2>{{custom_field.title}}</h2>
                                                        <custom-field-render :id="post_id" :type="'post'"
                                                                             :fields="custom_field.fields"></custom-field-render>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div><!--end .col -->
                                <!-- END BLOG POST TEXT -->

                                <!-- BEGIN BLOG POST MENUBAR -->
                                <div class="col-md-3">
                                    <div class="card-body right-bottom-bloc">
                                        <h3 class="media-title text-light">Image à la une
                                            <!--<button @click="launchMedia" data-toggle="modal"
                                                    data-target="#mediaLibrary0" type="button"
                                                    class="btn pull-right ink-reaction btn-floating-action btn-info"><i
                                                    class="fa fa-pencil"></i></button>
                                            <button @click="post.thumbnail = null" type="button"
                                                    class="btn pull-right ink-reaction btn-floating-action btn-danger">
                                                <i class="fa fa-trash"></i></button>-->
                                        </h3>
                                        <div class="media-action-btn btn-group">
                                            <button type="button"
                                                    class="btn ink-reaction btn-floating-action btn-primary"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-bars"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                <li><a @click="launchMedia" data-toggle="modal"
                                                       data-target="#mediaLibrary0"><i
                                                        class="fa text-info fa-pencil"></i>
                                                    Modifier l'image</a></li>
                                                <li><a @click="post.thumbnail = null"><i
                                                        class="fa text-danger fa-trash"></i>
                                                    Supprimer l'image</a></li>
                                            </ul>
                                        </div>
                                        <div v-if="post.thumbnail != null" class="img-body">
                                            <img v-img="post.thumbnail.path" :alt="post.thumbnail.alt" width="100%">
                                        </div>
                                        <div class="clearfix"></div>
                                        <h3 class="text-light">Catégories
                                            <button data-toggle="modal" data-target="#createPostCategoryModal"
                                                    type="button"
                                                    class="btn pull-right ink-reaction btn-floating-action btn-primary"><i
                                                    class="fa fa-plus"></i></button>
                                        </h3>
                                        <ul class="nav nav-pills nav-stacked nav-transparent">
                                            <li v-for="category in categories">
                                                <a>
                                                    <div class="pull-right checkbox checkbox-styled checkbox-primary">
                                                        <label>
                                                            <input type="checkbox" v-model="post_categories"
                                                                   :value="category.id">
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    {{category.name}}</a>
                                            </li>
                                        </ul>
                                    </div><!--end .card-body -->
                                </div><!--end .col -->
                                <!-- END BLOG POST MENUBAR -->

                            </div><!--end .row -->
                        </div><!--end .card -->
                    </div><!--end .col -->
                </div><!--end .row -->

            </div><!--end .section -->
            <div class="modal fade" id="createPostCategoryModal" tabindex="-1" role="dialog"
                 aria-labelledby="formModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="createFormModalLabel">Ajouter une catégorie</h4>
                        </div>
                        <form class="form" role="form">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <input type="text" v-model="new_category" id="category"
                                                   class="form-control">
                                            <label for="category" class="control-label">Titre</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                                <button type="button" @click="createCategory" data-dismiss="modal"
                                        class="btn btn-primary">Enregistrer
                                </button>
                            </div>
                        </form>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
            <div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="deletePostModalLabel">Suppression</h4>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer cet article ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click="deletePost()">Oui
                            </button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div>
            <media :launch_media="launch_media" :button="false" @updateTarget="targetUpdate"
                   :accepted_file_type="file_type"
                   :max_options="max_media_options"></media>
        </section>

    </div>
</template>


<script type="text/babel">

    import {custom_field_api} from '@front/api'
    import {post_api, post_category_api} from '../api'

    import {mapGetters, mapActions} from 'vuex'

    export default
    {
        components: {
            TinymceEditor: resolve => {
                require(['@front/components/Helper/TinymceEditor.vue'], resolve)
            },
            Media: resolve => {
                require(['@front/components/Helper/Media.vue'], resolve)
            },
            CustomFieldRender: resolve => {
                require(['@front/components/CustomFieldRender/Repeater/RepeaterRenderCustomField.vue'], resolve)
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                post_id: this.$route.params.post_id,
                post: {
                    title: '',
                    slug: '',
                    content: '',
                    description: '',
                    published: true,
                    updated_at: {
                        date: ''
                    },
                    thumbnail: {
                        path: '',
                        alt: ''
                    },
                    website: {
                        domain: ''
                    },
                    new_categories: []
                },
                post_categories: [],
                categories: {},
                new_category: '',
                route: '',
                post_url: '',
                file_type: ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'],
                max_media: 25,
                max_media_options: [25, 50, 100],
                media_target_id: null,
                launch_media: false,
                launch_tinymce: false,
                custom_fields: []
            }
        },
        computed: {
            ...mapGetters([
                'website', 'auth', 'system'
            ]),
            custom_fields_params () {
                return {
                    everywhere: '',
                    publication_type: 'post',
                    user_role: this.auth.status.id,
                    post: (this.post.id !== undefined) ? this.post.id : this.post_id,
                    post_category: this.post_categories
                }
            }
        },
        methods: {
            ...mapActions([
                'create', 'read', 'update', 'destroy', 'createResource', 'updateResource', 'updateResourceValue', 'removeResource', 'removePagination', 'deleteResources'
            ]),
            generateUrl(){
                if (this.route.url !== undefined) {
                    let regex = {':slug': this.post.slug, ':id': this.post.id};
                    this.post_url = this.route.url;
                    for (let index in regex) {
                        if (regex.hasOwnProperty(index)) {
                            this.post_url = this.post_url.replace(index, regex[index]);
                        }
                    }
                }
            },
            updateContent (content) {
                this.post.content = content;
            },
            targetUpdate(target){
                this.post.thumbnail = target;
            },
            launchMedia () {
                this.launch_media = !this.launch_media;
            },
            /* Read methods */
            loadPost(){
                return this.read({api: post_api.read + this.website_id + '/' + this.post_id}).then((response) => {
                    if (response.data.status == 'success') {
                        this.post = response.data.resource;
                        if(this.post.content == null) this.post.content = '';
                        this.launch_tinymce = true;
                        this.generateUrl();
                    }
                })
            },
            loadCustomFields(){
                this.read({
                    api: custom_field_api.admin_render + this.website_id,
                    options: {params: {params: this.custom_fields_params}}
                }).then((response) => {
                    if (response.data.resource !== undefined)
                        this.custom_fields = response.data.resource;
                })
            },
            loadSinglePostRoute(){
                this.read({api: post_api.get_single_post_route + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined) {
                        this.route = response.data.resource;
                    }
                })
            },
            loadCategory(){
                this.read({api: post_category_api.list_names + this.website_id}).then((response) => {
                    if (response.data.resource !== undefined)
                        this.categories = response.data.resource;
                })
            },
            /* Create or update methods */
            createCategory(){
                if (this.new_category != '') {
                    this.create({
                        api: post_category_api.create + this.website_id,
                        value: {name: this.new_category}
                    }).then(() => {
                        this.loadCategory();
                    });
                }
            },
            updateOrCreatePost(){
                this.post['new_categories'] = this.post_categories;
                if (this.post_id == 'create') {
                    this.createResource({
                        api: post_api.update_or_create + this.website_id + '/create',
                        resource: 'posts_' + this.website_id,
                        value: this.post
                    }).then((response) => {
                        this.updateAll(response);
                    });
                } else {
                    this.updateResource({
                        api: post_api.update_or_create + this.website_id + '/' + this.post.id,
                        resource: 'posts_' + this.website_id,
                        value: this.post
                    }).then((response) => {
                        this.updateAll(response);
                    });
                }
            },
            updateAll(response){
                if (response.data.status == 'success' && response.data.resource !== undefined) {
                    this.post = response.data.resource;
                    this.generateUrl();
                    this.updateCustomFields(this.post.id).then(() => {
                        this.updateOthers(this.post.id)
                    });
                }
            },
            updateCustomFields(post_id){
                if (this.custom_fields.length > 0) {
                    return this.update({
                        api: custom_field_api.update_or_create_front + this.website_id + '/post/' + post_id,
                        value: {
                            custom_fields: this.custom_fields,
                            old_content_key: 'post@' + this.post_id,
                            old_row_key: 'rows@post@' + this.post_id,
                            params: this.custom_fields_params
                        }
                    }).then((field_response) => {
                        this.removePagination('custom_fields_' + this.website_id);
                        if (field_response.data.resource !== undefined)
                            this.custom_fields = field_response.data.resource;
                        else if (field_response.data.reload !== undefined)
                            location.reload();
                    });
                }
                return new Promise((resolve, reject) => {
                    resolve();
                });
            },
            updateOthers(post_id){
                if (this.post_id != 'create') {
                    this.read({api: post_api.emit_post_update_event + this.post_id + '/' + post_id + '/' + this.website_id})
                    if (this.post_id != post_id) {
                        this.removeResource({
                            resource: 'posts_' + this.website_id,
                            id: this.post_id
                        });
                    }
                }
                if (this.post_id != post_id) {
                    this.$router.replace({
                        name: 'module:post:action',
                        params: {
                            website_id: this.website_id,
                            post_id: post_id
                        }
                    });
                }
                this.post_id = (this.post.id !== undefined) ? this.post.id : 'create';
            },
            /* Delete method */
            deletePost (){
                if (this.post.id !== undefined) {
                    this.deleteResources({
                        api: post_api.destroy + this.website_id,
                        resource: 'posts_' + this.website_id,
                        ids: [this.post.id]
                    }).then((response) => {
                        if (response.data.status == 'success') {
                            this.$router.replace({name: 'module:post', params: {website_id: this.website_id}})
                        }
                    });
                }
            }
        },
        mounted(){
            this.loadSinglePostRoute();
            if (this.post_id == 'create') {
                this.launch_tinymce = true;
                this.loadCategory();
                this.loadCustomFields();
            } else {
                this.loadPost().then(() => {
                    this.loadCategory();
                    this.loadCustomFields();
                    for (let index in this.post.categories)
                        if (this.post.categories.hasOwnProperty(index))
                            this.post_categories.push(this.post.categories[index].id);
                })
            }
        }
    }
</script>