<style>
    .list-post .breadcrumb {
        display: inline-block;
    }

    .list-post button {
        margin-left: 10px;
    }

    .list-post .section-body {
        margin-top: 10px;;
    }

    .list-post .checkbox {
        float: left;
    }

    .list-post .list-results .post-box {
        padding: 20px 10px;
    }

    .list-post .post-icon {
        padding: 0 10px;
        display: inline-block;
        vertical-align: middle;
    }
    .list-post .category-icon{
        padding-right: 10px;
    }
    .list-post .clearfix{
        clear: both;
    }
    .list-post .edit-category{
        cursor: pointer;
        position: absolute;
        top: 0;
        right: 5px;
    }
    .list-post .edit-category i{
        margin-right: 10px;
    }
    .list-post .edit-category:hover i{
        font-size:2em;
    }
    .list-post .category-title{
        padding-right:20px;
    }
</style>

<template>
    <section class="list-post">
        <loading :loading="loading"></loading>
        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Articles</li>
            </ol>
            <button class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i> Ajouter un article
            </button>
            <button data-toggle="modal" data-target="#createPostCategoryModal" class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                <i class="fa fa-plus" aria-hidden="true"></i>
                Ajouter une catégorie
            </button>
        </div>
        <div class="section-body">
            <div class="card tabs-left style-default-light">

                <!-- BEGIN SEARCH BAR -->
                <div class="card-body style-primary no-y-padding">
                    <form class="form form-inverse search-item">
                        <div class="form-group">
                            <div class="input-group input-group-lg">
                                <div class="input-group-content">
                                    <input type="text" class="form-control" id="searchInput" v-model="search_value"
                                           placeholder="Recherche">
                                    <div class="form-control-line"></div>
                                </div>
                                <div class="input-group-btn">
                                    <button class="btn btn-floating-action btn-default-bright" @click="search"
                                            type="button"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div><!--end .form-group -->
                    </form>
                </div><!--end .card-body -->
                <!-- END SEARCH BAR -->

                <!-- BEGIN TAB RESULTS -->
                <ul class="card-head nav nav-tabs tabs-accent post-categories" data-toggle="tabs">
                    <li class="post-category post-all active"><a @click="refresh(resource.name);addClass('all')">Tous les
                        articles</a></li>
                    <li :class="'post-category post-' + category.slug" v-for="category in categories">
                        <a @click="setParams({resource:resource.name, key: 'filter', value: {column:'c.id',operator:'eq',value:category.id}});addClass(category.slug)">
                            <i :title="getIconTitle('Cette catégorie',category.website.id)" :class="'category-icon ' + getIconClass(category.website.id)"></i>
                            <span class="category-title">{{category.name}}</span>
                        </a>
                        <span @click="selectCategory(category)" data-toggle="modal" data-target="#editPostCategoryModal" class="pull-right clearfix edit-category"><i class="fa fa-pencil"></i></span>
                    </li>
                </ul>
                <!-- END TAB RESULTS -->

                <!-- BEGIN TAB CONTENT -->
                <div class="card-body tab-content style-default-bright">
                    <div class="tab-pane active" id="web1">
                        <div class="row">
                            <div class="col-lg-12">

                                <!-- BEGIN PAGE HEADER -->
                                <div class="margin-bottom-xxl">
                                    <label class="text-light text-lg">Lister : </label>
                                    <select v-model.number="resource.max">
                                        <option v-for="option in max_options" :value="option">{{option}}
                                        </option>
                                    </select>
                                    <span class="text-light text-lg">Total <strong>{{resource.total}}</strong></span>
                                    <div class="btn-group btn-group-sm pull-right">
                                        <button type="button" class="btn btn-default-light dropdown-toggle"
                                                data-toggle="dropdown">
                                            <span class="glyphicon glyphicon-arrow-down"></span> Trier
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right animation-dock" role="menu">
                                            <li>
                                                <a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.updated_at',dir:'asc'}});addClass('all')">Date
                                                    croissant</a></li>
                                            <li>
                                                <a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.updated_at',dir:'desc'}});addClass('all')">Date
                                                    décroissant</a></li>
                                            <li><a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.title',dir:'asc'}});addClass('all')">Titre
                                                croissant</a></li>
                                            <li><a @click="setParams({resource: resource.name, key: 'order', value: {column:'p.title',dir:'desc'}});addClass('all')">Titre
                                                décroissant</a></li>
                                        </ul>
                                    </div>
                                    <div class="btn-group btn-group-sm pull-right">
                                        <button type="button" class="btn btn-default-light dropdown-toggle"
                                                data-toggle="dropdown">
                                            <span class="glyphicon glyphicon-arrow-down"></span> Séléction
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right animation-dock" role="menu">
                                            <li><a @click="updatePostState(1)">Publier</a></li>
                                            <li><a @click="updatePostState(0)">Dépubier</a></li>
                                            <li><a data-toggle="modal" data-target="#deletePostModal">Supprimer</a></li>
                                        </ul>
                                    </div>
                                </div><!--end .margin-bottom-xxl -->
                                <!-- END PAGE HEADER -->

                                <!-- BEGIN RESULT LIST -->
                                <div class="list-results list-results-underlined">
                                    <div v-for="post in resource.data" class="col-xs-12 post-box">
                                        <div class="checkbox checkbox-styled checkbox-primary">
                                            <label>
                                                <input type="checkbox" :value="post.id" v-model="selected_items">
                                                <span></span>
                                            </label>
                                        </div>
                                        <img class="pull-left width-3" v-img="post.thumbnail.path"
                                             :alt="post.thumbnail.alt"/>
                                        <div>
                                            <router-link :to="{name: 'module:post:read', params:{website_id: website_id, post_id: post.id}}" class="text-medium text-lg text-primary">{{post.title}}</router-link>
                                            <div class="pull-right">
                                                <router-link :to="{name: 'module:post:read', params:{website_id: website_id, post_id: post.id}}" class="btn ink-reaction btn-floating-action btn-info"><i
                                                        class="fa fa-pencil"></i></router-link>
                                                <a @click="selectPost(post)" data-toggle="modal" data-target="#deletePostModal"
                                                   class="btn ink-reaction btn-floating-action btn-danger"><i
                                                        class="fa fa-trash"></i></a>
                                                <a @click="changeState(post)" title="Cet article est publié" v-show="post.published"
                                                   class="btn ink-reaction btn-floating-action btn-success"><i
                                                        class="fa fa-check"></i></a>
                                                <a @click="changeState(post)" title="Cet article n'est pas publié" v-show="!post.published"
                                                   class="btn ink-reaction btn-floating-action btn-warning"><i
                                                        class="fa fa-times"></i></a>
                                                <span class="post-icon"><i :title="getIconTitle('Cet article',post.website.id)"
                                                                           :class="getIconClass(post.website.id)"></i> </span>
                                            </div>
                                            <br/>
                                        </div>
                                        <div class="contain-xs pull-left">
                                            {{post.description}}
                                        </div>
                                        <div class="clearfix"></div>
                                        <em class="pull-right">Dernière mise à jour : {{ post.updated_at.data |
                                            moment('DD/MM/YYYY') }}</em>
                                    </div><!--end .col -->
                                </div><!--end .list-results -->
                                <!-- END RESULTS LIST -->

                                <!-- BEGIN PAGING -->
                                <pagination :refresh="refresh_items" :resource="resource"></pagination>
                                <!-- END PAGING -->

                            </div><!--end .col -->
                        </div><!--end .row -->
                    </div><!--end .tab-pane -->
                </div><!--end .card-body -->
                <!-- END TAB CONTENT -->

            </div><!--end .card -->
        </div><!--end .section-body -->

        <div class="modal fade" id="deletePostModal" tabindex="-1" role="dialog" aria-labelledby="simpleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="deletePostModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Voulez-vous vraiment supprimer cet article ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="deletePost()">Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="createPostCategoryModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
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
                                        <input type="text" v-model="new_category" id="category" class="form-control">
                                        <label for="category" class="control-label">Titre</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            <button type="button" @click="createCategory" data-dismiss="modal" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <div class="modal fade" id="editPostCategoryModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="editFormModalLabel">Modifier la catégorie</h4>
                    </div>
                    <form class="form" role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" v-model="category.name" id="edit-category" class="form-control">
                                        <label for="edit-category" class="control-label">Titre</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button"  @click="deleteCategory" data-dismiss="modal" class="btn btn-danger">Supprimer la catégorie</button>
                            <button type="button" @click="updateCategory" data-dismiss="modal" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </section>


</template>


<script type="text/babel">

    import Response from '../../../../../Blocks/AdminBlock/Front/components/Helper/Response.vue'
    import Loading from '../../../../../Blocks/AdminBlock/Front/components/Helper/Loading.vue'
    import Pagination from '../../../../../Blocks/AdminBlock/Front/components/Helper/Pagination.vue'

    import {mapActions} from 'vuex'

    export default
    {
        components: {Response, Loading, Pagination},
        data () {
            return {
                website_id: this.$route.params.website_id,
                resource: {
                    url: ADMIN_DOMAIN + '/module/post/all/' + this.$route.params.website_id,
                    name: 'posts_' + this.$route.params.website_id,
                    data: [],
                    max: 10,
                    total: 0
                },
                category: {},
                new_category: '',
                categories: {},
                search_value: '',
                selected_items: [],
                refresh_items: false,
                icon_class: '',
                max_options: [10, 20, 30],
                loading: false
            }
        },
        methods: {
            ...mapActions([
                'create', 'read', 'update','destroy', 'setParams', 'refresh', 'updateResourceValue', 'deleteResources'
            ]),
            search () {
                if (this.search_value !== ''){
                    this.setParams({resource: this.resource.name, key: 'search', value: this.search_value});
                    this.addClass('all');
                }
            },
            addClass (slug){
                $('.post-category').removeClass('active');
                $('.post-' + slug).addClass('active');
            },
            getIconClass (website) {
                return (this.website_id == website) ? 'fa fa-laptop' : 'fa fa-sitemap';
            },
            getIconTitle (content,website) {
                return (this.website_id == website) ? content + ' vient du site' : content + ' vient du thème parent';
            },
            selectPost (post){
                this.selected_items = [post];
            },
            updatePostState (state) {
                if (this.selected_items.length > 0) {
                    this.loading = true;
                    this.update({
                        api: ADMIN_DOMAIN + '/module/post/change-state/' + this.website_id,
                        value: {
                            state: parseInt(state),
                            ids: this.selected_items
                        }
                    }).then((response) => {
                        if (response.data.status == 'success')
                            this.selected_items.forEach((id) => {
                                this.updateResourceValue({
                                    resource: this.resource.name,
                                    id,
                                    key: 'published',
                                    value: state
                                });
                            });
                        this.selected_items = [];
                        this.loading = false;
                        this.refresh_items = true;
                    });
                    this.refresh_items = false;
                }
            },
            changeState (post) {
                this.loading = true;
                let state = (post.published == 0) ? 1 : 0;
                this.update({
                    api: ADMIN_DOMAIN + '/module/post/change-state/' + this.website_id,
                    value: {
                        state,
                        ids: [post.id]
                    }
                }).then((response) => {
                    if (response.data.status == 'success')
                        this.updateResourceValue({
                            resource: this.resource.name,
                            id: post.id,
                            key: 'published',
                            value: state
                        });
                    this.loading = false;
                    this.refresh_items = true;
                });
                this.refresh_items = false;
            },
            deletePost () {
                if (this.selected_items.length > 0) {
                    this.loading = true;
                    this.deleteResources({
                        api: ADMIN_DOMAIN + '/module/post/delete/' + this.website_id,
                        resource: this.resource.name,
                        ids: this.selected_items
                    }).then(() => {
                        this.selected_items = [];
                        this.loading = false;
                    });
                }
            },
            selectCategory (category) {
                this.category = category;
            },
            createCategory () {
                if(this.new_category != '') {
                    this.loading = true;
                    this.create({
                        api: ADMIN_DOMAIN + '/module/post-category/create/' + this.website_id,
                        value: {name: this.new_category}
                    }).then(() => {
                        this.loading = false;
                        this.loadCategory();
                    });
                }
            },
            updateCategory(){
                if(this.category.name != '') {
                    this.loading = true;
                    this.update({
                        api: ADMIN_DOMAIN + '/module/post-category/update/' + this.category.id + '/' + this.website_id,
                        value: {name: this.category.name}
                    }).then(() => {
                        this.loading = false;
                    });
                }
            },
            deleteCategory(){
                this.loading = true;
                this.destroy({
                    api: ADMIN_DOMAIN + '/module/post-category/delete/' + this.website_id,
                    ids: [this.category.id]
                }).then(() => {
                    this.loading = false;
                    this.loadCategory();
                });
            },
            loadCategory(){
                this.loading = true;
                this.read({api: ADMIN_DOMAIN + '/module/post-category/list-by-name/' + this.website_id}).then((response) => {
                    this.categories = response.data;
                    this.loading = false;
                })
            }
        },
        created () {
            this.loadCategory();
        },
        mounted () {
            let o = this;
            $(".search-item").submit(function (e) {
                e.preventDefault();
                o.search();
            });
        }
    }
</script>
