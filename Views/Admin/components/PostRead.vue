<style>
    #mediaLibrary{
        z-index: 999999;
    }
    .post-read .title-input{
        color: #20252b;
        font-size: 1em;
        width: 100%;
        padding: 5px;
    }
    .post-read .right-bloc{
        font-size:1.3em;
        margin-bottom:10px;
    }
    .post-read i{
        margin-right: 5px;
    }
    .post-read article{
        padding: 10px;
    }
</style>

<template>
    <div>
        <loading :loading="loading"></loading>
        <section class="post-read" v-show="post.id">
            <div class="section-header">
                <ol class="breadcrumb">
                    <li>
                        <router-link :to="{name: 'module:post', params: {website_id: $route.params.website_id}}">Articles</router-link>
                    </li>
                    <li class="active">{{ post.title }}</li>
                </ol>

            </div>
            <div class="section-body contain-lg">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card card-tiles style-default-light">

                            <!-- BEGIN BLOG POST HEADER -->
                            <div class="row style-default-dark">
                                <div class="col-sm-9">
                                    <div class="card-body style-default-dark">
                                        <h2><input class="title-input" v-model="post.title" type="text"></h2>
                                        <div class="text-default-light"><strong class="text-primary">Lien : </strong><a :href="post.website.domain + route" target="_blank" >{{post.website.domain}}{{route}}</a></div>
                                    </div>
                                </div><!--end .col -->
                                <div class="col-sm-3 style-primary">
                                    <div class="card-body">
                                        <div class="row right-bloc">
                                            <strong><i class="fa fa-check-circle-o"></i> État : </strong><span v-if="post.published">Publié</span><span v-else>Brouillon</span><br />
                                            <strong><i class="fa fa-calendar"></i> Publié le : </strong>{{post.updated_at.date | moment('DD/MM/YYYY')}}
                                        </div>
                                        <div class="row">
                                            <button type="button" class="col-md-6 btn ink-reaction btn-raised btn-danger">Supprimer</button>
                                            <button type="button" class="col-md-6 btn ink-reaction btn-raised btn-default">Mettre à jour</button>
                                        </div>
                                    </div>
                                </div><!--end .col -->
                            </div><!--end .row -->
                            <!-- END BLOG POST HEADER -->

                            <div class="row">

                                <!-- BEGIN BLOG POST TEXT -->
                                <div class="col-md-9">
                                    <article class="style-default-bright">
                                        <div>
                                            <textarea class="post_content">{{post.content}}</textarea>
                                        </div>
                                    </article>
                                </div><!--end .col -->
                                <!-- END BLOG POST TEXT -->

                                <!-- BEGIN BLOG POST MENUBAR -->
                                <div class="col-md-3">
                                    <div class="card-body">
                                        <h3 class="text-light">Image à la une <i class="pull-right text-danger fa fa-trash"></i></h3>
                                        <img v-img="post.thumbnail.path" :alt="post.thumbnail.alt" width="100%">
                                        <h3 class="text-light">Catégories <i class="pull-right text-info fa fa-plus"></i></h3>
                                        <ul class="nav nav-pills nav-stacked nav-transparent">
                                            <li v-for="category in categories">
                                                <a href="#"><div class="pull-right checkbox checkbox-styled checkbox-primary">
                                                    <label>
                                                        <input type="checkbox" :checked="checkCategory(category.id)">
                                                        <span></span>
                                                    </label>
                                                </div>{{category.name}}</a>
                                            </li>
                                        </ul>
                                        <h3 class="text-light">Tags</h3>
                                        <div class="list-tags">
                                            <a class="btn btn-xs btn-primary">Wordpress</a>
                                            <a class="btn btn-xs btn-primary">Technology</a>
                                            <a class="btn btn-xs btn-primary">HTML5</a>
                                            <a class="btn btn-xs btn-primary">Illustrator</a>
                                            <a class="btn btn-xs btn-primary">Music</a>
                                            <a class="btn btn-xs btn-primary">CSS3</a>
                                            <a class="btn btn-xs btn-primary">Video</a>
                                            <a class="btn btn-xs btn-primary">Photoshop</a>
                                            <a class="btn btn-xs btn-primary">jQuery</a>
                                        </div>
                                    </div><!--end .card-body -->
                                </div><!--end .col -->
                                <!-- END BLOG POST MENUBAR -->

                            </div><!--end .row -->
                        </div><!--end .card -->
                    </div><!--end .col -->
                </div><!--end .row -->

            </div><!--end .section -->
            <media :launch_media="launch_media" :button="false" @updateTarget="targetUpdate" @updateMax="maxUpdate" :dir="'/sites/' + website_id + '/'" :accepted_file_type="file_type" :max_options="max_media_options"></media>
        </section>
    </div>
</template>


<script type="text/babel">


    import Response from '../../../../../Blocks/AdminBlock/Front/components/Helper/Response.vue'
    import Loading from '../../../../../Blocks/AdminBlock/Front/components/Helper/Loading.vue'
    import Pagination from '../../../../../Blocks/AdminBlock/Front/components/Helper/Pagination.vue'
    import Media from '../../../../../Blocks/AdminBlock/Front/components/Helper/Media.vue'

    import {mapGetters, mapActions} from 'vuex'

    export default
    {
        components: {Response, Loading, Pagination, Media},
        data () {
            return {
                website_id: this.$route.params.website_id,
                post_id: this.$route.params.post_id,
                post: {
                    updated_at: {
                        date: ''
                    },
                    thumbnail: {
                        path: '/user/default-photo.png',
                        alt:''
                    },
                    website: {
                        domain: ''
                    }
                },
                categories: {},
                post_categories: {},
                route: '',
                file_type: ['image/jpg','image/png','image/gif'],
                max_media: 24,
                max_media_options: [24,48,96],
                media_target_id: null,
                loading: false,
                launch_media: false
            }
        },
        computed: {
            ...mapGetters([
                'pagination'
            ])
        },
        methods: {
            ...mapActions([
                'create', 'read', 'update', 'destroy', 'setParams', 'refresh', 'updateResourceValue', 'deleteResources'
            ]),
            targetUpdate (target) {
                $('#'+this.media_target_id).val(PUBLIC_PATH + '/public/media' + target.path);
            },
            maxUpdate (max) {
                this.max_media = max;
            },
            checkCategory (category){
                for(let index in this.post_categories) {
                    if (this.post_categories.hasOwnProperty(index)) {
                        if(this.post_categories[index].id == category)return true;
                    }
                }
                return false;
            }
        },
        mounted () {
            this.$nextTick(function () {
                let o = this;
                this.loading = true;
                this.read({api: ADMIN_DOMAIN + '/module/post/read/' + this.website_id + '/' + this.post_id}).then((response) => {
                    if (response.data.status == 'success') {
                        this.post = response.data.resource;
                        if (response.data.route != '') {
                            let regex = {':slug': this.post.slug, ':id': this.post.id};
                            this.route = response.data.route.url;
                            for (let index in regex) {
                                if (regex.hasOwnProperty(index)) {
                                    this.route = this.route.replace(index, regex[index]);
                                }
                            }
                        }
                        this.post_categories = response.data.categories.categories;
                    }
                    this.loading = false;

                }).then(() => {
                    this.loading = true;
                    this.read({api: ADMIN_DOMAIN + '/module/post-category/list-by-name/' + this.website_id}).then((response) => {
                        this.categories = response.data;
                        this.loading = false;
                    })
                }).then(() => {
                    tinymce.init({
                        selector: '.post_content',
                        language: 'fr_FR',
                        height: 300,
                        plugins: [
                            'advlist autolink link image lists charmap preview hr anchor pagebreak',
                            'wordcount visualblocks visualchars code insertdatetime media nonbreaking',
                            'table directionality emoticons template textcolor'
                        ],
                        toolbar: 'undo redo | styleselect | forecolor bold italic | table link image | code',
                        file_browser_callback: function (field_name, url, type, win) {
                            o.launch_media = true;
                            o.media_target_id = field_name;
                            $('#mediaLibrary').modal()
                        }
                    });
                });
            });
        }
    }
</script>
